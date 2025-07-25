<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\WarehouseHistory\IndexRequest;
use App\Models\WarehouseHistory;

class WarehouseHistoryController extends ApiBaseController
{
    protected $model = WarehouseHistory::class;

    protected $indexRequest = IndexRequest::class;

    public function modifyIndex($query)
    {
        $request = request();
        $userId = "";
        if ($request->has('user_id') && $request->user_id != "") {
            $userId = $this->getIdFromHash($request->user_id);
        }

        if ($request->has('result_type') && $request->result_type == "customer_supplier" && $userId != "") {
            $query = $query->whereIn('warehouse_history.type', ['purchases', 'purchase-returns', 'sales', 'sales-returns', 'payment-out', 'payment-in'])
                ->where('warehouse_history.user_id', $userId);
        } else if ($request->has('result_type') && $request->result_type == "staff_member" && $userId != "") {
            $query = $query->whereIn('warehouse_history.type', ['purchases', 'purchase-returns', 'sales', 'sales-returns'])
                ->where('warehouse_history.staff_user_id', $userId);
        } else if ($userId != "") {
            $query = $query->where('warehouse_history.user_id', $userId);
        }

        return $query;
    }
}
