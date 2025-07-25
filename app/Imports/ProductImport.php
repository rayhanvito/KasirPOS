<?php

namespace App\Imports;

use App\Classes\Common;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductDetails;
use App\Models\Tax;
use App\Models\Unit;
use App\Models\Warehouse;
use Examyou\RestAPI\Exceptions\ApiException;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToArray;
use Illuminate\Support\Str;

class ProductImport implements ToArray, WithHeadingRow
{
    public function array(array $products)
    {
        DB::transaction(function () use ($products) {
            $user = user();

            foreach ($products as $product) {

                if (
                    !array_key_exists('name', $product) || !array_key_exists('barcode_symbology', $product) || !array_key_exists('item_code', $product) || !array_key_exists('description', $product) ||
                    !array_key_exists('category', $product) || !array_key_exists('brand', $product) || !array_key_exists('unit', $product) || !array_key_exists('tax', $product) ||
                    !array_key_exists('mrp', $product) || !array_key_exists('purchase_price', $product) || !array_key_exists('sales_price', $product) || !array_key_exists('purchase_tax_type', $product) ||
                    !array_key_exists('sales_tax_type', $product) || !array_key_exists('stock_quantitiy_alert', $product) || !array_key_exists('opening_stock', $product) || !array_key_exists('opening_stock_date', $product) ||
                    !array_key_exists('wholesale_price', $product) || !array_key_exists('wholesale_quantity', $product)
                ) {
                    throw new ApiException('Field missing from header.');
                }

                $productName = trim($product['name']);
                $productCount = Product::where('name', $productName)->count();
                if ($productCount > 0) {
                    throw new ApiException('Product ' . $productName . ' Already Exists');
                }

                // Category
                $categoryName = trim($product['category']);
                $category = Category::where('name', $categoryName)->first();
                if (!$category) {
                    throw new ApiException('Category Not Found');
                }

                // Brand
                $brandName = trim($product['brand']);
                $brand = Brand::where('name', $brandName)->first();
                if (!$brand) {
                    throw new ApiException('Brand Not Found');
                }

                // Unit
                $unitName = trim($product['unit']);
                $unit = Unit::where('name', $unitName)->first();
                if (!$unit) {
                    throw new ApiException('Unit Not Found');
                }

                $barcodeSymbology = trim($product['barcode_symbology']);
                if ($barcodeSymbology == "" || !in_array($barcodeSymbology, ['CODE128', 'CODE39'])) {
                    throw new ApiException('Barcode symoblogy must be CODE128 or CODE39');
                }

                $itemCode = trim($product['item_code']);
                $isItemCodeAlreadyExists = Product::where('item_code', $itemCode)->count();
                if ($isItemCodeAlreadyExists > 0) {
                    throw new ApiException('Item Code ' . $itemCode . ' Already Exists');
                }

                // Product Details

                // Tax
                $taxName = trim($product['tax']);
                if ($taxName != "") {
                    $tax = Tax::where('name', $taxName)->first();

                    if (!$tax) {
                        throw new ApiException('Tax Not Found');
                    }
                }

                $purchaseTaxType = trim($product['purchase_tax_type']);
                if ($taxName != "" && !in_array($purchaseTaxType, ['exclusive', 'inclusive', 'Inclusive', 'Exclusive'])) {
                    throw new ApiException('Purchase Tax Type must be inclusive or exclusive');
                }

                $salesTaxType = trim($product['sales_tax_type']);
                if ($taxName != "" && !in_array($salesTaxType, ['exclusive', 'inclusive', 'Inclusive', 'Exclusive'])) {
                    throw new ApiException('Sales Tax Type must be inclusive or exclusive');
                }

                $openingStockDate = trim($product['opening_stock_date']);
                $stockQuantityAlert = trim($product['stock_quantitiy_alert']);
                $openingStock = trim($product['opening_stock']);
                $openingStockDate = trim($product['opening_stock_date']);
                $wholesaleQuantity = trim($product['wholesale_quantity']);
                $wholesalePrice = trim($product['wholesale_price']);
                $allWarehouses = Warehouse::select('id')->get();

                if (array_key_exists('warehouse', $product)) {
                    $warehouse = Warehouse::where('name', $product['warehouse'])->first();
                    $currentWarehouse = warehouse();

                    $createdWarehouseId = $warehouse && $warehouse->id ? $warehouse->id : $currentWarehouse->id;
                } else {
                    $warehouse = warehouse();
                    $createdWarehouseId = $warehouse->id;
                }

                $newProduct = new Product();
                $newProduct->name = $productName;
                $newProduct->warehouse_id = $createdWarehouseId;
                $newProduct->slug = Str::slug($productName, '-');
                $newProduct->barcode_symbology = $barcodeSymbology;
                // $newProduct->item_code = (int) $itemCode;
                $newProduct->item_code = $itemCode;
                $newProduct->category_id = $category->id;
                $newProduct->brand_id = $brand->id;
                $newProduct->unit_id = $unit->id;
                $newProduct->user_id = $user->id;
                $newProduct->save();


                foreach ($allWarehouses as $allWarehouse) {
                    $newProductDetails = new ProductDetails();
                    $newProductDetails->warehouse_id = $allWarehouse->id;
                    $newProductDetails->product_id = $newProduct->id;
                    $newProductDetails->tax_id = $taxName == "" ? null : $tax->id;
                    $newProductDetails->purchase_tax_type = $purchaseTaxType != '' ? lcfirst($purchaseTaxType) : 'exclusive';
                    $newProductDetails->sales_tax_type = $salesTaxType != '' ? lcfirst($salesTaxType) : 'exclusive';
                    $newProductDetails->mrp = trim($product['mrp']);
                    $newProductDetails->purchase_price = trim($product['purchase_price']);
                    $newProductDetails->sales_price = trim($product['sales_price']);
                    $newProductDetails->stock_quantitiy_alert = $stockQuantityAlert != "" ? (int) $stockQuantityAlert : null;
                    $newProductDetails->opening_stock = $openingStock != "" ? (int) $openingStock : null;
                    $newProductDetails->opening_stock_date = $openingStockDate != "" ? $openingStockDate : null;
                    $newProductDetails->wholesale_price = $wholesalePrice != "" ? $wholesalePrice : null;
                    $newProductDetails->wholesale_quantity = $wholesaleQuantity == "" ? null : $wholesaleQuantity;
                    $newProductDetails->save();

                    Common::recalculateOrderStock($newProductDetails->warehouse_id, $newProduct->id);
                }
            }
        });
    }
}
