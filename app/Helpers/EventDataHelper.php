<?php

namespace App\Helpers;

use App\Models\Conference;
use App\Models\Order;
use App\Models\SettingEvent;
use App\Models\User;

class EventDataHelper
{
    /**
     * Get the price based on type and user ID.
     * @return bool The price.
     */
    public static function earlyBirdStatus(int $id): bool
    {
        $earlyBirdSetting = Conference::where('id', $id)->value('early_bird_active');
        return (bool)$earlyBirdSetting;
    }

    /**
     * Get the price based on type and user ID.
     *
     * @param string $type The type ('delegate' or 'spouse').
     * @param int $userId The user ID.
     * @return float The price.
     */
    public static function getPrice(string $type, int $userId, int $conferenceId): float
    {
        $earlyBird = self::earlyBirdStatus($conferenceId);

        $company = User::find($userId);
        $conference = Conference::find($conferenceId);
        $companyMembershipType = $company->active_member; // true is a member, false no non-member

        if ($type === 'delegate') {
            if ($companyMembershipType) {
                // Handle delegate price for a member $conference->value('value') event_earlybird_price_delegate   event_price_delegate
                $price = $earlyBird ? (float)$conference->value('eb_member_delegate_price') : (float)$conference->value('member_delegate_price');
            } else {
                // Handle delegate price for a non-member $conference->value('event_earlybird_price_delegate')  event_earlybird_non_member_price_delegate  event_non_member_price_delegate
                $price = $earlyBird ? (float)$conference->value('eb_non_member_delegate_price') : (float)$conference->value('non_member_delegate_price');
            }
        } else if ($type === 'spouse') {
            if ($companyMembershipType) {
                // Handle spouse price for a member
                $price = $earlyBird ? (float)$conference->value('eb_member_spouse_price') : (float)$conference->value('member_spouse_price');
            } else {
                // Handle spouse price for a non-member
                $price = $earlyBird ? (float)$conference->value('eb_non_member_spouse_price') : (float)$conference->value('non_member_spouse_price');
            }
        } else {
            $price = 0;
        }

        return $price;
    }


    /**
     * Get the price based on type and user ID.
     * @param float $totalPrice
     * @param float $discountValue
     * @param string $discountType
     * @return float The price.
     */
    public static function applyDiscount(float $totalPrice, float $discountValue, string $discountType): float
    {
        if ($discountType === 'percentage') {
            $discountedPrice = $totalPrice * ($discountValue / 100);
        } else {
            $discountedPrice = $discountValue;
        }
        return max(0, $discountedPrice); // Ensure the price doesn't go below zero
    }

    /**
     * @param int $id
     * @return void
     */
    public static function sumOrderTotal(int $id): void
    {
        $order = Order::find($id);
        if ($order->status == "approved_online_payment" || $order->status == "approved_bank_transfer") {
          return  ;
        } else {
            $userCountApprovedOrders = User::ordersCount('approved')->find($order->user_id)->orders_count;
            $delegateFee = self::getPrice('delegate', $order->user_id, $order->conference_id);
            $spouseFee = self::getPrice('spouse', $order->user_id, $order->conference_id);
            $earlyBird = self::earlyBirdStatus($order->conference_id);
            $totalDelegatesCount = $order->delegates()->count();
            $totalSpousesCount = $order->spouses()->count();


            // Calculate the package price
            if ($order->package && $totalDelegatesCount === 0) {
                $packagePrice = $earlyBird ? $order->package->earlybird_price : $order->package->price;
            } else if ($order->package && $totalDelegatesCount > 0) {
                $packageFreeDelegatesCount = $order->package->delegate_count;
                if ($totalDelegatesCount < $packageFreeDelegatesCount) {
                    $packagePrice = $earlyBird ? $order->package->earlybird_price : $order->package->price;
                } else {
                    $originalPackagePrice = $earlyBird ? $order->package->earlybird_price : $order->package->price;
                    $extraDelegates = $totalDelegatesCount - $packageFreeDelegatesCount;
                    $extraDelegatesFees = $extraDelegates * $delegateFee;
                    $packagePrice = $originalPackagePrice + $extraDelegatesFees;
                }
            } else {
                $packagePrice = 0;
            }
            $order->update(['total_price_package' => $packagePrice]);

            // ------- Delegates -------
            if ($totalDelegatesCount > 0) {
                $totalDelegatesPrice = $totalDelegatesCount * $delegateFee;
            } else {
                $totalDelegatesPrice = 0;
            }
            $order->update(['total_price_delegate' => $totalDelegatesPrice]);

            // ------- Spouses -------
            if ($totalSpousesCount > 0) {
                $totalSpousesPrice = $totalSpousesCount * $spouseFee;
            } else {
                $totalSpousesPrice = 0;
            }
            $order->update(['total_price_spouse' => $totalSpousesPrice]);

            // ------- Sponsorship Items -------
            $totalSponsorshipItemsPrice = $earlyBird ? $order->sponsorshipItems()->pluck('earlybird_price')->sum() : $order->sponsorshipItems()->pluck('price')->sum();

            $sponsorshipItems = $order->sponsorshipItems()->get();
            foreach ($sponsorshipItems as $sponsorshipItem) {

                if (!$earlyBird) {
                    $priceUsed = $sponsorshipItem->price;
                }else{
                    $priceUsed = $sponsorshipItem->earlybird_price;
                }
                $order->sponsorshipItems()->updateExistingPivot($sponsorshipItem->id, ['price_sponsorship_item' => $priceUsed]);
            }

            $order->update(['total_price_sponsorship_items' => $totalSponsorshipItemsPrice]);

            // ------- Rooms -------
            $orderRooms = $order->rooms;
            $totalRoomsPrice = 0;

            foreach ($orderRooms as $room) {

                $pivot = $room->pivot; // Access the pivot data

                $startDate = $pivot->start_date;
                $endDate = $pivot->end_date;

                // Calculate the number of nights
                $numberOfNights = strtotime($endDate) - strtotime($startDate);
                $numberOfNights = round($numberOfNights / (60 * 60 * 24));
                // Get the room price
                $pricePerNight = $room->price;

                // Calculate the total price for the room based on the number of nights
                $totalPrice = $numberOfNights * $pricePerNight;

                // Update the total price in the pivot table
                $pivot->update(['total_price' => $totalPrice]);

                // Add the total room price to the order's total rooms price
                $totalRoomsPrice += $totalPrice;
            }
            $order->update(['total_price_rooms' => $totalRoomsPrice]);

            // ------- Define Total Order without Discount -------
            if ($order->package) {
                $totalAmount = $packagePrice + $totalSpousesPrice + $totalRoomsPrice + $totalSponsorshipItemsPrice;
            } else {
                $totalAmount = $packagePrice + $totalDelegatesPrice + $totalSpousesPrice + $totalRoomsPrice + $totalSponsorshipItemsPrice;
            }





            if ($totalAmount === 0 && $userCountApprovedOrders === 0) {
                $order->update(['total' => $delegateFee]);
            } else if ($totalAmount > 0 && $totalDelegatesCount === 0 && $userCountApprovedOrders === 0) {
                if ($order->package) {
                    $order->update(['total' => $totalAmount]);
                } else {
                    $newTotal = $totalAmount + $delegateFee;
                    $order->update(['total' => $newTotal]);
                }
            } else if ($totalAmount === 0 && $userCountApprovedOrders >= 1) {
                $order->update(['total' => 0]);
            } else {
                $order->update(['total' => $totalAmount]);
            }

            // ------- Define final order amount value after discount -------
            if ($order->package) {
                $originalPackagePrice = $earlyBird ? $order->package->earlybird_price : $order->package->price;
                $packageFreeDelegatesCount = $order->package->delegate_count;
                if ($totalDelegatesCount <= $packageFreeDelegatesCount) {
                    $amountPackagePrice = $originalPackagePrice;
                } else {
                    $extraDelegates = $totalDelegatesCount - $packageFreeDelegatesCount;
                    $extraDelegatesFees = $extraDelegates * $delegateFee;
                    $amountPackagePrice = $originalPackagePrice + $extraDelegatesFees;
                }
            } else {
                $amountPackagePrice = $totalDelegatesCount * $delegateFee;
            }

            if ($order->coupon) {
                $coupon = $order->coupon;
                $couponType = $coupon->coupon_type;
                $discountValue = (float)$coupon->discount_value;
                $discountType = $coupon->discount_type;

                if ($couponType === 'delegate') {
                    if ($order->package) {
                        $packageFreeDelegatesCount = $order->package->delegate_count;
                        if ($totalDelegatesCount > $packageFreeDelegatesCount) {
                            $extraDelegates = $totalDelegatesCount - $packageFreeDelegatesCount;
                            $extraDelegatesFees = $extraDelegates * $delegateFee;
                            $discountAmount = EventDataHelper::applyDiscount($extraDelegatesFees, $discountValue, $discountType);
                        } else {
                            $discountAmount = 0;
                        }
                    } else {
                        $discountAmount = EventDataHelper::applyDiscount($totalDelegatesPrice, $discountValue, $discountType);
                    }
                } else if ($couponType === 'spouse') {
                    $discountAmount = EventDataHelper::applyDiscount($totalSpousesPrice, $discountValue, $discountType);
                } else if ($couponType === 'delegate_spouse') {
                    if ($order->package) {
                        $packageFreeDelegatesCount = $order->package->delegate_count;
                        if ($totalDelegatesCount <= $packageFreeDelegatesCount) {
                            $discountAmount = EventDataHelper::applyDiscount($totalSpousesPrice, $discountValue, $discountType);
                        } else {
                            $extraDelegates = $totalDelegatesCount - $packageFreeDelegatesCount;
                            $extraDelegatesFees = $extraDelegates * $delegateFee;
                            $totalDelegatesSpousePriceAfterDiscount = $extraDelegatesFees + $totalSpousesPrice;
                            $discountAmount = EventDataHelper::applyDiscount($totalDelegatesSpousePriceAfterDiscount, $discountValue, $discountType);
                        }
                    } else {
                        $totalDelegatesSpousePriceAfterDiscount = $totalDelegatesPrice + $totalSpousesPrice;
                        $discountAmount = EventDataHelper::applyDiscount($totalDelegatesSpousePriceAfterDiscount, $discountValue, $discountType);
                    }
                } else if ($couponType === 'sponsorship_item') {
                    $discountAmount = EventDataHelper::applyDiscount($totalSponsorshipItemsPrice, $discountValue, $discountType);
                } else if ($couponType === 'all') {
                    $discountAmount = EventDataHelper::applyDiscount($totalAmount, $discountValue, $discountType);
                } else {
                    $discountAmount = 0;
                }
                $totalAfterDiscount = $totalAmount - $discountAmount;
                $order->update(['amount' => $totalAfterDiscount]);
            } else {
                if ($order->package) {
                    $totalAfterDiscount = $amountPackagePrice + $totalSpousesPrice + $totalRoomsPrice + $totalSponsorshipItemsPrice;
                    $order->update(['amount' => $totalAfterDiscount]);
                } else {
                    if ($totalAmount === 0 && $userCountApprovedOrders === 0) {
                        $order->update(['amount' => $delegateFee]);
                    } else if ($totalAmount > 0 && $totalDelegatesCount === 0 && $userCountApprovedOrders === 0) {
                        if ($order->package) {
                            $order->update(['amount' => $totalAmount]);
                        } else {
                            $newTotal = $totalAmount + $delegateFee;
                            $order->update(['amount' => $newTotal]);
                        }
                    } else if ($totalAmount === 0 && $userCountApprovedOrders >= 1) {
                        $order->update(['amount' => 0]);
                    } else {
                        $order->update(['amount' => $totalAmount]);
                    }
                }
            }
        }
    }
}
