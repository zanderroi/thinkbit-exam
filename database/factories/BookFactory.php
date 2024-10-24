<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Book;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

     protected $model = Book::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence,
            'author' => $this->faker->name,
            'cover' => $this->faker->imageUrl(640,480, 'books', true, 'Faker')
        ];
    }
}
