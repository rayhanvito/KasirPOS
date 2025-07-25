<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Tax\IndexRequest;
use App\Http\Requests\Api\Tax\StoreRequest;
use App\Http\Requests\Api\Tax\UpdateRequest;
use App\Http\Requests\Api\Tax\DeleteRequest;
use App\Models\Tax;

class TaxController extends ApiBaseController
{
	protected $model = Tax::class;

	protected $indexRequest = IndexRequest::class;
	protected $storeRequest = StoreRequest::class;
	protected $updateRequest = UpdateRequest::class;
	protected $deleteRequest = DeleteRequest::class;

    public function modifyIndex($query)
    {
        $request = request();

        $query = $query->whereNull('taxes.parent_id');

        return $query;
    }

    public function stored(Tax $tax)
    {
        $this->insertOrUpdateTaxData($tax);

        return $tax;
    }

    public function updated($tax)
    {
        $this->insertOrUpdateTaxData($tax);

        return $tax;
    }

    public function insertOrUpdateTaxData($tax)
    {
        $request = request();

        $multipleTaxes = $request->multiple_taxes;
        foreach ($multipleTaxes as $multipleTax) {
            if ($multipleTax['id'] == '') {
                $childTax = new Tax();
            } else {
                $id = $this->getIdFromHash($multipleTax['id']);
                $childTax = Tax::find($id);
            }
            $childTax->name = $multipleTax['tax_name'];
            $childTax->rate = $multipleTax['rate'];
            $childTax->parent_id = $tax->id;
            $childTax->save();

        }
    }
}


