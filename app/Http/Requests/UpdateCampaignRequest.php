<?php

namespace App\Http\Requests;


class UpdateCampaignRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'present|required|string|max:255',
            'date_from' => 'present|required|date_format:Y-m-d|before_or_equal:date_to',
            'date_to' => 'present|required|date_format:Y-m-d',
            'total_budget' => 'present|required|numeric',
            'daily_budget' => 'present|required|numeric',
            'banner_files' => 'nullable|array|min:1',
            'banner_files.*' => 'image|mimes:jpeg,png,jpg,gif',
            'deleted_files' => 'nullable|array|min:1',
            'deleted_files.*' => 'numeric',
        ];
    }

    /**
     * Custom messages for validation
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'Name is required',
            'date_from.required' => 'Date from is required',
            'date_to.required' => 'Date to is required',
            'total_budget.required' => 'Total budget is required',
            'daily_budget.required' => 'Daily budget is required',
            'banner_files.array' => 'The banner_files must be array of images',
            'banner_files.min' => 'The banner_files must have at least 1 items',
            'banner_files.*.image' => 'Banner file must be an image',
            'banner_files.*.mimes' => 'Banner file must be files of type:jpeg,png,jpg,gif',
            'deleted_files.array' => 'The deleted_files, if present must be array of integers',
            'deleted_files.*.numeric' => 'The deleted_files, if present must be array of integers'
        ];
    }
}
