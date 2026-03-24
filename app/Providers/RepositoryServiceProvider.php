<?php

namespace App\Providers;

use App\Interfaces\AdminRepositoryInterface;
use App\Interfaces\ContactUsRepositoryInterface;
use App\Interfaces\MemberRepositoryInterface;
use App\Interfaces\SliderRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\AdminRepository;
use App\Repositories\ContactUsRepository;
use App\Repositories\BranchRepository;
use App\Repositories\SliderRepository;
use App\Repositories\UserRepository;
use App\Repositories\MaintenanceRepository;
use Illuminate\Support\ServiceProvider;
use App\Interfaces\BranchRepositoryInterface;
use App\Interfaces\MaintenanceRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\UsedMachineRepositoryInterface;
use App\Repositories\MemberRepository;
use App\Repositories\ProductRepository;
use App\Repositories\UsedMachineRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(AdminRepositoryInterface::class, AdminRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(ContactUsRepositoryInterface::class, ContactUsRepository::class);
        $this->app->bind(SliderRepositoryInterface::class, SliderRepository::class);
        $this->app->bind(BranchRepositoryInterface::class, BranchRepository::class);
        $this->app->bind(MaintenanceRepositoryInterface::class, MaintenanceRepository::class);
        $this->app->bind(UsedMachineRepositoryInterface::class, UsedMachineRepository::class);
        $this->app->bind(MemberRepositoryInterface::class, MemberRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
    }
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}


