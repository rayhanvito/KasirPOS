<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Unit\IndexRequest;
use App\Http\Requests\Api\Unit\StoreRequest;
use App\Http\Requests\Api\Unit\UpdateRequest;
use App\Http\Requests\Api\Unit\DeleteRequest;
use App\Models\Product;
use App\Models\Unit;
use Examyou\RestAPI\Exceptions\ApiException;

class UnitController extends ApiBaseController
{
	protected $model = Unit::class;

	protected $indexRequest = IndexRequest::class;
	protected $storeRequest = StoreRequest::class;
	protected $updateRequest = UpdateRequest::class;
	protected $deleteRequest = DeleteRequest::class;

    public function destroying(Unit $unit)
	{
        $isUnitExitsForProduct = Product::where('unit_id', $unit->id)->count();

		if ($isUnitExitsForProduct > 0) {
			throw new ApiException('Cannot delete. Unit assinged to some product.');
		}

		return $unit;
	}
}
