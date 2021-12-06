<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Http\DTOs\CampaignBannerDto;
use App\Models\CampaignBanner;

/**
 * Repository class that abstract direct data access
 */
class CampaignBannerRepository implements CampaignBannerRepositoryInterface
{

    /**      
     * @var CampaignBanner      
     */
    protected $model;

    public function __construct(CampaignBanner $model)
    {
        $this->model = $model;
    }

    /**
     * Get banner info by banner ids
     *
     * @param array $banner_ids integer array
     * @param bool $convertPathToUrl indicate whether the banner path should be converted to storage url suitable for the browser
     * @return array array of CampaignBannerDto
     */
    public function getBannersById($banner_ids, $convertPathToUrl)
    {
        $banners =  $this->model->findMany($banner_ids);
        $banner_dtos = array();
        foreach ($banners as $banner) {
            $banner_dtos[] = (array)new CampaignBannerDto($banner, $convertPathToUrl);
        }
        return $banner_dtos;
    }
}
