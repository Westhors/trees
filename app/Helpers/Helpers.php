<?php

use App\Models\Media;
use App\Models\Network;
use App\Models\Setting;
use App\Models\Slug\SlugOptions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Stripe\Tax\Settings;

if (!function_exists('config_path')) {
    /**
     * Get the configuration path.
     *
     * @param string $path
     * @return string
     */
    function config_path(string $path = ''): string
    {
        return app()->basePath() . '/config' . ($path ? '/' . $path : $path);
    }
}



if (!function_exists('checkRole')) {

    function checkRole($permission)
    {
        return auth()->user()->can($permission);
    }
}


if (!function_exists('sumDiscount')) {
    function sumDiscount($price, $discount_type, $discount_value)
    {
        if ($discount_type == 'percentage') {
            return $price - ($price * $discount_value / 100);
        } else {
            return $price - $discount_value;
        }
    }
}

function customSyncItem($data, $array_key): array
{
    $customData = [];
    foreach ($data as $value) {
        $customData[$value[$array_key]] = $value;
    }
    return $customData;
}

if (!function_exists('isJson')) {

    /**
     * check if sting is Json.
     *
     * @param $string
     * @return Boolean
     */
    function isJson($string): bool
    {
        if (is_array($string)) {
            return false;
        }
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}

if (!function_exists('getDatabase')) {

    function getDatabase($model)
    {
        if ($model->getConnectionName() == 'tenant') {
            return session()->get('server_database');
        } else {
            return null;
        }
    }
}



if (!function_exists('setting')) {

    function setting($key, $default = null)
    {
        if (is_null($key)) {
            return new Setting();
        }

        if (is_array($key)) {
            return Setting::set($key[0], $key[1]);
        }

        $value = Setting::get($key);

        return is_null($value) ? value($default) : $value;
    }
}


if (!function_exists('is_true')) {
    /**
     * Get the application copyright.
     *
     * @param $val
     * @param bool $return_null
     * @return bool|string
     */
    function is_true($val, bool $return_null = false): bool|string
    {
        $boolVal = (is_string($val) ? filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : (bool) $val);
        return ($boolVal === null && !$return_null ? false : $boolVal);
    }
}


if (!function_exists('getAllSettingsWithValues')) {
    /**
     * Get the application copyright.
     *
     * @return array|string
     */
    function getAllSettingsWithValues(): array|string
    {
        $newArray = [];
        $allSettings = Setting::getDefinedSetting();
        foreach ($allSettings as $key_level_1 => $settings_level_1) {

            $newArray[$key_level_1] = getSettingsValues($settings_level_1);
        }

        return $newArray;
    }
}


if (!function_exists('columnFilters')) {


    /**
     * Filter the query by a given code.
     *
     * @param $type
     * @param $old_value
     * @param int|string $value
     * @return bool
     */
    function columnFilters($type, $old_value, int|string $value): bool
    {
        if ($value && $old_value) {
            switch ($type) {
                case 'like':
                    return (Str::contains(Str::lower($old_value), Str::lower($value)));
                    break;
                case '=':
                    return (Str::lower($old_value) == Str::lower($value));
                    break;
                case '!=':
                    return (Str::lower($old_value) != Str::lower($value));
                    break;
                case '<':
                    return (Str::lower($old_value) < Str::lower($value));
                    break;
                case '<=':
                    return (Str::lower($old_value) <= Str::lower($value));
                    break;
                case '>':
                    return (Str::lower($old_value) > Str::lower($value));
                    break;
                case '>=':
                    return (Str::lower($old_value) >= Str::lower($value));
                    break;
                default:
                    return true;
                    break;
            }
        }

        return false;
    }
}


if (!function_exists('getSettingsValues')) {
    /**
     * Get the application copyright.
     *
     * @return array
     */
    function getSettingsValues($setting_fields): array
    {


        $array = [];
        if (isset($setting_fields['elements'])) {
            foreach ($setting_fields['elements'] as $key => $element) {
                if ($element['type'] == 'img') {
                    $value = setting::get($element['name'], $element['value']);
                    if ($value) {
                        if (isset($value['id'])) {
                            $full_url = Media::find($value['id']);
                            if (!$full_url) {
                                $array[$element['name']] = "";
                            } else {
                                $array[$element['name']] = $full_url->full_url;
                            }
                        }
                    }
                } else if ($element['type'] == 'list') {
                    $array[$element['name']] = getSettingsValues($element);
                } else {
                    $array[$element['name']] = Setting::get($element['name'], $element['value']);
                }
            }
        }

        if (isset($setting_fields['section'])) {
            foreach ($setting_fields['section'] as $key => $element) {
                $array[$key] = getSettingsValues($element);
            }
        }

        if (isset($setting_fields['inputs'])) {
            $setting_fields['value'] = setting::get($setting_fields['name']);

            foreach ($setting_fields['inputs'] as $input_key => $input) {
                foreach ($setting_fields['value'] as $key => $value) {

                    if ($input['type'] == 'img') {

                        if ($value[$input['name']] != 'null') {
                            $media = Media::find($value[$input['name']]['id']);
                            $array[$key][$input['name']] = $media ? $media->full_url : null;
                        } else {
                            $array[$key][$input['name']] = null;
                        }
                    } else {
                        $array[$key][$input['name']] = $value[$input['name']] != 'null' ? $value[$input['name']] : null;
                    }
                }
            }
        }

        return $array;
    }
}


if (!function_exists('get_domain')) {
    /**
     * Get the application copyright.
     *
     * @param $host
     * @return string
     */
    function get_domain($host): string
    {
        $domain = explode('.', $host);
        unset($domain[0]);
        return implode(".", $domain);
    }
}

if (!function_exists('get_sub_domain')) {
    /**
     * Get the application copyright.
     *
     * @param $host
     * @return string
     */
    function get_sub_domain($host): string
    {
        $domain = explode('.', $host);
        return $domain[0];
    }
}

if (!function_exists('get_table_settings')) {
    function get_table_settings($table, $attr, $default = false)
    {
        $setting = Setting::get($table);
        $user_id = Auth()->user()->id;
        return isset($setting['table_attribute'][$user_id][$attr]) ? filter_var($setting['table_attribute'][$user_id][$attr], FILTER_VALIDATE_BOOLEAN) : $default;
    }
}

if (!function_exists('app_copyright')) {
    /**
     * Get the application copyright.
     *
     * @return string
     */
    function app_copyright(): string
    {
        return Settings::locale()->get('copyright');
    }
}
if (!function_exists('app_name')) {
    /**
     * Get the application name.
     *
     * @return string
     */
    function app_name(): string
    {
        return Settings::locale()
            ->get('name', config('app.name', 'Laravel'))
            ?: config('app.name', 'Laravel');
    }
}


if (!function_exists('routex')) {


    function routex($route, $params = null): string
    {
        $tenant = getTenantName();

        return route('tenant.' . $route, [$tenant, $params]);
    }
}


if (!function_exists('getTenantName')) {

    function getTenantName(): ?string
    {

        $url = request()->getHost();
        $urlArray = explode('.', $url);

        if (sizeof($urlArray) === 3 && in_array($urlArray[1], ['ap', 'dashboard'])) {

            $tenantArray = explode('//', $urlArray[0]);

            $tenant = $tenantArray[1];

            return $tenant === 'admin' ? null : $tenant;
        }

        return null;
    }
}

if (!function_exists('app_logo')) {
    /**
     * Get the application logo url.
     *
     * @return string
     */
    function app_logo(): string
    {
        if (($model = Settings::instance('logo')) && $file = $model->getFirstMediaUrl('logo')) {
            return $file;
        }

        return 'https://ui-avatars.com/api/?name=' . rawurldecode(config('app.name')) . '&bold=true';
    }
}


if (!function_exists('app_logo_light')) {
    /**
     * Get the application logo url.
     *
     * @return string
     */
    function app_logo_light(): string
    {
        if (($model = Settings::instance('logo_light')) && $file = $model->getFirstMediaUrl('logo_light')) {
            return $file;
        }

        return 'https://ui-avatars.com/api/?name=' . rawurldecode(config('app.name')) . '&bold=true';
    }
}



if (!function_exists('array_unset_by_value')) {
    /**
     * unset value from array .
     *
     * @param $array
     * @param $value
     * @return void
     */
    function array_unset_by_value($array, $value): void
    {
        if (($key = array_search($value, $array)) !== false) {
            unset($array[$key]);
        }
    }
}



function checkColumnUsed($pschema_name, $ptable_name, $pcolumn_name, $pvalue, $extable = []): bool
{
    $table_list = DB::select('SELECT
        TABLE_NAME,COLUMN_NAME,CONSTRAINT_NAME, REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME
      FROM
        INFORMATION_SCHEMA.KEY_COLUMN_USAGE
      WHERE
        REFERENCED_TABLE_SCHEMA = ' . "'$pschema_name'" . ' AND
        REFERENCED_TABLE_NAME = ' . "'$ptable_name'" . ' AND
        REFERENCED_COLUMN_NAME = ' . "'$pcolumn_name'" . '');

    foreach ($table_list as $table) {
        $table_name = $table->TABLE_NAME;

        if (in_array($table_name, $extable)) {

            continue;
        }
        $column_name = $table->COLUMN_NAME;
        $ROWSCOUNT = DB::select('SELECT COUNT(*) as R_count FROM ' . $table_name . ' WHERE ' . $column_name . '=' . $pvalue);

        if ($ROWSCOUNT[0]->R_count > 0) {

            return true;
            break;
        }
    }
    return false;
}


if (!function_exists('generateRandomString')) {
    /**
     * Get the application logo url.
     *
     * @return string
     */

    function generateRandomString($length = 10): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

if (!function_exists('str_limit')) {
    /**
     * str limit of string
     *
     * @return string
     */

    function str_limit($string, $limit = 150, $end = '...')
    {
        return Str::limit($string, $limit, $end);
    }
}


if (!function_exists('app_favicon')) {
    /**
     * Get the application favicon url.
     *
     * @return string
     */
    function app_favicon(): string
    {
        if (($model = Settings::instance('favicon')) && $file = $model->getFirstMediaUrl('favicon')) {
            return $file;
        }

        return '/favicon.ico';
    }
}

if (!function_exists('meme_type_to_ex')) {
    /**
     * Format numbers to nearest thousands such as
     * Kilos, Millions, Billions, and Trillions with comma.
     *
     * @param $type
     * @return string|null
     */
    function meme_type_to_ex($type): ?string
    {
        $meme_types = [
            'application/bmp' => 'bmp',
            'application/cdr' => 'cdr',
            'application/coreldraw' => 'cdr',
            'application/excel' => 'xl',
            'application/gpg-keys' => 'gpg',
            'application/java-archive' => 'jar',
            'application/json' => 'json',
            'application/mac-binary' => 'bin',
            'application/mac-binhex' => 'hqx',
            'application/mac-binhex40' => 'hqx',
            'application/mac-compactpro' => 'cpt',
            'application/macbinary' => 'bin',
            'application/msexcel' => 'xls',
            'application/msword' => 'doc',
            'application/octet-stream' => 'pdf',
            'application/oda' => 'oda',
            'application/ogg' => 'ogg',
            'application/pdf' => 'pdf',
            'application/pgp' => 'pgp',
            'application/php' => 'php',
            'application/pkcs-crl' => 'crl',
            'application/pkcs10' => 'p10',
            'application/pkcs7-mime' => 'p7c',
            'application/pkcs7-signature' => 'p7s',
            'application/pkix-cert' => 'crt',
            'application/pkix-crl' => 'crl',
            'application/postscript' => 'ai',
            'application/powerpoint' => 'ppt',
            'application/rar' => 'rar',
            'application/s-compressed' => 'zip',
            'application/smil' => 'smil',
            'application/videolan' => 'vlc',
            'application/vnd.google-earth.kml+xml' => 'kml',
            'application/vnd.google-earth.kmz' => 'kmz',
            'application/vnd.mif' => 'mif',
            'application/vnd.mpegurl' => 'm4u',
            'application/vnd.ms-excel' => 'xlsx',
            'application/vnd.ms-office' => 'ppt',
            'application/vnd.ms-powerpoint' => 'ppt',
            'application/vnd.msexcel' => 'csv',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/wbxml' => 'wbxml',
            'application/wmlc' => 'wmlc',
            'application/x-binary' => 'bin',
            'application/x-binhex40' => 'hqx',
            'application/x-bmp' => 'bmp',
            'application/x-cdr' => 'cdr',
            'application/x-compress' => 'z',
            'application/x-compressed' => '7zip',
            'application/x-coreldraw' => 'cdr',
            'application/x-director' => 'dcr',
            'application/x-dos_ms_excel' => 'xls',
            'application/x-dvi' => 'dvi',
            'application/x-excel' => 'xls',
            'application/x-gtar' => 'gtar',
            'application/x-gzip' => 'gzip',
            'application/x-gzip-compressed' => 'tgz',
            'application/x-httpd-php' => 'php',
            'application/x-httpd-php-source' => 'php',
            'application/x-jar' => 'jar',
            'application/x-java-application' => 'jar',
            'application/x-javascript' => 'js',
            'application/x-mac-binhex40' => 'hqx',
            'application/x-macbinary' => 'bin',
            'application/x-ms-excel' => 'xls',
            'application/x-msdownload' => 'exe',
            'application/x-msexcel' => 'xls',
            'application/x-pem-file' => 'pem',
            'application/x-photoshop' => 'psd',
            'application/x-php' => 'php',
            'application/x-pkcs10' => 'p10',
            'application/x-pkcs12' => 'p12',
            'application/x-pkcs7' => 'rsa',
            'application/x-pkcs7-certreqresp' => 'p7r',
            'application/x-pkcs7-mime' => 'p7c',
            'application/x-pkcs7-signature' => 'p7a',
            'application/x-rar' => 'rar',
            'application/x-rar-compressed' => 'rar',
            'application/x-shockwave-flash' => 'swf',
            'application/x-stuffit' => 'sit',
            'application/x-tar' => 'tar',
            'application/x-troff-msvideo' => 'avi',
            'application/x-win-bitmap' => 'bmp',
            'application/x-x509-ca-cert' => 'crt',
            'application/x-x509-user-cert' => 'pem',
            'application/x-xls' => 'xls',
            'application/x-zip' => 'zip',
            'application/x-zip-compressed' => 'zip',
            'application/xhtml+xml' => 'xhtml',
            'application/xls' => 'xls',
            'application/xml' => 'xml',
            'application/xspf+xml' => 'xspf',
            'application/zip' => 'zip',
            'audio/ac3' => 'ac3',
            'audio/aiff' => 'aif',
            'audio/midi' => 'mid',
            'audio/mp3' => 'mp3',
            'audio/mp4' => 'm4a',
            'audio/mpeg' => 'mp3',
            'audio/mpeg3' => 'mp3',
            'audio/mpg' => 'mp3',
            'audio/ogg' => 'ogg',
            'audio/wav' => 'wav',
            'audio/wave' => 'wav',
            'audio/x-acc' => 'aac',
            'audio/x-aiff' => 'aif',
            'audio/x-au' => 'au',
            'audio/x-flac' => 'flac',
            'audio/x-m4a' => 'm4a',
            'audio/x-ms-wma' => 'wma',
            'audio/x-pn-realaudio' => 'ram',
            'audio/x-pn-realaudio-plugin' => 'rpm',
            'audio/x-realaudio' => 'ra',
            'audio/x-wav' => 'wav',
            'font/otf' => 'otf',
            'font/ttf' => 'ttf',
            'font/woff' => 'woff',
            'font/woff2' => 'woff2',
            'image/bmp' => 'bmp',
            'image/cdr' => 'cdr',
            'image/gif' => 'gif',
            'image/jp2' => 'jp2',
            'image/jpeg' => 'jpeg',
            'image/jpm' => 'jp2',
            'image/jpx' => 'jp2',
            'image/ms-bmp' => 'bmp',
            'image/pjpeg' => 'jpeg',
            'image/png' => 'png',
            'image/svg+xml' => 'svg',
            'image/tiff' => 'tiff',
            'image/vnd.adobe.photoshop' => 'psd',
            'image/vnd.microsoft.icon' => 'ico',
            'image/webp' => 'webp',
            'image/x-bitmap' => 'bmp',
            'image/x-bmp' => 'bmp',
            'image/x-cdr' => 'cdr',
            'image/x-ico' => 'ico',
            'image/x-icon' => 'ico',
            'image/x-ms-bmp' => 'bmp',
            'image/x-png' => 'png',
            'image/x-win-bitmap' => 'bmp',
            'image/x-windows-bmp' => 'bmp',
            'image/x-xbitmap' => 'bmp',
            'message/rfc822' => 'eml',
            'multipart/x-zip' => 'zip',
            'text/calendar' => 'ics',
            'text/comma-separated-values' => 'csv',
            'text/css' => 'css',
            'text/html' => 'html',
            'text/json' => 'json',
            'text/php' => 'php',
            'text/plain' => 'txt',
            'text/richtext' => 'rtx',
            'text/rtf' => 'rtf',
            'text/srt' => 'srt',
            'text/vtt' => 'vtt',
            'text/x-comma-separated-values' => 'csv',
            'text/x-log' => 'log',
            'text/x-php' => 'php',
            'text/x-scriptzsh' => 'zsh',
            'text/x-vcard' => 'vcf',
            'text/xml' => 'xml',
            'text/xsl' => 'xsl',
            'video/3gp' => '3gp',
            'video/3gpp' => '3gp',
            'video/3gpp2' => '3g2',
            'video/avi' => 'avi',
            'video/mj2' => 'jp2',
            'video/mp4' => 'mp4',
            'video/mpeg' => 'mpeg',
            'video/msvideo' => 'avi',
            'video/ogg' => 'ogg',
            'video/quicktime' => 'mov',
            'video/vnd.rn-realvideo' => 'rv',
            'video/webm' => 'webm',
            'video/x-f4v' => 'f4v',
            'video/x-flv' => 'flv',
            'video/x-ms-asf' => 'wmv',
            'video/x-ms-wmv' => 'wmv',
            'video/x-msvideo' => 'avi',
            'video/x-sgi-movie' => 'movie',
            'zz-application/zz-winassoc-cdr' => 'cdr',
        ];


        return $meme_types[$type] ?? null;
    }
}


if (!function_exists('count_formatted')) {
    /**
     * Format numbers to nearest thousands such as
     * Kilos, Millions, Billions, and Trillions with comma.
     *
     * @param float|int $num
     * @return float|int|string
     */
    function count_formatted(float|int $num): float|int|string
    {
        if ($num >= 1000) {
            $x = round($num);
            $x_number_format = number_format($x);
            $x_array = explode(',', $x_number_format);
            $x_parts = ['K', 'M', 'B', 'T'];
            $x_count_parts = count($x_array) - 1;
            $x_display = $x_array[0] . ((int)$x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
            $x_display .= $x_parts[$x_count_parts - 1];

            return $x_display;
        }

        return $num;
    }
}

if (!function_exists('active_navbar')) {
    /**
     * str limit of string
     *
     * @param $route
     * @return string
     */

    function active_navbar($route): string
    {

        return Route::currentRouteName() == $route ? "active" : " ";
    }
}


if (!function_exists('IsNullOrEmptyString')) {
    /**
     * str limit of string
     *
     * @param $str
     * @return bool
     */

    function IsNullOrEmptyString($str): bool
    {
        return ($str === null || trim($str) === '' || $str === 'null');
    }
}
if (!function_exists('getIp')) {
    function getIp()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if (isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';

        return $ipaddress;
    }
}
if (!function_exists('getCurrentNetwork')) {
    function getCurrentNetwork($networks)
    {
        $networkId = request()->header('X-Network-ID');

        foreach ($networks as $network) {
            if ($network->pivot->network_id == $networkId) {
                return $network;
            }
        }

        return null;
    }
}

if (!function_exists('getPendingNetworks')) {
    function getPendingNetworks($networks): array
    {
        $pendingNetworks = [];
        foreach ($networks as $network) {
            if ($network->pivot->status == 'pending') {
                $pendingNetworks[] = $network;
            }
        }

        return $pendingNetworks;
    }
}

if (!function_exists('readingTime')) {
    function readingTime($text): float
    {
        $words = str_word_count(strip_tags($text));
        return ceil($words / 20);
    }
}


function customSync($data, $array_key): array
{
    $customData = [];
    foreach ($data as $value) {
        $customData[$value[$array_key]] = $value;
    }
    return $customData;
}

if (!function_exists('getNetworkSlug')) {
    function getNetworkSlug()
    {
        $networkId = request()->header('X-Network-ID');
        return Network::where('id', $networkId)->value('slug');
    }
}

if (!function_exists('getSlugOptions')) {
    function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }
}

if (!function_exists('getSlugOptionTitle')) {
    function getSlugOptionTitle(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }
}



function getAuthMiddleware($type)
{
    if ($type === 'admin') {
        return app()->environment('production') ? ['auth:admins'] : [];
    } elseif ($type === 'user') {
        return app()->environment('production') ? ['auth:sanctum'] : [];
    }
}

if (!function_exists('generateUUID')) {
    function generateUUID($orderId)
    {
        $date = now()->format('ymd');
        $paddedOrderId = str_pad($orderId, 3, '0', STR_PAD_LEFT);
        $randomDigits = str_pad(mt_rand(0, 99), 2, '0', STR_PAD_LEFT);
        return 'EV-' . $date . $paddedOrderId . $randomDigits;
    }
}
