<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpenseTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */

	public function run()
	{
		Model::unguard();

		DB::table('expense_categories')->delete();
		DB::table('expenses')->delete();

		DB::statement('ALTER TABLE expense_categories AUTO_INCREMENT = 1');
		DB::statement('ALTER TABLE expenses AUTO_INCREMENT = 1');

		$expenseCategoryUtitlty = new ExpenseCategory();
		$expenseCategoryUtitlty->name = "Utilities";
		$expenseCategoryUtitlty->description = "Utilities such as electricity, internet, sewage and trash pickup fees are fully deductible.";
		$expenseCategoryUtitlty->save();

		$expenseCategoryPrinting = new ExpenseCategory();
		$expenseCategoryPrinting->name = "Printing";
		$expenseCategoryPrinting->description = "Items such as ink cartridges, printers or payments for printing services can be included under this expense category.";
		$expenseCategoryPrinting->save();

		$expenseCategoryGrocery = new ExpenseCategory();
		$expenseCategoryGrocery->name = "Grocery";
		$expenseCategoryGrocery->description = "Grocery related items will comes under this category like tea, snakes etc.";
		$expenseCategoryGrocery->save();

		$expenseCategoryTravel = new ExpenseCategory();
		$expenseCategoryTravel->name = "Travel";
		$expenseCategoryTravel->description = "Travel related expenses covered under this category";
		$expenseCategoryTravel->save();

		$faker = \Faker\Factory::create();
		$warehouses = Warehouse::all();

		foreach ($warehouses as $warehouse) {

			$exp1 = $faker->randomElement([true, false]);
			if ($exp1) {
				$expense = new Expense();
				$expense->expense_category_id = $expenseCategoryUtitlty->id;
				$expense->warehouse_id = $warehouse->id;
				$expense->amount = $faker->numberBetween(20, 50);
				$expense->notes = "Surf, Soap, Brush etc.";
				$expense->date = $faker->dateTimeBetween("-3 week", 'now');
				$expense->user_id = User::where('warehouse_id', $warehouse->id)->inRandomOrder()->first()->id;
				$expense->save();
			}

			$exp2 = $faker->randomElement([true, false]);
			if ($exp2) {
				$expense = new Expense();
				$expense->expense_category_id = $expenseCategoryTravel->id;
				$expense->warehouse_id = $warehouse->id;
				$expense->amount = $faker->numberBetween(20, 50);
				$expense->notes = "Bike and car petrol expenses.";
				$expense->date = $faker->dateTimeBetween("-3 week", 'now');
				$expense->user_id = User::where('warehouse_id', $warehouse->id)->inRandomOrder()->first()->id;
				$expense->save();
			}

			$exp3 = $faker->randomElement([true, false]);
			if ($exp3) {
				$expense = new Expense();
				$expense->expense_category_id = $expenseCategoryGrocery->id;
				$expense->warehouse_id = $warehouse->id;
				$expense->amount = $faker->numberBetween(20, 50);
				$expense->notes = "Biscikt, cold drink, snacks.";
				$expense->date = $faker->dateTimeBetween("-3 week", 'now');
				$expense->user_id = User::where('warehouse_id', $warehouse->id)->inRandomOrder()->first()->id;
				$expense->save();
			}

			$exp4 = $faker->randomElement([true, false]);
			if ($exp4) {
				$expense = new Expense();
				$expense->expense_category_id = $expenseCategoryPrinting->id;
				$expense->warehouse_id = $warehouse->id;
				$expense->amount = $faker->numberBetween(20, 50);
				$expense->notes = "Ink, Cartage etc.";
				$expense->date = $faker->dateTimeBetween("-3 week", 'now');
				$expense->user_id = User::where('warehouse_id', $warehouse->id)->inRandomOrder()->first()->id;
				$expense->save();
			}
		}
	}
}
