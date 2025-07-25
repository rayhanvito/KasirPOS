<?php

namespace App\Http\Controllers\Api;

use App\Classes\Common;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Product\CheckVariantRequest;
use App\Http\Requests\Api\Product\IndexRequest;
use App\Http\Requests\Api\Product\StoreRequest;
use App\Http\Requests\Api\Product\UpdateRequest;
use App\Http\Requests\Api\Product\DeleteRequest;
use App\Http\Requests\Api\Product\ImportRequest;
use App\Imports\ProductImport;
use App\Models\Product;
use App\Models\ProductDetails;
use App\Models\ProductVariant;
use App\Models\Tax;
use App\Models\Unit;
use App\Models\Variation;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Examyou\RestAPI\ApiResponse;
use Maatwebsite\Excel\Facades\Excel;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Str;

class ProductController extends ApiBaseController
{
    protected $model = Product::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    public function modifyIndex($query)
    {
        $request = request();
        $warehouse = warehouse();

        if ($warehouse->products_visibility == 'warehouse') {
            $query->where('products.warehouse_id', '=', $warehouse->id);
        }

        if($request->has('product_type') && $request->product_type == 'variable') {
            $query = $query->whereNull('products.parent_id')
                ->where('products.product_type', 'variable');
        } else if($request->has('product_type') && $request->product_type == 'single') {
            $query = $query->join('product_details', 'product_details.product_id', '=', 'products.id')
                ->where('product_details.warehouse_id', $warehouse->id)
                ->whereNull('products.parent_id')
                ->where('products.product_type', 'single');
        } else {
            $query = $query->join('product_details', 'product_details.product_id', '=', 'products.id')
            ->where('product_details.warehouse_id', $warehouse->id);
        }


        if ($request->has('fetch_stock_alert') && $request->fetch_stock_alert) {
            $query = $query->whereNotNull('stock_quantitiy_alert')
                ->whereRaw('product_details.current_stock <= product_details.stock_quantitiy_alert');
        };

        if ($request->has('x_id') && $request->x_id != '') {
            $query = $query->where('products.id', $this->getIdFromHash($request->x_id));
        };

        return $query;
    }

    public function storing(Product $product)
    {
        $request = request();
        $loggedUser = user();
        $warehouse = warehouse();

        $product->user_id = $loggedUser->id;
        $product->warehouse_id = $loggedUser->hasRole('admin') && $request->warehouse_id != '' ? $request->warehouse_id : $warehouse->id;

        return $product;
    }
    public function stored(Product $product)
    {
        $request = request();
        $allWarehouses = Warehouse::select('id')->get();
        $productType = $product->product_type;

        if($productType == 'variable') {
            $allVariations = $request->variations;

            foreach($allVariations as $allVariation) {
                // $variantValueId = $this->getIdFromHash($allVariation['variant_value_id']);
                // $variantValue = Variation::find($variantValueId);

                $allRequestVariantTypes = $allVariation['variant_value_id'];
                $fullName = $product->name . ' - ';
                foreach($allRequestVariantTypes as $allRequestVariantType) {
                    $convertedVariantValueId = $this->getIdFromHash($allRequestVariantType);
                    $variantValueRecord = Variation::find($convertedVariantValueId);

                    $fullName .= $variantValueRecord->name . ',';
                }


                $newVariantProduct = new Product();
                $newVariantProduct->image = isset($allVariation['image']) ? $allVariation['image'] : null;
                $newVariantProduct->warehouse_id = $product->warehouse_id;
                $newVariantProduct->product_type = 'single';
                $newVariantProduct->name = trim($fullName, ',');
                $newVariantProduct->slug = Str::slug($newVariantProduct->name, '-');
                $newVariantProduct->barcode_symbology = $product->barcode_symbology;
                $newVariantProduct->item_code = $allVariation['item_code'];
                $newVariantProduct->parent_item_code = $product->item_code;
                $newVariantProduct->category_id = $product->category_id;
                $newVariantProduct->brand_id = $product->brand_id;
                $newVariantProduct->unit_id = $product->unit_id;
                $newVariantProduct->user_id = user()->id;
                // $newVariantProduct->variant_id = $allVariation['variant_id'];
                // $newVariantProduct->variant_value_id = $allVariation['variant_value_id'];
                $newVariantProduct->parent_id = $product->id;
                $newVariantProduct->save();

                foreach($allRequestVariantTypes as $allRequestVariantType) {
                    $convertedVariantValueId = $this->getIdFromHash($allRequestVariantType);
                    $variantValueRecord = Variation::find($convertedVariantValueId);

                    $newProductVariant = new ProductVariant();
                    $newProductVariant->product_id = $newVariantProduct->id;
                    $newProductVariant->variant_id = $variantValueRecord->parent_id;
                    $newProductVariant->variant_value_id = $variantValueRecord->id;
                    $newProductVariant->save();
                }

                foreach ($allWarehouses as $allWarehouse) {
                    $productDetails = new ProductDetails();
                    $productDetails->warehouse_id = $allWarehouse->id;
                    $productDetails->product_id = $newVariantProduct->id;

                    $productDetails->tax_id = $request->has('tax_id') && $request->tax_id != '' ? $request->tax_id : null;
                    $productDetails->mrp = $allVariation['mrp'];
                    $productDetails->purchase_price = $allVariation['purchase_price'];
                    $productDetails->sales_price = $allVariation['sales_price'];
                    $productDetails->purchase_tax_type = $allVariation['purchase_tax_type'];
                    $productDetails->sales_tax_type = $allVariation['sales_tax_type'];
                    $productDetails->stock_quantitiy_alert = isset($allVariation['stock_quantitiy_alert']) ? $allVariation['stock_quantitiy_alert'] : null;
                    // $productDetails->wholesale_price = $request->wholesale_price;
                    // $productDetails->wholesale_quantity = $request->wholesale_quantity;
                    $productDetails->save();

                    // Common::updateProductCustomFields($product, $allWarehouse->id);
                }

                // Saving Opening Stock and date for current product
                // Becuase these values will be different for each product
                // At warehouse
                $currentProductDetails = $newVariantProduct->details;
                $currentProductDetails->opening_stock = $allVariation['opening_stock'];
                $currentProductDetails->opening_stock_date = isset($allVariation['opening_stock_date']) ? $allVariation['opening_stock_date'] : null;
                $currentProductDetails->save();

                Common::recalculateOrderStock($currentProductDetails->warehouse_id, $newVariantProduct->id);
            }

        } else {
            foreach ($allWarehouses as $allWarehouse) {
                $productDetails = new ProductDetails();
                $productDetails->warehouse_id = $allWarehouse->id;
                $productDetails->product_id = $product->id;

                $productDetails->tax_id = $request->has('tax_id') && $request->tax_id != '' ? $request->tax_id : null;
                $productDetails->mrp = $request->mrp;
                $productDetails->purchase_price = $request->purchase_price;
                $productDetails->sales_price = $request->sales_price;
                $productDetails->purchase_tax_type = $request->purchase_tax_type;
                $productDetails->sales_tax_type = $request->sales_tax_type;
                $productDetails->stock_quantitiy_alert = isset($allVariation['stock_quantitiy_alert']) ? $allVariation['stock_quantitiy_alert'] : null;;
                $productDetails->wholesale_price = $request->wholesale_price;
                $productDetails->wholesale_quantity = $request->wholesale_quantity;
                $productDetails->save();

                Common::updateProductCustomFields($product, $allWarehouse->id);
            }

            // Saving Opening Stock and date for current product
            // Becuase these values will be different for each product
            // At warehouse
            $currentProductDetails = $product->details;
            $currentProductDetails->opening_stock = $request->opening_stock;
            $currentProductDetails->opening_stock_date = $request->has('opening_stock_date') ? $request->opening_stock_date : null;
            $currentProductDetails->save();

            Common::recalculateOrderStock($currentProductDetails->warehouse_id, $product->id);
        }
    }

    public function updating($product)
    {
        $request = request();
        $loggedUser = user();

        if ($loggedUser->hasRole('admin') && $request->warehouse_id != '') {
            $product->warehouse_id = $request->warehouse_id;
        }

        return $product;
    }

    public function updated(Product $product)
    {
        $request = request();
        $productType = $product->product_type;
        $allWarehouses = Warehouse::select('id')->get();

        if($productType == 'variable') {
            $allVariations = $request->variations;

            foreach($allVariations as $allVariation) {
                // This is newly added variant product
                if($allVariation['xid'] == '') {
                    // $variantValueId = $this->getIdFromHash($allVariation['variant_value_id']);
                    // $variantValue = Variation::find($variantValueId);

                    $allRequestVariantTypes = $allVariation['variant_value_id'];
                    $fullName = $product->name . ' - ';
                    foreach($allRequestVariantTypes as $allRequestVariantType) {
                        $convertedVariantValueId = $this->getIdFromHash($allRequestVariantType);
                        $variantValueRecord = Variation::find($convertedVariantValueId);

                        $fullName .= $variantValueRecord->name . ',';
                    }

                    $newVariantProduct = new Product();
                    $newVariantProduct->image = isset($allVariation['image']) ? $allVariation['image'] : null;
                    $newVariantProduct->warehouse_id = $product->warehouse_id;
                    $newVariantProduct->product_type = 'single';
                    $newVariantProduct->name = trim($fullName, ',');
                    $newVariantProduct->slug = Str::slug($newVariantProduct->name, '-');
                    $newVariantProduct->barcode_symbology = $product->barcode_symbology;
                    $newVariantProduct->item_code = $allVariation['item_code'];
                    $newVariantProduct->parent_item_code = $product->item_code;
                    $newVariantProduct->category_id = $product->category_id;
                    $newVariantProduct->brand_id = $product->brand_id;
                    $newVariantProduct->unit_id = $product->unit_id;
                    $newVariantProduct->user_id = user()->id;
                    // $newVariantProduct->variant_id = $allVariation['variant_id'];
                    // $newVariantProduct->variant_value_id = $allVariation['variant_value_id'];
                    $newVariantProduct->parent_id = $product->id;
                    $newVariantProduct->save();

                    // Deleting Previous ProductVariants
                    ProductVariant::where('product_id', $newVariantProduct->id)->delete();
                    foreach($allRequestVariantTypes as $allRequestVariantType) {
                        $convertedVariantValueId = $this->getIdFromHash($allRequestVariantType);
                        $variantValueRecord = Variation::find($convertedVariantValueId);

                        $newProductVariant = new ProductVariant();
                        $newProductVariant->product_id = $newVariantProduct->id;
                        $newProductVariant->variant_id = $variantValueRecord->parent_id;
                        $newProductVariant->variant_value_id = $variantValueRecord->id;
                        $newProductVariant->save();
                    }

                    foreach ($allWarehouses as $allWarehouse) {
                        $productDetails = new ProductDetails();
                        $productDetails->warehouse_id = $allWarehouse->id;
                        $productDetails->product_id = $newVariantProduct->id;

                        $productDetails->tax_id = $request->has('tax_id') && $request->tax_id != '' ? $request->tax_id : null;
                        $productDetails->mrp = $allVariation['mrp'];
                        $productDetails->purchase_price = $allVariation['purchase_price'];
                        $productDetails->sales_price = $allVariation['sales_price'];
                        $productDetails->purchase_tax_type = $allVariation['purchase_tax_type'];
                        $productDetails->sales_tax_type = $allVariation['sales_tax_type'];
                        $productDetails->stock_quantitiy_alert = $request->stock_quantitiy_alert;
                        $productDetails->wholesale_price = $request->wholesale_price;
                        $productDetails->wholesale_quantity = $request->wholesale_quantity;
                        $productDetails->save();

                        // Common::updateProductCustomFields($product, $allWarehouse->id);
                    }

                    // Saving Opening Stock and date for current product
                    // Becuase these values will be different for each product
                    // At warehouse
                    $currentProductDetails = $newVariantProduct->details;
                    $currentProductDetails->opening_stock = $allVariation['opening_stock'];
                    $currentProductDetails->opening_stock_date = isset($allVariation['opening_stock_date']) ? $allVariation['opening_stock_date'] : null;
                    $currentProductDetails->save();

                    Common::recalculateOrderStock($currentProductDetails->warehouse_id, $newVariantProduct->id);
                } else {
                    // This is old variant product
                    $variantProductId =  $this->getIdFromHash($allVariation['xid']);

                    $variantProduct = Product::find($variantProductId);

                    // $variantValueId = $variantProduct->variant_value_id;
                    // $variantValue = Variation::find($variantValueId);
                    $allRequestVariantTypes = $allVariation['variant_value_id'];
                    $fullName = $product->name . ' - ';
                    foreach($allRequestVariantTypes as $allRequestVariantType) {
                        $convertedVariantValueId = $this->getIdFromHash($allRequestVariantType);
                        $variantValueRecord = Variation::find($convertedVariantValueId);

                        $fullName .= $variantValueRecord->name . ',';
                    }

                    $variantProduct->name = trim($fullName, ',');
                    $variantProduct->slug = Str::slug($variantProduct->name, '-');
                    $variantProduct->item_code = $allVariation['item_code'];
                    $variantProduct->save();

                    $currentProductDetails = $variantProduct->details;
                    $currentProductDetails->tax_id = $product->tax_id;
                    $currentProductDetails->mrp = $allVariation['mrp'];
                    $currentProductDetails->purchase_price = $allVariation['purchase_price'];
                    $currentProductDetails->sales_price = $allVariation['sales_price'];
                    $currentProductDetails->purchase_tax_type = $allVariation['purchase_tax_type'];
                    $currentProductDetails->sales_tax_type = $allVariation['sales_tax_type'];
                    $currentProductDetails->stock_quantitiy_alert = isset($allVariation['stock_quantitiy_alert']) ? $allVariation['stock_quantitiy_alert'] : null;;
                    // $currentProductDetails->wholesale_price = $request->wholesale_price;
                    // $currentProductDetails->wholesale_quantity = $request->wholesale_quantity;
                    $currentProductDetails->opening_stock = $allVariation['opening_stock'];
                    $currentProductDetails->opening_stock_date = isset($allVariation['opening_stock_date']) ? $allVariation['opening_stock_date'] : null;
                    $currentProductDetails->save();

                    Common::updateProductCustomFields($variantProduct, $currentProductDetails->warehouse_id);
                    Common::recalculateOrderStock($currentProductDetails->warehouse_id, $variantProduct->id);
                }
            }
        } else {
            $currentProductDetails = $product->details;
            $currentProductDetails->tax_id = $request->has('tax_id') && $request->tax_id != '' ? $request->tax_id : null;
            $currentProductDetails->mrp = $request->mrp;
            $currentProductDetails->purchase_price = $request->purchase_price;
            $currentProductDetails->sales_price = $request->sales_price;
            $currentProductDetails->purchase_tax_type = $request->purchase_tax_type;
            $currentProductDetails->sales_tax_type = $request->sales_tax_type;
            $currentProductDetails->stock_quantitiy_alert = $request->stock_quantitiy_alert;
            $currentProductDetails->wholesale_price = $request->wholesale_price;
            $currentProductDetails->wholesale_quantity = $request->wholesale_quantity;
            $currentProductDetails->opening_stock = $request->opening_stock;
            $currentProductDetails->opening_stock_date = $request->has('opening_stock_date') ? $request->opening_stock_date : null;
            $currentProductDetails->save();

            Common::updateProductCustomFields($product, $currentProductDetails->warehouse_id);
            Common::recalculateOrderStock($currentProductDetails->warehouse_id, $product->id);
        }


    }

    public function searchProduct(Request $request)
    {
        $warehouse = warehouse();
        $searchTerm = $request->search_term;
        $orderType = $request->order_type;
        $warehouseId = $warehouse->id;

        $products = Product::select('products.id', 'products.name', 'products.image', 'products.unit_id')
            ->where(function ($query) use ($searchTerm) {
                $query->where('products.name', 'LIKE', "%$searchTerm%")
                    ->orWhere('products.item_code', trim($searchTerm))
                    ->orWhere('products.parent_item_code', trim($searchTerm));
            });

        if ($warehouse->products_visibility == 'warehouse') {
            $products->where('products.warehouse_id', '=', $warehouse->id);
        }

        if ($request->has('products')) {
            $selectedProducts = $request->products;
            $convertedSelectedProducts = [];
            if (count($selectedProducts) > 0) {
                foreach ($selectedProducts as $selectedProduct) {
                    $convertedSelectedProducts[] = $this->getIdFromHash($selectedProduct);
                }
            }
            $products = $products->whereNotIn('products.id', $convertedSelectedProducts);
        }

        $products = $products->where('products.product_type', 'single')->take(8)->get();

        $allProducs = [];

        if ($warehouseId == '') {
            return $allProducs;
        } else {
            $warehouseId = Common::getHashFromId($warehouseId);
        }

        foreach ($products as $product) {
            $productDetails = $product->details;
            $tax = Tax::find($productDetails->tax_id);

            if ($orderType == 'purchases' || $orderType == 'quotations' || ($orderType == 'sales' && $productDetails->current_stock > 0) || ($orderType == 'sales-returns') || ($orderType == 'purchase-returns' && $productDetails->current_stock > 0) || ($orderType == 'stock-transfers' && $productDetails->current_stock > 0)) {
                $stockQuantity = $productDetails->current_stock;
                $unit = $product->unit_id != null ? Unit::find($product->unit_id) : null;

                if ($orderType == 'purchases' || $orderType == 'purchase-returns' || $orderType == 'stock-transfers') {
                    $unitPrice = $productDetails->purchase_price;
                    $taxType = $productDetails->purchase_tax_type;
                } else if ($orderType == 'sales' || $orderType == 'sales-returns' || $orderType == 'quotations') {
                    $unitPrice = $productDetails->sales_price;
                    $taxType = $productDetails->sales_tax_type;
                }

                $singleUnitPrice = $unitPrice;

                if ($tax && $tax->rate != '') {
                    $taxRate = $tax->rate;

                    if ($taxType == 'inclusive') {
                        $subTotal = $singleUnitPrice;
                        $singleUnitPrice =  ($singleUnitPrice * 100) / (100 + $taxRate);
                        $taxAmount = ($singleUnitPrice) * ($taxRate / 100);
                    } else {
                        $taxAmount =  ($singleUnitPrice * ($taxRate / 100));
                        $subTotal = $singleUnitPrice + $taxAmount;
                    }
                } else {
                    $taxAmount = 0;
                    $taxRate = 0;
                    $subTotal = $singleUnitPrice;
                }

                $allProducs[] = [
                    'item_id'    =>  '',
                    'xid'    =>  $product->xid,
                    'name'    =>  $product->name,
                    'image'    =>  $product->image,
                    'image_url'    =>  $product->image_url,
                    'discount_rate'    =>  0,
                    'total_discount'    =>  0,
                    'x_tax_id'    =>  $tax ? $tax->xid : null,
                    'tax_type'    =>  $taxType,
                    'tax_rate'    =>  $taxRate,
                    'total_tax'    =>  $taxAmount,
                    'x_unit_id'    =>  Hashids::encode($product->unit_id),
                    'unit'    =>  $unit,
                    'unit_price'    =>  $unitPrice,
                    'single_unit_price'    =>  $singleUnitPrice,
                    'subtotal'    =>  $subTotal,
                    'quantity'    =>  1,
                    'stock_quantity'    =>  $stockQuantity,
                    'unit_short_name'    =>  $unit ? $unit->short_name : '',
                ];
            }

            // All Type products
            if (!$request->has('order_type')) {
                $allProducs[] = [
                    'xid'    =>  $product->xid,
                    'name'    =>  $product->name,
                    'image'    =>  $product->image,
                    'image_url'    =>  $product->image_url,
                    'stock_quantity'    =>  $productDetails->current_stock,
                ];
            }
        }

        return ApiResponse::make('Fetched Successfully', $allProducs);
    }

    public function getWarehouseStock(Request $request)
    {
        $warehouse = warehouse();
        $stockValue = "-";

        $warehouseId = $warehouse->id;
        $productId = $this->getIdFromHash($request->product_id);

        if ($warehouseId != '' && $productId != '') {
            $stock = ProductDetails::withoutGlobalScope('current_warehouse')
                ->where('warehouse_id', '=', $warehouseId)
                ->where('product_id', '=', $productId)
                ->first();

            if ($stock) {
                $stockValue = $stock->current_stock;
            }
        }

        return ApiResponse::make('Fetched Successfully', ['stock' => $stockValue]);
    }

    public function import(ImportRequest $request)
    {
        if ($request->hasFile('file')) {
            Excel::import(new ProductImport, request()->file('file'));
        }

        return ApiResponse::make('Imported Successfully', []);
    }

    public function checkProductVariant(CheckVariantRequest $request)
    {
        return ApiResponse::make('Added Successfully', []);
    }
}
