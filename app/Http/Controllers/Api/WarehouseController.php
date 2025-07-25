<?php

namespace App\Http\Controllers\Api;

use App\Classes\Common;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Warehouse\IndexRequest;
use App\Http\Requests\Api\Warehouse\StoreRequest;
use App\Http\Requests\Api\Warehouse\UpdateRequest;
use App\Http\Requests\Api\Warehouse\DeleteRequest;
use App\Http\Requests\Api\Warehouse\UpdateOnlineStoreStatusRequest;
use App\Models\Customer;
use App\Models\FrontWebsiteSettings;
use App\Models\Product;
use App\Models\ProductDetails;
use App\Models\User;
use App\Models\UserDetails;
use App\Models\Warehouse;
use Examyou\RestAPI\ApiResponse;
use Examyou\RestAPI\Exceptions\ApiException;

class WarehouseController extends ApiBaseController
{
    protected $model = Warehouse::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    public function modifyIndex($query)
    {
        $loggedUser = user();

        if ($loggedUser && !$loggedUser->hasRole('admin')) {
            if ($loggedUser->user_type == 'staff_members') {
                $query = $query->where(function ($newQuery) use ($loggedUser) {
                    foreach ($loggedUser->userWarehouses as $userWaerehouseKey => $userWarehouse) {
                        if ($userWaerehouseKey == 0) {
                            $newQuery = $newQuery->where('warehouses.id', '=', $userWarehouse->warehouse_id);
                        } else {
                            $newQuery = $newQuery->orWhere('warehouses.id', '=', $userWarehouse->warehouse_id);
                        }
                    }
                });
            } else {
                $query = $query->where('warehouses.id', '=', $loggedUser->warehouse_id);
            }
        }

        return $query;
    }

    public function stored(Warehouse $warehouse)
    {
        $company = company();
        $companyWarehouse = $company->warehouse;

        // Front website settings
        $frontSetting = new FrontWebsiteSettings();
        $frontSetting->warehouse_id = $warehouse->id;
        $frontSetting->featured_categories = [];
        $frontSetting->featured_products = [];
        $frontSetting->features_lists = [];
        $frontSetting->pages_widget = [];
        $frontSetting->contact_info_widget = [];
        $frontSetting->links_widget = [];
        $frontSetting->top_banners = [];
        $frontSetting->bottom_banners_1 = [];
        $frontSetting->bottom_banners_2 = [];
        $frontSetting->bottom_banners_3 = [];
        $frontSetting->save();

        // Fix - Issue fixed for variable type product
        $allProducts = Product::select('id')
            ->where('product_type', 'single')
            ->whereNotNull('parent_id')->get();
        foreach ($allProducts as $allProduct) {
            // Getting product details for company default warehouse
            $defaultWarehouseProductDetails = ProductDetails::withoutGlobalScope('current_warehouse')
                ->where('warehouse_id', '=', $companyWarehouse->id)
                ->where('product_id', '=', $allProduct->id)
                ->first();

            // Inserting all products details for this warhouse
            $productDetails = new ProductDetails();
            $productDetails->warehouse_id = $warehouse->id;
            $productDetails->product_id = $allProduct->id;
            $productDetails->tax_id = $defaultWarehouseProductDetails->tax_id;
            $productDetails->mrp = $defaultWarehouseProductDetails->mrp;
            $productDetails->purchase_price = $defaultWarehouseProductDetails->purchase_price;
            $productDetails->sales_price = $defaultWarehouseProductDetails->sales_price;
            $productDetails->purchase_tax_type = $defaultWarehouseProductDetails->purchase_tax_type;
            $productDetails->sales_tax_type = $defaultWarehouseProductDetails->sales_tax_type;
            $productDetails->stock_quantitiy_alert = $defaultWarehouseProductDetails->stock_quantitiy_alert;
            $productDetails->wholesale_price = $defaultWarehouseProductDetails->wholesale_price;
            $productDetails->wholesale_quantity = $defaultWarehouseProductDetails->wholesale_quantity;
            $productDetails->save();

            // Common::updateProductCustomFields($allProduct, $productDetails->warehouse_id);
            Common::recalculateOrderStock($productDetails->warehouse_id, $allProduct->id);
        }

        // Creating user Details for each customer and supplier
        // For this created warehouse
        $allCustomerSuppliers = Customer::withoutGlobalScope('type')
            ->where('user_type', 'suppliers')
            ->orWhere('user_type', 'customers')
            ->get();
        foreach ($allCustomerSuppliers as $allCustomerSupplier) {
            $userDetails = new UserDetails();
            $userDetails->warehouse_id = $warehouse->xid;
            $userDetails->user_id = $allCustomerSupplier->id;
            $userDetails->credit_period = 30;
            $userDetails->save();
        }
    }

    public function updated(Warehouse $warehouse)
    {
        $sessionWarehouse = warehouse();

        // Reseting session warehouse
        if ($sessionWarehouse && $sessionWarehouse->id && $sessionWarehouse->id == $warehouse->id) {
            session(['warehouse' => $warehouse]);
        }

        return $warehouse;
    }

    public function destroying(Warehouse $warehouse)
    {
        $company = company();

        if ($warehouse->id == $company->warehouse_id) {
            throw new ApiException('Default warehouse cannot be deleted');
        }

        $warehouseStaffMemberCount = User::where('warehouse_id', $warehouse->id)->count();

        if ($warehouseStaffMemberCount > 0) {
            throw new ApiException('This warehouse have active staff member(s). Either delete or change their warehouse before deleteing this');
        }

        return $warehouse;
    }

    public function updateOnlineStoreStatus(UpdateOnlineStoreStatusRequest $request)
    {
        $warehouseId = $request->warehouse_id;
        $id = $this->getIdFromHash($warehouseId);

        $warehouse = Warehouse::find($id);
        $warehouse->online_store_enabled = $request->status;
        $warehouse->save();

        return ApiResponse::make('Success', []);
    }
}
