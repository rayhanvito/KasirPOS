<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Payments\IndexRequest;
use App\Http\Requests\Api\Payments\StoreRequest;
use App\Http\Requests\Api\Payments\UpdateRequest;
use App\Http\Requests\Api\Payments\DeleteRequest;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Examyou\RestAPI\ApiResponse;
use Examyou\RestAPI\Exceptions\ApiException;

class PaymentController extends ApiBaseController
{
    protected $model = Payment::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    protected function modifyIndex($query)
    {
        $request = request();
        $user = user();

        // Dates Filters
        if ($request->has('dates') && $request->dates != "") {
            $dates = explode(',', $request->dates);
            $startDate = $dates[0];
            $endDate = $dates[1];

            $query = $query->whereRaw('payments.date >= ?', [$startDate])
                ->whereRaw('payments.date <= ?', [$endDate]);
        }

        if ($request->has('payment_mode') && $request->payment_mode != "") {
            if ($request->payment_mode == "cash" || $request->payment_mode == "bank") {
                $query = $query->join('payment_modes', 'payment_modes.id', '=', 'payments.payment_mode_id')
                    ->where('payment_modes.mode_type', $request->payment_mode);
            }
        }

        // Chekcing payments permissions
        if (!($user->ability('admin', 'payment_in_view') && $user->ability('admin', 'payment_out_view'))) {
            throw new ApiException("Don't have valid permission");
        } else if ($user->ability('admin', 'payment_in_view')) {
            $query = $query->where('payment_type', 'in');
        } else if ($user->ability('admin', 'payment_out_view')) {
            $query = $query->where('payment_type', 'out');
        }


        return $query;
    }

    public function customerSuppliers()
    {
        $company = company();

        $users = User::select('id', 'name')
            ->withoutGlobalScopes()
            ->where(function ($query) {
                $query->where('user_type', 'customers')
                    ->orWhere('user_type', 'suppliers');
            })
            ->where('company_id', $company->id)
            ->get();

        $data = $users->toArray();

        return ApiResponse::make('Data fetched', $data);
    }

    public function userInvoices()
    {
        $request = request();
        $userId = $this->getIdFromHash($request->user_id);
        $paymentType = $request->payment_type;

        if ($paymentType == 'in') {
            $orderType = ['sales', 'purchase-returns'];
        } else {
            $orderType = ['purchases', 'sales-returns'];
        }

        $invoices = Order::where('user_id', $userId)
            ->where('payment_status', '!=', 'paid')
            ->whereIn('order_type', $orderType)
            ->orderBy('orders.order_date')
            ->get();

        $data = [
            'invoices' => $invoices,
        ];

        return ApiResponse::make('Data fetched', $data);
    }
}
