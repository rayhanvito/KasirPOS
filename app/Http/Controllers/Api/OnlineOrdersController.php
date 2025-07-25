<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\OnlineOrders\IndexRequest;
use App\Models\Order;
use Examyou\RestAPI\ApiResponse;
use Illuminate\Http\Request;

class OnlineOrdersController extends ApiBaseController
{
	protected $model = Order::class;

	protected $indexRequest = IndexRequest::class;

	protected function modifyIndex($query)
	{
		$request = request();
		$warehouse = warehouse();

		$query = $query->where('orders.order_type', "online-orders");

		// Dates Filters
		if ($request->has('dates') && $request->dates != "") {
			$dates = explode(',', $request->dates);
			$startDate = $dates[0];
			$endDate = $dates[1];

			$query = $query->whereRaw('orders.order_date >= ?', [$startDate])
				->whereRaw('orders.order_date <= ?', [$endDate]);
		}

		// Can see only order of warehouses which is assigned to him
		$query = $query->where('orders.warehouse_id', $warehouse->id);

		return $query;
	}

	public function cancelOrder($id)
	{
		$order = Order::where('unique_id', $id)->first();

		if ($order->order_type == "online-orders" && $order->order_status != 'delivered') {
			$order->cancelled = 1;
			$order->cancelled_by = auth('api')->user()->id;
			$order->save();

			return ApiResponse::make('Success', []);
		}
	}

	public function confirmOrder(Request $request, $id)
	{
		$order = Order::where('unique_id', $id)->first();

		if ($order->order_type == "online-orders" && $order->order_status == 'ordered' && $order->cancelled != 1) {
			$order->order_status = "confirmed";
			$order->save();

			return ApiResponse::make('Success', []);
		}
	}

	public function changeOrderStatus(Request $request, $id)
	{
		$order = Order::where('unique_id', $id)->first();

		if (
			$order->order_type == "online-orders" &&
			$order->order_status != 'ordered' &&
			$order->order_status != 'delivered' &&
			$order->cancelled != 1 &&
			($request->order_status == 'confirmed' || $request->order_status == 'processing' || $request->order_status == 'shipping')
		) {
			$order->order_status = $request->order_status;
			$order->save();

			return ApiResponse::make('Success', []);
		}
	}

	public function markAsDelivered(Request $request, $id)
	{
		$order = Order::where('unique_id', $id)->first();

		if (
			$order->order_type == "online-orders" &&
			$order->cancelled != 1 &&
			($order->order_status == 'confirmed' || $order->order_status == 'processing' || $order->order_status == 'shipping')
		) {
			$order->order_status = "delivered";
			$order->save();

			return ApiResponse::make('Success', []);
		}
	}
}
