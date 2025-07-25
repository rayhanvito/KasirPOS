<?php

namespace App\Http\Controllers\Api;

use App\Classes\Common;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Quotations\IndexRequest;
use App\Http\Requests\Api\Quotations\StoreRequest;
use App\Http\Requests\Api\Quotations\UpdateRequest;
use App\Http\Requests\Api\Quotations\DeleteRequest;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Traits\OrderTraits;
use Examyou\RestAPI\ApiResponse;

class QuotationController extends ApiBaseController
{
    use OrderTraits;

    protected $model = Order::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    public function __construct()
    {
        parent::__construct();

        $this->orderType = "quotations";
    }

    public function convertToSale(Request $request, $id)
    {
        $order = Order::where('unique_id', $id)->first();

        if ($order->order_type == "quotations") {
            $order->order_type = 'sales';
            $order->order_status = 'confirmed';
            $order->save();

            Common::storeAndUpdateOrder($order, "");

            Common::updateUserAmount($order->user_id, $order->warehouse_id);

            // Updating Warehouse History
            Common::updateWarehouseHistory('order', $order, "add_edit");

            return ApiResponse::make('Success', []);
        }
    }
}
