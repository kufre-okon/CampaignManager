<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'date_from',
        'date_to',
        'total_budget',
        'daily_budget'
    ];

    protected $casts = [
        'date_from' => 'date:Y-m-d',
        'date_to' => 'date:Y-m-d'
    ];

    protected $hidden = [
        'updated_at'
    ];

    /**
     * Get the banners for the campaign
     */
    public function banners()
    {
        return $this->hasMany(CampaignBanner::class);
    }

}
