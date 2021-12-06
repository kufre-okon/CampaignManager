<?php

use App\Http\Controllers\Api\CampaignController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/campaign/list', [CampaignController::class, 'list']);
Route::post('/campaign/create', [CampaignController::class, 'store']);
Route::put('/campaign/update/{id}', [CampaignController::class, 'update']);
