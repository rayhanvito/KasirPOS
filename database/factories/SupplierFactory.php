<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
	/**
	 * The name of the factory's corresponding model.
	 *
	 * @var string
	 */
	protected $model = Supplier::class;

	/**
	 * Define the model's default state.
	 *
	 * @return array
	 */
	public function definition()
	{
		$address = $this->faker->address();

		return [
			'name' => $this->faker->company(),
			'email' => $this->faker->unique()->safeEmail,
			'phone' => $this->faker->e164PhoneNumber(),
			'user_type' => 'suppliers',
			'address' => $address,
			'shipping_address' => $address,
		];
	}
}
