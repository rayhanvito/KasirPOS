<?php

namespace App\Traits;

use App\Classes\Common;
use App\Models\OrderPayment;
use App\Models\Payment;
use App\Models\PaymentMode;
use Examyou\RestAPI\ApiResponse;
use Examyou\RestAPI\Exceptions\ApiException;
use Examyou\RestAPI\Exceptions\ResourceNotFoundException;
use Vinkla\Hashids\Facades\Hashids;

trait PaymentTraits
{
    public $paymentType = "";

    protected function modifyIndex($query)
    {
        $request = request();

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

        $query = $query->where('payment_type', $this->paymentType);

        return $query;
    }

    public function storing(Payment $payment)
    {
        $request = request();
        $warehouse = warehouse();

        if ($request->has('payment_number') && $request->payment_number != "") {
            $payment->payment_number = $request->payment_number;
        }

        $payment->warehouse_id = $warehouse->id;
        $payment->payment_type = $this->paymentType;

        return $payment;
    }

    public function stored(Payment $payment)
    {
        $request = request();
        $paidAmount = 0;

        if ($request->has('invoices') && count($request->invoices) > 0) {
            $invoices = $request->invoices;

            // Deleting previous invoices of payments
            OrderPayment::where('payment_id', $payment->id)->delete();

            foreach ($invoices as $invoice) {
                $newOrderPayment = new OrderPayment();
                $newOrderPayment->payment_id = $payment->id;
                $newOrderPayment->order_id = $this->getIdFromHash($invoice['order_id']);
                $newOrderPayment->amount = $invoice['amount'];
                $newOrderPayment->save();

                $paidAmount += $newOrderPayment->amount;

                // Updating user amount
                Common::updateOrderAmount($newOrderPayment->order_id);
            }
        } else {
            // No invoice means no order
            // So updating directly user amount
            Common::updateUserAmount($payment->user_id, $payment->warehouse_id);
        }

        if ($payment->payment_number == null) {
            $paymentType = 'payment-' . $payment->payment_type;
            $payment->payment_number = Common::getTransactionNumber($paymentType, $payment->id);
        }

        $payment->paid_amount = $paidAmount;
        $payment->unused_amount = $payment->amount - $paidAmount;
        $payment->save();

        // Updating Warehouse History
        Common::updateWarehouseHistory('payment', $payment, "add_edit");
    }

    public function updating(Payment $payment)
    {
        if ($payment->amount != $payment->getOriginal('amount')) {
            throw new ApiException('Amount can not be changed');
        }

        if ($payment->warehouse_id != $payment->getOriginal('warehouse_id')) {
            throw new ApiException('Warehouse can not be changed');
        }

        return $payment;
    }

    public function updated(Payment $payment)
    {
        // Updating Warehouse History
        Common::updateWarehouseHistory('payment', $payment, "add_edit");
    }

    public function destroy(...$args)
    {
        \DB::beginTransaction();

        // Geting id from hashids
        $xid = last(func_get_args());
        $convertedId = Hashids::decode($xid);
        $id = $convertedId[0];

        $this->validate();

        // Get object for update
        $this->query = call_user_func($this->model . "::query");

        /** @var Model $object */
        $object = $this->query->find($id);

        if (!$object) {
            throw new ResourceNotFoundException();
        }

        if (method_exists($this, 'destroying')) {
            $object = call_user_func([$this, 'destroying'], $object);
        }

        // Getting order_id before deleting payment
        $orderPayments = OrderPayment::select('order_id')->where('payment_id', $id)->get();
        $userId = $object->user_id;
        $warehouseId = $object->warehouse_id;

        $object->delete();


        // Also deleting order Payments
        OrderPayment::select('order_id')->where('payment_id', $id)->delete();

        // Deleting order amount and user amount
        // After deleting payment
        if (count($orderPayments) > 0) {
            foreach ($orderPayments as $orderPayment) {
                Common::updateOrderAmount($orderPayment->order_id);
            }
        } else {
            Common::updateUserAmount($userId, $warehouseId);
        }

        $meta = $this->getMetaData(true);

        \DB::commit();

        // Updating Warehouse History
        Common::updateWarehouseHistory('payment', $object);

        if (method_exists($this, 'destroyed')) {
            call_user_func([$this, 'destroyed'], $object);
        }

        return ApiResponse::make("Resource deleted successfully", null, $meta);
    }
};
