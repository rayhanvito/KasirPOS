<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\PaymentMode\IndexRequest;
use App\Http\Requests\Api\PaymentMode\StoreRequest;
use App\Http\Requests\Api\PaymentMode\UpdateRequest;
use App\Http\Requests\Api\PaymentMode\DeleteRequest;
use App\Models\Payment;
use App\Models\PaymentMode;
use Examyou\RestAPI\Exceptions\ApiException;

class PaymentModeController extends ApiBaseController
{
    protected $model = PaymentMode::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;


    public function destroying($paymentMode)
    {
        // Can not delete payment mode if it assigned to any payment
        $paymentModeCount = Payment::where('payment_mode_id', $paymentMode->id)->count();
        if ($paymentModeCount > 0) {
            throw new ApiException("Payment mode assigned to payment. Delete that first and try again.");
        }

        return $paymentMode;
    }
}
