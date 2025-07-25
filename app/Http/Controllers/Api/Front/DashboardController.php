<?php

namespace App\Http\Controllers\Api\Front;

use App\Classes\Common;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Front\Dashboard\UserOrderRequest;
use App\Models\Company;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderShippingAddress;
use App\Models\Product;
use App\Models\UserAddress;
use App\Models\Warehouse;
use Carbon\Carbon;
use Examyou\RestAPI\ApiResponse;
use Examyou\RestAPI\Exceptions\ApiException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends ApiBaseController
{
    public function dashboard(Request $request)
    {
        $storeSlug = $request->warehouse;
        $warehouse = Warehouse::withoutGlobalScope(CompanyScope::class)->where('slug', $storeSlug)->first();

        if (!$warehouse) {
            throw new ApiException("Not a valid warehouse");
        }

        $user = auth('api_front')->user();
        $totalOrder = Order::withoutGlobalScope(CompanyScope::class)
            ->where('order_type', '=', 'online-orders')
            ->where('user_id', '=', $user->id)
            ->where('warehouse_id', '=', $warehouse->id)
            ->count();

        $pendingOrder = Order::withoutGlobalScope(CompanyScope::class)
            ->where('order_type', '=', 'online-orders')
            ->where('order_status', '=', 'ordered')
            ->where('cancelled', '!=', 1)
            ->where('user_id', '=', $user->id)
            ->where('warehouse_id', '=', $warehouse->id)
            ->count();

        $processingOrder = Order::withoutGlobalScope(CompanyScope::class)
            ->where('order_type', '=', 'online-orders')
            ->where('order_status', '=', 'processing')
            ->where('cancelled', '!=', 1)
            ->where('user_id', '=', $user->id)
            ->where('warehouse_id', '=', $warehouse->id)
            ->count();

        $completedOrders = Order::withoutGlobalScope(CompanyScope::class)
            ->where('order_type', '=', 'online-orders')
            ->where('order_status', '=', 'delivered')
            ->where('cancelled', '!=', 1)
            ->where('user_id', '=', $user->id)
            ->where('warehouse_id', '=', $warehouse->id)
            ->count();

        $recentOrders = Order::withoutGlobalScope(CompanyScope::class)
            ->with(['user:id,name,email,address', 'warehouse:id,name,address', 'items', 'items.product:id,name', 'shippingAddress'])
            ->where('order_type', '=', 'online-orders')
            ->where('user_id', '=', $user->id)
            ->where('warehouse_id', '=', $warehouse->id)
            ->take(10)
            ->get();

        return ApiResponse::make('Data fetched', [
            'totalOrders' => $totalOrder,
            'pendingOrders' => $pendingOrder,
            'processingOrders' => $processingOrder,
            'completedOrders' => $completedOrders,
            'recentOrders' => $recentOrders,
        ]);
    }

    public function orders(UserOrderRequest $request)
    {
        $storeSlug = $request->warehouse;
        $warehouse = Warehouse::withoutGlobalScope(CompanyScope::class)->where('slug', $storeSlug)->first();

        if (!$warehouse) {
            throw new ApiException("Not a valid warehouse");
        }

        $user = auth('api_front')->user();
        $orders = Order::with(['user:id,name,email,address', 'warehouse:id,name,address', 'items', 'items.product:id,name', 'shippingAddress'])
            ->where('orders.warehouse_id', '=', $warehouse->id);


        // Order Status Filter
        if ($request->has('order_status_type') && $request->order_status_type != 'all') {
            if ($request->order_status_type == 'pending') {
                $orders = $orders->where(function ($query) {
                    return $query->where('payment_status', '=', 'pending')
                        ->orWhere('payment_status', '=', 'partially_paid')
                        ->orWhere('payment_status', '=', 'unpaid');
                })
                    ->where('cancelled', '!=', 1);
            } else if ($request->order_status_type == 'paid') {
                $orders = $orders->where('payment_status', '=', 'paid')
                    ->where('cancelled', '!=', 1);
            } else if ($request->order_status_type == 'cancelled') {
                $orders = $orders->where('cancelled', '=', 1);
            }
        }

        // Date Filter
        if ($request->has('dates') && $request->dates != null && count($request->dates) > 1) {
            $dates = $request->dates;

            $orders = $orders->whereBetween(DB::raw('DATE(order_date)'), $dates);
        }

        $orders = $orders->where('order_type', '=', 'online-orders')
            ->where('user_id', '=', $user->id)
            ->orderBy('order_date', 'desc')
            ->get();

        return ApiResponse::make('Data fetched', [
            'orders' => $orders,
        ]);
    }

    public function cancelOrder($orderUniqueId)
    {
        $user = auth('api_front')->user();

        $order = Order::where('unique_id', $orderUniqueId)->first();

        if ($order && $order->user_id == $user->id && $order->order_type == 'online-orders' && $order->order_status == 'ordered') {
            $order->cancelled = 1;
            $order->cancelled_by = $user->id;
            $order->save();

            return ApiResponse::make('Data fetched');
        }

        // TODO - Error Response
    }

    public function checkoutOrders(Request $request)
    {
        $user = auth('api_front')->user();
        $allProducts = $request->products;
        $totalTax = 0;
        $totalItems = 0;
        $totalQuantities = 0;
        $subtotal = 0;
        $total = 0;
        $addressId = $this->getIdFromHash($request->address_id);
        $warehouse = Warehouse::where('slug', $request->warehouse)->first();

        if (!$warehouse) {
            throw new ApiException("Not a valid store.");
        }

        $address = UserAddress::where('user_id', $user->id)->where('id', $addressId)->first();
        if (!$address) {
            throw new ApiException("Address not valid.");
        }

        DB::beginTransaction();

        try {
            $order = new Order();
            $order->order_type = 'online-orders';
            $order->invoice_number = "";
            $order->unique_id = Common::generateOrderUniqueId();
            $order->order_date = Carbon::now();
            $order->warehouse_id = $warehouse->id;
            $order->user_id = $user->id;
            $order->subtotal = 0;
            $order->total = 0;
            $order->order_status = "ordered";
            $order->save();

            $order->invoice_number = Common::getTransactionNumber($order->order_type, $order->id);
            $order->save();

            foreach ($allProducts as $allProduct) {
                $productId = $this->getIdFromHash($allProduct['xid']);
                $product = Product::select('id', 'name', 'slug', 'image', 'description', 'category_id', 'brand_id', 'unit_id')
                    ->with(['details:id,product_id,sales_price,mrp,tax_id,sales_tax_type', 'details.tax:id,rate', 'category:id,name,slug', 'brand:id,name,slug'])
                    ->find($productId);

                if ($product) {
                    $orderItem = new OrderItem();
                    $orderItem->user_id = $user->id;
                    $orderItem->order_id = $order->id;
                    $orderItem->product_id = $product->id;
                    $orderItem->unit_id = $product->unit_id;
                    $orderItem->quantity = $allProduct['cart_quantity'];
                    $orderItem->unit_price = $product->details->sales_price;
                    $orderItem->single_unit_price = $product->details->sales_price;
                    $orderItem->tax_id = $product->details->tax_id;
                    $orderItem->tax_rate = $product->details->tax_id != null ? $product->details->tax->rate : 0;
                    $orderItem->tax_type = $product->details->sales_tax_type;

                    $taxAmount = Common::getSalesOrderTax($orderItem->tax_rate, $product->details->sales_price, $product->details->sales_tax_type);
                    $salesPriceWithTax = Common::getSalesPriceWithTax($orderItem->tax_rate, $product->details->sales_price, $product->details->sales_tax_type);

                    $orderItem->total_tax = $taxAmount * $orderItem->quantity;
                    $orderItem->subtotal = $salesPriceWithTax * $orderItem->quantity;
                    $orderItem->save();

                    $totalTax += $taxAmount;
                    $totalItems += 1;
                    $totalQuantities += $orderItem->quantity;
                    $subtotal += $orderItem->subtotal;
                    $total += $orderItem->subtotal;
                }
            }

            $order->tax_amount = $totalTax;
            $order->subtotal = $subtotal;
            $order->total = $total;
            $order->due_amount = $total;
            $order->total_items = $totalItems;
            $order->total_quantity = $totalQuantities;
            $order->save();

            $orderShippingAddress = new OrderShippingAddress();
            $orderShippingAddress->order_id = $order->id;
            $orderShippingAddress->name = $address->name;
            $orderShippingAddress->email = $address->email;
            $orderShippingAddress->phone = $address->phone;
            $orderShippingAddress->address = $address->address;
            $orderShippingAddress->shipping_address = $address->shipping_address;
            $orderShippingAddress->city = $address->city;
            $orderShippingAddress->state = $address->state;
            $orderShippingAddress->country = $address->country;
            $orderShippingAddress->zipcode = $address->zipcode;
            $orderShippingAddress->save();

            DB::commit();

            return ApiResponse::make('Order placed successfull', [
                'unique_id' => $order->unique_id
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            throw new ApiException($e->getMessage());
            // throw new ApiException("Something went wrong. Please contact to administrator.");
        }
    }

    public function checkoutSuccess($orderUniqueId)
    {
        $user = auth('api_front')->user();
        $order = Order::with(['user:id,name,email,address', 'warehouse:id,name,address', 'items', 'items.product:id,name', 'shippingAddress'])
            ->where('order_type', '=', 'sales')
            ->where('unique_id', '=', $orderUniqueId)
            ->where('user_id', '=', $user->id)
            ->first();

        return ApiResponse::make('Data fetched', [
            'order' => $order,
        ]);
    }
}
