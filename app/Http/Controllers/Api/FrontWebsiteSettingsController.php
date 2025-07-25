<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Models\FrontWebsiteSettings;
use App\Http\Requests\Api\FrontWebsiteSettings\IndexRequest;
use App\Http\Requests\Api\FrontWebsiteSettings\UpdateRequest;

class FrontWebsiteSettingsController extends ApiBaseController
{
	protected $model = FrontWebsiteSettings::class;

	protected $indexRequest = IndexRequest::class;
	protected $updateRequest = UpdateRequest::class;

	public function modifyIndex($query)
	{
		return $query->first();
	}

	public function storing(FrontWebsiteSettings $frontWebsiteSettings)
	{
		$request = request();
		$frontWebsiteSettings->featured_categories = $this->getIdArrayFromHash($request->featured_categories);
		$frontWebsiteSettings->featured_products = $this->getIdArrayFromHash($request->featured_products);

		return $frontWebsiteSettings;
	}

	public function updating(FrontWebsiteSettings $frontWebsiteSettings)
	{
		$request = request();
		$frontWebsiteSettings->featured_categories = $this->getIdArrayFromHash($request->featured_categories);
		$frontWebsiteSettings->featured_products = $this->getIdArrayFromHash($request->featured_products);

		return $frontWebsiteSettings;
	}
}
