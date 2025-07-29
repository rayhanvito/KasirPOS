<?php

namespace App\Traits;

use App\Models\Invoice;
use App\Models\InvoiceProduct;
use Illuminate\Support\Facades\DB;

trait SalesReport
{
    public function getInvoiceItems($start, $end)
    {
        $invoiceItems = InvoiceProduct::select('product_services.name', DB::raw('sum(invoice_products.quantity) as quantity'), DB::raw('sum(invoice_products.price * invoice_products.quantity) as price'), DB::raw('sum(invoice_products.price * invoice_products.quantity)/sum(invoice_products.quantity) as avg_price'));
        $invoiceItems->leftjoin('product_services', 'product_services.id', 'invoice_products.product_id');
        $invoiceItems->leftJoin('invoices', 'invoice_products.invoice_id', 'invoices.id');
        $invoiceItems->where('invoices.created_by', \Auth::user()->creatorId());
        if ($start && $end) {
            $invoiceItems->whereBetween('invoices.issue_date', [$start, $end]);
        }
        $invoiceItems->whereNotNull('product_services.name');
        $invoiceItems->groupBy('invoice_products.product_id');
        return $invoiceItems->get()->toArray();
    }

    public function getInvoiceCustomers($start, $end)
    {
        $invoices = Invoice::select('customers.name', \DB::raw('count(DISTINCT invoices.customer_id, invoice_products.invoice_id) as invoice_count'))
                ->selectRaw('sum((invoice_products.price * invoice_products.quantity) - invoice_products.discount) as price')
                ->selectRaw('(SELECT SUM((price * quantity - discount) * (taxes.rate / 100)) FROM invoice_products
            LEFT JOIN taxes ON FIND_IN_SET(taxes.id, invoice_products.tax) > 0
            WHERE invoice_products.invoice_id = invoices.id) as total_tax')
                ->leftJoin('customers', 'customers.id', 'invoices.customer_id')
                ->leftJoin('invoice_products', 'invoice_products.invoice_id', 'invoices.id')
                ->where('invoices.created_by', \Auth::user()->creatorId())
                ->groupBy('invoices.invoice_id');

        if ($start && $end) {
            $invoices->whereBetween('invoices.issue_date', [$start, $end]);
        }
    
        $invoices =  $invoices->get()->toArray();
        $mergedArray = [];
        foreach ($invoices as $item) {
            $name = $item["name"];

            if (!isset($mergedArray[$name])) {
                $mergedArray[$name] = [
                    "name" => $name,
                    "invoice_count" => 0,
                    "price" => 0.0,
                    "total_tax" => 0.0,
                ];
            }

            $mergedArray[$name]["invoice_count"] += $item["invoice_count"];
            $mergedArray[$name]["price"]         += $item["price"];
            $mergedArray[$name]["total_tax"]     += $item["total_tax"];
        }
        $invoiceCustomers = array_values($mergedArray);
    
        return $invoiceCustomers;
    }
}