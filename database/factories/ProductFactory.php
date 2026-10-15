<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'category' => fake()->randomElement(Product::CATEGORIES ? array_keys(Product::CATEGORIES) : ['CEMILAN']),
            'description' => fake()->paragraph(),
            'price' => fake()->numberBetween(5_000, 100_000),
            'badge' => fake()->randomElement(['best_seller', 'baru', 'pedas', null]),
            'image_path' => null,
            'whatsapp_order_link' => null,
            'rating' => fake()->randomFloat(1, 4.0, 5.0),
            'sold' => fake()->numberBetween(0, 500),
            'available' => true,
            'stock' => fake()->numberBetween(0, 200),
            'min_stock' => 10,
        ];
    }

    public function unavailable(): static
    {
        return $this->state(fn (array $attributes) => [
            'available' => false,
        ]);
    }

    public function lowStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => 3,
        ]);
    }

    public function bestSeller(): static
    {
        return $this->state(fn (array $attributes) => [
            'sold' => 1000,
            'badge' => 'best_seller',
        ]);
    }
}