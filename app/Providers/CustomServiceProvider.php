<?php

namespace App\Providers;

use App\Repositories\CampaignBannerRepository;
use App\Repositories\CampaignBannerRepositoryInterface;
use App\Repositories\CampaignRepository;
use App\Repositories\CampaignRepositoryInterface;
use App\Services\CampaignService;
use App\Services\CampaignServiceInterface;
use Illuminate\Support\ServiceProvider;

class CustomServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(CampaignRepositoryInterface::class, CampaignRepository::class);
        $this->app->bind(CampaignBannerRepositoryInterface::class, CampaignBannerRepository::class);
        $this->app->bind(CampaignServiceInterface::class, CampaignService::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
