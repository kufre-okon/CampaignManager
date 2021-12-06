<?php

declare(strict_types=1);

namespace App\Http\DTOs;

use App\Models\CampaignBanner;
use Illuminate\Support\Facades\Storage;

use function PHPUnit\Framework\isNull;

class CampaignBannerDto
{

    public $id;
    public $campaign_id;
    public $image_url;

    public function __construct(CampaignBanner $campaignBanner, $convertPathToUrl = false)
    {
        $this->id = $campaignBanner->id;
        $this->image_url = $convertPathToUrl ?  Storage::url($campaignBanner->image_url) : $campaignBanner->image_url;
        $this->campaign_id = $campaignBanner->campaign_id;
    }
}
