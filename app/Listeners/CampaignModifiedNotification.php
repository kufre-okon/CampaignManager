<?php

namespace App\Listeners;

use App\Events\CampaignModified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CampaignModifiedNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Event\CampaignModified  $event
     * @return void
     */
    public function handle(CampaignModified $event)
    {
        // invalid campaigns cache
        Cache::forget('campaigns');
    }
}
