<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $brands = ['Logitech', 'Apple', 'Dell', 'Samsung', 'Lenovo', 'Razer', 'HP'];

        $categoriesMap = [
            'laptop' => ['laptop', 'notebook', 'ultrabook'],
            'mouse' => ['mouse', 'pointer'],
            'keyboard' => ['keyboard', 'keypad'],
            'monitor' => ['monitor', 'display', 'screen'],
            'headset' => ['headset', 'headphones'],
        ];

        $adjectives = [
            'gaming', 'office', 'wireless', 'mechanical',
            'ergonomic', 'ultra', 'portable'
        ];

        $noiseWords = [
            'pro', 'max', 'plus', '2024', 'edition', 'rgb', 'usb-c'
        ];

        // --- helpers ---
        $typo = function ($word) {
            if (fake()->boolean(20)) {
                return substr($word, 0, max(1, strlen($word) - 1));
            }
            return $word;
        };

        $brand = fake()->randomElement($brands);

        $baseCategory = fake()->randomElement(array_keys($categoriesMap));
        $category = fake()->randomElement($categoriesMap[$baseCategory]);

        // иногда добавляем второй конфликтующий смысл
        $secondaryCategory = fake()->optional(0.3)->randomElement(
            $categoriesMap[fake()->randomElement(array_keys($categoriesMap))]
        );

        $titleParts = array_filter([
            $typo($brand),
            fake()->optional(0.7)->randomElement($adjectives),
            $typo($category),
            $secondaryCategory,
            fake()->optional(0.5)->randomElement($noiseWords),
            fake()->optional(0.3)->words(2, true),
        ]);

        $descriptionParts = [
            $brand,
            $category,
            fake()->randomElement($adjectives),

            // длинные тексты
            fake()->paragraph(),
            fake()->paragraph(),

            // немного мусора
            fake()->optional(0.5)->words(10, true),

            // добавим альтернативные формулировки
            fake()->optional(0.5)->randomElement($categoriesMap[$baseCategory]),

            // случайное предложение
            fake()->sentence(),
        ];

        return [
            'title' => implode(' ', $titleParts),
            'description' => implode(' ', $descriptionParts),
            'price' => fake()->randomFloat(2, 10, 3000),
        ];
    }
}
