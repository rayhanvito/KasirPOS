<?php

namespace Database\Seeders;

use App\Classes\Common;
use App\Classes\PermsSeed;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Permission;
use App\Models\Product;
use App\Models\ProductDetails;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        Model::unguard();

        DB::table('orders')->delete();
        DB::table('order_items')->delete();
        DB::table('warehouse_history')->delete();

        DB::statement('ALTER TABLE `orders` AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE `order_items` AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE `warehouse_history` AUTO_INCREMENT = 1');


        $allWarehouses = Warehouse::all();

        // Purchase, Purchase Returns
        $this->createPurchases($allWarehouses);
        $this->createPurchasesReturns($allWarehouses);

        // Sales, Sales Return
        $this->createSales($allWarehouses);
        $this->createSalesReturn($allWarehouses);
    }

    public function createPurchases($allWarehouses)
    {
        $faker = \Faker\Factory::create();

        foreach ($allWarehouses as $allWarehouse) {
            for ($i = 0; $i < $faker->numberBetween(30, 40); $i++) {
                $randomSupplier = Supplier::inRandomOrder()->first();
                $randomStaffUser = User::where('warehouse_id', $allWarehouse->id)->inRandomOrder()->first();

                $purchaseOrder = new Order();
                $purchaseOrder->unique_id = Common::generateOrderUniqueId();
                $purchaseOrder->order_type = 'purchases';
                $purchaseOrder->invoice_number = "";
                $purchaseOrder->order_date = $faker->dateTimeBetween('-2 week', 'now');
                $purchaseOrder->warehouse_id = $allWarehouse->id;
                $purchaseOrder->user_id = $randomSupplier->id;
                $purchaseOrder->staff_user_id = $randomStaffUser->id;
                $purchaseOrder->subtotal = 0;
                $purchaseOrder->total = 0;
                $purchaseOrder->order_status = $faker->randomElement(['received', 'pending', 'ordered']);
                $purchaseOrder->save();

                $purchaseOrder->invoice_number = Common::getTransactionNumber($purchaseOrder->order_type, $purchaseOrder->id);
                $purchaseOrder->save();

                $randomProducts = Product::inRandomOrder()->take($faker->numberBetween(1, 4))->get();
                $productItems = [];
                $orderTotal = 0;
                foreach ($randomProducts as $randomProduct) {
                    $productDetails = ProductDetails::withoutGlobalScope('current_warehouse')
                        ->where('warehouse_id', $allWarehouse->id)
                        ->where('product_id', $randomProduct->id)
                        ->first();

                    $quantity = $faker->numberBetween(1, 9);
                    $purchasePrice = $productDetails->purchase_price;
                    $subtotal = $quantity * $purchasePrice;

                    $productItems[] = [
                        'item_id' => null,
                        'xid' => $randomProduct->xid,
                        'user_id' => $randomSupplier->xid,
                        'quantity' => $quantity,
                        'product_id' => $randomProduct->xid,
                        'unit_price' => $purchasePrice,
                        'single_unit_price' => $purchasePrice,
                        'unit_id' => $randomProduct->unit_id,
                        'x_unit_id' => Common::getHashFromId($randomProduct->unit_id),
                        'subtotal' => $subtotal,
                        'tax_rate' => 0,
                        'discount_rate' => 0,
                        'total_discount' => 0,
                        'total_tax' => 0,
                        'tax_type' => 'inclusive',
                    ];

                    $orderTotal += $subtotal;
                }

                $purchaseOrder->total = $orderTotal;
                $purchaseOrder->save();

                $purchaseOrder = Common::storeAndUpdateOrderItem($purchaseOrder, $productItems);

                // Updating Warehouse History
                Common::updateWarehouseHistory('order', $purchaseOrder, "add_edit");
            }
        }
    }

    public function createPurchasesReturns($allWarehouses)
    {
        $faker = \Faker\Factory::create();

        foreach ($allWarehouses as $allWarehouse) {
            for ($i = 0; $i < $faker->numberBetween(10, 20); $i++) {
                $randomSupplier = Supplier::inRandomOrder()->first();
                $randomStaffUser = User::where('warehouse_id', $allWarehouse->id)->inRandomOrder()->first();

                $purchaseOrder = new Order();
                $purchaseOrder->unique_id = Common::generateOrderUniqueId();
                $purchaseOrder->order_type = 'purchase-returns';
                $purchaseOrder->invoice_number = "";
                $purchaseOrder->order_date = $faker->dateTimeBetween('-1 week', 'now');
                $purchaseOrder->warehouse_id = $allWarehouse->id;
                $purchaseOrder->user_id = $randomSupplier->id;
                $purchaseOrder->staff_user_id = $randomStaffUser->id;
                $purchaseOrder->subtotal = 0;
                $purchaseOrder->total = 0;
                $purchaseOrder->order_status = $faker->randomElement(['completed', 'pending']);
                $purchaseOrder->save();

                $purchaseOrder->invoice_number = Common::getTransactionNumber($purchaseOrder->order_type, $purchaseOrder->id);
                $purchaseOrder->save();

                $randomProducts = Product::inRandomOrder()->take($faker->numberBetween(1, 4))->get();
                $productItems = [];
                $orderTotal = 0;
                foreach ($randomProducts as $randomProduct) {
                    $productDetails = ProductDetails::withoutGlobalScope('current_warehouse')
                        ->where('warehouse_id', $allWarehouse->id)
                        ->where('product_id', $randomProduct->id)
                        ->first();

                    $quantity = $faker->numberBetween(1, 9);
                    $purchasePrice = $productDetails->purchase_price;
                    $subtotal = $quantity * $purchasePrice;

                    $productItems[] = [
                        'item_id' => null,
                        'xid' => $randomProduct->xid,
                        'user_id' => $randomSupplier->xid,
                        'quantity' => $quantity,
                        'product_id' => $randomProduct->xid,
                        'unit_price' => $purchasePrice,
                        'single_unit_price' => $purchasePrice,
                        'unit_id' => $randomProduct->unit_id,
                        'x_unit_id' => Common::getHashFromId($randomProduct->unit_id),
                        'subtotal' => $subtotal,
                        'tax_rate' => 0,
                        'discount_rate' => 0,
                        'total_discount' => 0,
                        'total_tax' => 0,
                        'tax_type' => 'inclusive',
                    ];

                    $orderTotal += $subtotal;
                }

                $purchaseOrder->total = $orderTotal;
                $purchaseOrder->save();

                $purchaseOrder = Common::storeAndUpdateOrderItem($purchaseOrder, $productItems);

                // Updating Warehouse History
                Common::updateWarehouseHistory('order', $purchaseOrder, "add_edit");
            }
        }
    }

    public function createSales($allWarehouses)
    {
        $faker = \Faker\Factory::create();

        foreach ($allWarehouses as $allWarehouse) {
            for ($i = 0; $i < $faker->numberBetween(20, 40); $i++) {
                $randomCustomer = Customer::inRandomOrder()->first();
                $randomStaffUser = User::where('warehouse_id', $allWarehouse->id)->inRandomOrder()->first();

                $purchaseOrder = new Order();
                $purchaseOrder->unique_id = Common::generateOrderUniqueId();
                $purchaseOrder->order_type = 'sales';
                $purchaseOrder->invoice_number = "";
                $purchaseOrder->order_date = $faker->dateTimeBetween('-2 week', 'now');
                $purchaseOrder->warehouse_id = $allWarehouse->id;
                $purchaseOrder->user_id = $randomCustomer->id;
                $purchaseOrder->staff_user_id = $randomStaffUser->id;
                $purchaseOrder->subtotal = 0;
                $purchaseOrder->total = 0;
                $purchaseOrder->order_status = $faker->randomElement(['ordered', 'confirmed', 'processing', 'shipping', 'delivered']);
                $purchaseOrder->save();

                $purchaseOrder->invoice_number = Common::getTransactionNumber($purchaseOrder->order_type, $purchaseOrder->id);
                $purchaseOrder->save();

                $randomProducts = Product::inRandomOrder()->take($faker->numberBetween(1, 4))->get();
                $productItems = [];
                $orderTotal = 0;
                foreach ($randomProducts as $randomProduct) {
                    $productDetails = ProductDetails::withoutGlobalScope('current_warehouse')
                        ->where('warehouse_id', $allWarehouse->id)
                        ->where('product_id', $randomProduct->id)
                        ->first();

                    $quantity = $faker->numberBetween(1, 9);
                    $salesPrice = $productDetails->sales_price;
                    $subtotal = $quantity * $salesPrice;

                    $productItems[] = [
                        'item_id' => null,
                        'xid' => $randomProduct->xid,
                        'user_id' => $randomCustomer->xid,
                        'quantity' => $quantity,
                        'product_id' => $randomProduct->xid,
                        'unit_price' => $salesPrice,
                        'single_unit_price' => $salesPrice,
                        'unit_id' => $randomProduct->unit_id,
                        'x_unit_id' => Common::getHashFromId($randomProduct->unit_id),
                        'subtotal' => $subtotal,
                        'tax_rate' => 0,
                        'discount_rate' => 0,
                        'total_discount' => 0,
                        'total_tax' => 0,
                        'tax_type' => 'inclusive',
                    ];

                    $orderTotal += $subtotal;
                }

                $purchaseOrder->total = $orderTotal;
                $purchaseOrder->save();

                $purchaseOrder = Common::storeAndUpdateOrderItem($purchaseOrder, $productItems);

                // Updating Warehouse History
                Common::updateWarehouseHistory('order', $purchaseOrder, "add_edit");
            }
        }
    }

    public function createSalesReturn($allWarehouses)
    {
        $faker = \Faker\Factory::create();

        foreach ($allWarehouses as $allWarehouse) {
            for ($i = 0; $i < $faker->numberBetween(10, 20); $i++) {
                $randomCustomer = Customer::inRandomOrder()->first();
                $randomStaffUser = User::where('warehouse_id', $allWarehouse->id)->inRandomOrder()->first();

                $purchaseOrder = new Order();
                $purchaseOrder->unique_id = Common::generateOrderUniqueId();
                $purchaseOrder->order_type = 'sales-returns';
                $purchaseOrder->invoice_number = "";
                $purchaseOrder->order_date = $faker->dateTimeBetween('-2 week', 'now');
                $purchaseOrder->warehouse_id = $allWarehouse->id;
                $purchaseOrder->user_id = $randomCustomer->id;
                $purchaseOrder->staff_user_id = $randomStaffUser->id;
                $purchaseOrder->subtotal = 0;
                $purchaseOrder->total = 0;
                $purchaseOrder->order_status = $faker->randomElement(['received', 'pending']);
                $purchaseOrder->save();

                $purchaseOrder->invoice_number = Common::getTransactionNumber($purchaseOrder->order_type, $purchaseOrder->id);
                $purchaseOrder->save();

                $randomProducts = Product::inRandomOrder()->take($faker->numberBetween(1, 4))->get();
                $productItems = [];
                $orderTotal = 0;
                foreach ($randomProducts as $randomProduct) {
                    $productDetails = ProductDetails::withoutGlobalScope('current_warehouse')
                        ->where('warehouse_id', $allWarehouse->id)
                        ->where('product_id', $randomProduct->id)
                        ->first();

                    $quantity = $faker->numberBetween(1, 9);
                    $salesPrice = $productDetails->sales_price;
                    $subtotal = $quantity * $salesPrice;

                    $productItems[] = [
                        'item_id' => null,
                        'xid' => $randomProduct->xid,
                        'user_id' => $randomCustomer->xid,
                        'quantity' => $quantity,
                        'product_id' => $randomProduct->xid,
                        'unit_price' => $salesPrice,
                        'single_unit_price' => $salesPrice,
                        'unit_id' => $randomProduct->unit_id,
                        'x_unit_id' => Common::getHashFromId($randomProduct->unit_id),
                        'subtotal' => $subtotal,
                        'tax_rate' => 0,
                        'discount_rate' => 0,
                        'total_discount' => 0,
                        'total_tax' => 0,
                        'tax_type' => 'inclusive',
                    ];

                    $orderTotal += $subtotal;
                }

                $purchaseOrder->total = $orderTotal;
                $purchaseOrder->save();

                $purchaseOrder = Common::storeAndUpdateOrderItem($purchaseOrder, $productItems);

                // Updating Warehouse History
                Common::updateWarehouseHistory('order', $purchaseOrder, "add_edit");
            }
        }
    }
}
