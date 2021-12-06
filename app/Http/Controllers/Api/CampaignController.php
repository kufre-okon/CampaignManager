<?php

namespace App\Http\Controllers\Api;

use App\Events\CampaignModified;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCampaignRequest;
use App\Http\Requests\UpdateCampaignRequest;
use App\Services\CampaignServiceInterface;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CampaignController extends Controller
{

    protected $campaignService;

    public function __construct(CampaignServiceInterface $campaignService)
    {
        $this->campaignService = $campaignService;
    }

    /**
     * Get a listing of the campaigns.
     *
     * @return aarray
     */
    public function list()
    {
        try {

            $data = Cache::rememberForever('campaigns', function () {
                return  $this->campaignService->getAll();
            });

            return $this->sendSuccess('Campaigns loaded successfully', $data);
        } catch (Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            // return custom error message to avoid leaking sensitive information to end user
            return $this->sendError('Unable to load campaigns, if error persist, please contact support');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCampaignRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCampaignRequest $request)
    {
        try {
            $data = $request->validated();

            $files = $data['banner_files'];
            $campaign =  $this->campaignService->create($data, $files);

            event(new CampaignModified());

            return $this->sendSuccess('Campaign created successfully', $campaign);
        } catch (ValidationException $e) {
            return $this->sendWithCode(false, $e->getMessage(), 422, $e->errors());
        } catch (Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            // return custom error message to avoid leaking sensitive information to end user
            return $this->sendError('Unable to create campaign, if error persist, please contact support');
        }
    }

    /**
     * Update the specified campaign.
     *
     * @param  \App\Http\Requests\UpdateCampaignRequest  $request
     * @param  int $id id of the campaign to be updated
     * @return \Illuminate\Http\Response
     */
    public function update($id, UpdateCampaignRequest $request)
    {
        try {
            $data = $request->validated();

            // since `banner_files` and `deleted_files` are nullable, hence the need to check for existence 
            $new_files  = array_key_exists('banner_files', $data) ? $data['banner_files'] : array();
            $delete_banners = array_key_exists('deleted_files', $data) ? $data['deleted_files'] : array();

            $campaign =  $this->campaignService->update($id, $data, $new_files, $delete_banners);

            event(new CampaignModified());

            return $this->sendSuccess('Campaign updated successfully', $campaign);
        } catch (ValidationException $e) {
            return $this->sendWithCode(false, $e->getMessage(), 422, $e->errors());
        } catch (Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            // return custom error message to avoid leaking sensitive information to end user
            return $this->sendError('Unable to update campaign, if error persist, please contact support');
        }
    }
}
