<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\TransactionUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionUserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TransactionUser::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'transaction_id' => Transaction::factory(),
            'category_id' => function() {
                return Category::whereIsSuggestion(true)->inRandomOrder()->first()->id;
            },
            'type' => $this->faker->randomElement(['E','I']),
            'amount' => function(array $attributes) {
                $amount = $this->faker->randomFloat(2,1,10000);
                return ($attributes['type'] === 'E')? -$amount : $amount;
            },
            'is_owner' => true,
        ];
    }
}
