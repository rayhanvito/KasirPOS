<?php

namespace App\Http\Controllers\Api;

use App\Classes\Notify;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Expense\IndexRequest;
use App\Http\Requests\Api\Expense\StoreRequest;
use App\Http\Requests\Api\Expense\UpdateRequest;
use App\Http\Requests\Api\Expense\DeleteRequest;
use App\Models\Expense;
use Carbon\Carbon;
use Examyou\RestAPI\Exceptions\ApiException;

class ExpenseController extends ApiBaseController
{
    protected $model = Expense::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    public function modifyIndex($query)
    {
        $request = request();
        $warehouse = warehouse();

        // If user not have admin role
        // then he can only view reords
        // of warehouse assigned to him
        $query = $query->where('expenses.warehouse_id', $warehouse->id);

        if ($request->has('dates') && $request->dates != '') {
            $dates = explode(',', $request->dates);
            $startDate = $dates[0];
            $endDate = $dates[1];

            $query = $query->whereRaw('expenses.date >= ?', [$startDate])
                ->whereRaw('expenses.date <= ?', [$endDate]);
        }

        return $query;
    }

    public function storing(Expense $expense)
    {
        $request = request();
        $loggedUser = user();
        $warehouse = warehouse();

        if ($loggedUser->hasRole('admin')) {
            $expense->user_id = $request->user_id;
        } else {
            $expense->user_id = $loggedUser->id;
        }
        $expense->warehouse_id = $warehouse->id;

        return $expense;
    }

    public function stored(Expense $expense)
    {
        // Notifying to Warehouse
        Notify::send('expense_create', $expense);
    }

    public function updating(Expense $expense)
    {
        $loggedUser = user();
        $warehouse = warehouse();

        $expense->user_id = $loggedUser->id;
        $expense->warehouse_id = $warehouse->id;

        return $expense;
    }

    public function updated(Expense $expense)
    {
        // Notifying to Warehouse
        Notify::send('expense_update', $expense);
    }

    public function destroying(Expense $expense)
    {
        $loggedUser = user();

        if (!$loggedUser->hasRole('admin') && $loggedUser->id != $expense->user_id) {
            throw new ApiException("Can not delete other user expense");
        }

        return $expense;
    }

    public function destroyed(Expense $expense)
    {
        // Notifying to Warehouse
        Notify::send('expense_delete', $expense);
    }
}
