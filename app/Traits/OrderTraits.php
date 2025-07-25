<?php

namespace App\Traits;

use App\Classes\Common;
use App\Classes\Notify;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\ProductDetails;
use App\Models\StockHistory;
use App\Models\Unit;
use App\Models\User;
use App\Models\WarehouseStock;
use Examyou\RestAPI\ApiResponse;
use Examyou\RestAPI\Exceptions\ApiException;
use Examyou\RestAPI\Exceptions\ResourceNotFoundException;
use Vinkla\Hashids\Facades\Hashids;

trait OrderTraits
{
    public $orderType = "";

    protected function modifyIndex($query)
    {
        $request = request();
        $warehouse = warehouse();

        $query = $query->where('orders.order_type', $this->orderType);

        // Dates Filters
        if ($request->has('dates') && $request->dates != "") {
            $dates = explode(',', $request->dates);
            $startDate = $dates[0];
            $endDate = $dates[1];

            $query = $query->whereRaw('orders.order_date >= ?', [$startDate])
                ->whereRaw('orders.order_date <= ?', [$endDate]);
        }

        // Can see only order of warehouses which is assigned to him
        if ($this->orderType == 'stock-transfers') {
            if ($request->transfer_type == 'transfered') {
                $query = $query->where('orders.from_warehouse_id', $warehouse->id);
            } else {
                $query = $query->where('orders.warehouse_id', $warehouse->id);
            }
        } else {
            $query = $query->where('orders.warehouse_id', $warehouse->id);
        }

        return $query;
    }

    public function show(...$args)
    {
        $xid = last(func_get_args());
        $id = Common::getIdFromHash($xid);

        $orderDetails = Order::find($id);
        $orderType = $orderDetails->order_type;
        $allProducs = [];
        $selectProductIds = [];
        $sn = 1;

        $allOrderIteams = OrderItem::with('product')->where('order_id', $id)->get();

        foreach ($allOrderIteams as $allOrderIteam) {

            $productDetails = ProductDetails::withoutGlobalScope('current_warehouse')
                ->where('warehouse_id', '=', $orderDetails->warehouse_id)
                ->where('product_id', '=', $allOrderIteam->product_id)
                ->first();

            $maxQuantity = $productDetails->current_stock;
            $unit = $allOrderIteam->unit_id != null ? Unit::find($allOrderIteam->unit_id) : null;

            if ($orderType == 'purchase-returns' || $orderType == 'sales') {
                $maxQuantity = $allOrderIteam->quantity + $maxQuantity;
            }

            $allProducs[] = [
                'sn'    =>  $sn,
                'xid'    =>  Common::getHashFromId($allOrderIteam->product_id),
                'item_id'    =>  $allOrderIteam->xid,
                'name'    =>  $allOrderIteam->product->name,
                'image'    =>  $allOrderIteam->product->image,
                'image_url'    =>  $allOrderIteam->product->image_url,
                'x_tax_id'    =>   Common::getHashFromId($allOrderIteam->tax_id),
                'discount_rate'    =>  $allOrderIteam->discount_rate,
                'total_discount'    =>  $allOrderIteam->total_discount,
                'total_tax'    =>  $allOrderIteam->total_tax,
                'unit_price'    =>  $allOrderIteam->unit_price,
                'single_unit_price'    =>  $allOrderIteam->single_unit_price,
                'subtotal'    =>  $allOrderIteam->subtotal,
                'quantity'    =>  $allOrderIteam->quantity,
                'tax_rate'    =>  $allOrderIteam->tax_rate,
                'tax_type'    =>  $allOrderIteam->tax_type,
                'x_unit_id'    =>  Common::getHashFromId($allOrderIteam->unit_id),
                'unit'    =>  $unit,
                'stock_quantity' => $maxQuantity,
                'unit_short_name' => $unit && $unit->short_name ? $unit->short_name : '',
            ];

            $selectProductIds[] = Common::getHashFromId($allOrderIteam->product_id);
            $sn++;
        }

        $user = User::select('id', 'name', 'phone')->find($orderDetails->user_id);

        return ApiResponse::make('Data fetched', [
            'order' => $orderDetails,
            'items' => $allProducs,
            'ids' => $selectProductIds,
            'user' => $user,
        ]);
    }

    public function storing(Order $order)
    {
        $request = request();
        $warehouse = warehouse();

        if (!$request->has('invoice_number') || ($request->has('invoice_number') && $request->invoice_number == "")) {
            $order->invoice_number = '';
        }

        $order->unique_id = Common::generateOrderUniqueId();
        $order->order_type = $this->orderType;
        $order->warehouse_id = $this->orderType == 'stock-transfers' ? $request->warehouse_id : $warehouse->id;
        $order->from_warehouse_id = $this->orderType == 'stock-transfers' ? $warehouse->id : null;
        $order->user_id = $this->orderType == 'stock-transfers' ? null : $request->user_id;

        if ($this->orderType == "quotations") {
            $order->order_status = "pending";
        }

        return $order;
    }

    public function stored(Order $order)
    {
        $oldOrderId = "";

        if ($order->invoice_number == '') {
            $order->invoice_number = Common::getTransactionNumber($order->order_type, $order->id);
        }

        // Created by user
        $order->staff_user_id = auth('api')->user()->id;
        $order->save();

        $order = Common::storeAndUpdateOrder($order, $oldOrderId);

        // Updating Warehouse History
        Common::updateWarehouseHistory('order', $order, "add_edit");

        // Notifying to Warehouse
        Notify::send(str_replace('-', '_', $order->order_type)  . '_create', $order);

        return $order;
    }

    public function updating(Order $order)
    {
        $loggedUser = user();
        $warehouse = warehouse();

        // If logged in user is not admin
        // then cannot update order who are
        // of other warehouse
        if (!$loggedUser->hasRole('admin') && $order->warehouse_id != $warehouse->id) {
            throw new ApiException("Don't have valid permission");
        }

        $order->order_type = $this->orderType;

        return $order;
    }

    public function update(...$args)
    {
        \DB::beginTransaction();

        // Geting id from hashids
        $xid = last(func_get_args());
        $convertedId = Hashids::decode($xid);
        $id = $convertedId[0];

        $this->validate();

        // Get object for update
        $this->query = call_user_func($this->model . "::query");

        /** @var ApiModel $object */
        $object = $this->query->find($id);

        if (!$object) {
            throw new ResourceNotFoundException();
        }

        $oldUserId = $object->user_id;

        $orderPaymentCount = OrderPayment::where('order_id', $id)->count();
        $request = request();

        if ($orderPaymentCount > 0) {
            $object->order_status = $request->order_status;
        } else {
            $object->fill(request()->all());
        }

        if (method_exists($this, 'updating')) {
            $object = call_user_func([$this, 'updating'], $object);
        }

        $object->save();

        $meta = $this->getMetaData(true);

        \DB::commit();

        if (method_exists($this, 'updated')) {
            call_user_func([$this, 'updated'], $object);
        }

        // If user changed then
        // Update his order_count & order_return_count
        if ($oldUserId != $object->user_id) {
            Common::updateUserAmount($oldUserId, $object->warehouse_id);
        }

        // Updating Warehouse History
        Common::updateWarehouseHistory('order', $object, "add_edit");

        return ApiResponse::make("Resource updated successfully", ["xid" => $object->xid], $meta);
    }

    public function updated(Order $order)
    {
        $oldOrderId = $order->id;
        Common::storeAndUpdateOrder($order, $oldOrderId);

        // Notifying to Warehouse
        Notify::send(str_replace('-', '_', $order->order_type) . '_update', $order);
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

        $order = $object;
        $loggedUser = user();
        $orderItems = $order->items;
        $orderType = $order->order_type;
        $warehouseId = $order->warehouse_id;
        $fromWarehouseId = $order->from_warehouse_id;
        $orderUserId = $order->user_id;

        // If logged in user is not admin
        // then cannot delete order who are
        // of other warehouse
        if (!$loggedUser->hasRole('admin') && $order->warehouse_id != $loggedUser->warehouse_id) {
            throw new ApiException("Don't have valid permission");
        }

        foreach ($orderItems as $orderItem) {

            $stockHistory = new StockHistory();
            $stockHistory->warehouse_id = $warehouseId;
            $stockHistory->product_id = $orderItem->product_id;
            $stockHistory->quantity = 0;
            $stockHistory->old_quantity = $orderItem->quantity;
            $stockHistory->order_type = $orderType;
            $stockHistory->stock_type = $orderType == 'sales' || $orderType == 'purchase-returns' ? 'out' : 'in';
            $stockHistory->action_type = "delete";
            $stockHistory->created_by = $loggedUser->xid;
            $stockHistory->save();
        }

        // Notifying to Warehouse
        Notify::send(str_replace('-', '_', $object->order_type) . '_delete', $object);

        $object->delete();

        foreach ($orderItems as $orderItem) {
            $productId = $orderItem->product_id;

            // Update warehouse stock for product
            Common::recalculateOrderStock($warehouseId, $productId);

            if ($orderType == "stock-transfers") {
                Common::recalculateOrderStock($fromWarehouseId, $productId);
            }
        }

        // Update Customer or Supplier total amount, due amount, paid amount
        Common::updateUserAmount($orderUserId, $order->warehouse_id);

        // Updating Warehouse History
        Common::updateWarehouseHistory('order', $order);

        $meta = $this->getMetaData(true);

        \DB::commit();

        return ApiResponse::make("Resource deleted successfully", null, $meta);
    }
};
