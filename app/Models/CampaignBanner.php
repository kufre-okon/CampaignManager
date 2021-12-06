<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignBanner extends Model
{
    use HasFactory;


    protected $fillable = [
        'image_url'
    ];

   
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    /**
     * Get the campaign that has this banner
     */
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
