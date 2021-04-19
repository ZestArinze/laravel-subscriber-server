<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $title = $this->faker->words($this->faker->randomElement([4,5,6]), true);

        return [
            'title'     => $title,
            'body'      => $this->faker->paragraph($this->faker->randomElement([1,2])),
            'slug'      => Str::slug($title),
        ];
    }
}
