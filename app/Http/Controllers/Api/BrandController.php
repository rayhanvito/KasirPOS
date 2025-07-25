<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Brand\IndexRequest;
use App\Http\Requests\Api\Brand\StoreRequest;
use App\Http\Requests\Api\Brand\UpdateRequest;
use App\Http\Requests\Api\Brand\DeleteRequest;
use App\Http\Requests\Api\Brand\ImportRequest;
use App\Imports\BrandImport;
use App\Models\Brand;
use Examyou\RestAPI\ApiResponse;
use Maatwebsite\Excel\Facades\Excel;

class BrandController extends ApiBaseController
{
	protected $model = Brand::class;

	protected $indexRequest = IndexRequest::class;
	protected $storeRequest = StoreRequest::class;
	protected $updateRequest = UpdateRequest::class;
	protected $deleteRequest = DeleteRequest::class;

	public function import(ImportRequest $request)
	{
		if ($request->hasFile('file')) {
			Excel::import(new BrandImport, request()->file('file'));
		}

		return ApiResponse::make('Imported Successfully', []);
	}
}
