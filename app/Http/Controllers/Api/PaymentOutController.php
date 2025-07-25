<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Payments\IndexRequest;
use App\Http\Requests\Api\Payments\StoreRequest;
use App\Http\Requests\Api\Payments\UpdateRequest;
use App\Http\Requests\Api\Payments\DeleteRequest;
use App\Models\Payment;

use App\Traits\PaymentTraits;

class PaymentOutController extends ApiBaseController
{
	use PaymentTraits;

	protected $model = Payment::class;

	protected $indexRequest = IndexRequest::class;
	protected $storeRequest = StoreRequest::class;
	protected $updateRequest = UpdateRequest::class;
	protected $deleteRequest = DeleteRequest::class;

	public function __construct()
	{
		parent::__construct();

		$this->paymentType = "out";
	}
}
