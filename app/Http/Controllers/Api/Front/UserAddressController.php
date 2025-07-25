<?php

namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\ApiBaseController;
use App\Models\UserAddress;
use App\Http\Requests\Api\Front\UserAddress\IndexRequest;
use App\Http\Requests\Api\Front\UserAddress\StoreRequest;
use App\Http\Requests\Api\Front\UserAddress\UpdateRequest;
use App\Http\Requests\Api\Front\UserAddress\DeleteRequest;


class UserAddressController extends ApiBaseController
{
	protected $model = UserAddress::class;

	protected $indexRequest = IndexRequest::class;
	protected $storeRequest = StoreRequest::class;
	protected $updateRequest = UpdateRequest::class;
	protected $deleteRequest = DeleteRequest::class;

	protected function modifyIndex($query)
	{
		$user = auth('api_front')->user();

		$query = $query->where('user_id', $user->id);

		return $query;
	}

	public function storing(UserAddress $userAddress)
	{
		$user = auth('api_front')->user();

		$userAddress->user_id = $user->id;

		return $userAddress;
	}
}
