<?php

namespace Database\Seeders;

use App\Classes\Common;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\Payment;
use App\Models\PaymentMode;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentsTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */

	public function run()
	{
		Model::unguard();

		DB::table('payments')->delete();
		DB::table('order_payments')->delete();

		DB::statement('ALTER TABLE payments AUTO_INCREMENT = 1');
		DB::statement('ALTER TABLE order_payments AUTO_INCREMENT = 1');


		$allWarehouses = Warehouse::all();
		$paymentTypes = PaymentMode::select('id')->pluck('id')->toArray();

		foreach ($allWarehouses as $allWarehouse) {
			$this->makePurchasesPayments($allWarehouse, $paymentTypes);
			$this->makeSalesPayments($allWarehouse, $paymentTypes);
		}
	}

	public function makePurchasesPayments($warehouse, $paymentTypes)
	{
		$orderType = "purchases";
		$paymentType = $orderType == 'purchases' || $orderType == 'sales-returns' ? "out" : "in";

		$this->seedPayment('random', $orderType, $warehouse, $paymentType, $paymentTypes);
		$this->seedPayment('full', $orderType, $warehouse, $paymentType, $paymentTypes);
		// TODO - break payments in installment i.e. $1400 will $300+$700+$400
	}

	public function makeSalesPayments($warehouse, $paymentTypes)
	{
		$orderType = "sales";
		$paymentType = $orderType == 'purchases' || $orderType == 'sales-returns' ? "out" : "in";

		$this->seedPayment('random', $orderType, $warehouse, $paymentType, $paymentTypes);
		$this->seedPayment('full', $orderType, $warehouse, $paymentType, $paymentTypes);
		// TODO - break payments in installment i.e. $1400 will $300+$700+$400
	}

	public function seedPayment($paymentForm, $orderType, $warehouse, $paymentType, $paymentTypes)
	{
		$faker = \Faker\Factory::create();

		$allPendingOrders = Order::where('warehouse_id', $warehouse->id)
			->where('order_type', $orderType)
			->where('payment_status', 'unpaid')
			->inRandomOrder()
			->take($faker->numberBetween(5, 8))
			->get();

		foreach ($allPendingOrders as $allPendingOrder) {
			$date = $faker->dateTimeBetween($allPendingOrder->order_date, 'now');
			if ($paymentForm == "random") {
				$amount = $faker->numberBetween(1, $allPendingOrder->total);
			} else if ($paymentForm == "full") {
				$amount = $allPendingOrder->total;
			}

			$newPayment = new Payment();
			$newPayment->warehouse_id = $warehouse->id;
			$newPayment->payment_type = $paymentType;
			$newPayment->date = $date;
			$newPayment->amount = $amount;
			$newPayment->unused_amount = 0;
			$newPayment->paid_amount = $amount;
			$newPayment->payment_mode_id = $faker->randomElement($paymentTypes);
			$newPayment->user_id = $allPendingOrder->user_id;
			$newPayment->notes = "";
			$newPayment->save();

			// Saving Payment Number
			$newPayment->payment_number = Common::getTransactionNumber('payment-' . $paymentType, $newPayment->id);
			$newPayment->save();

			$newOrderPayment = new OrderPayment();
			$newOrderPayment->payment_id = $newPayment->id;
			$newOrderPayment->order_id = $allPendingOrder->id;
			$newOrderPayment->amount = $newPayment->amount;
			$newOrderPayment->save();

			// Updating Warehouse History
			Common::updateWarehouseHistory('payment', $newPayment, "add_edit");

			Common::updateOrderAmount($newOrderPayment->order_id);
		}
	}
}
