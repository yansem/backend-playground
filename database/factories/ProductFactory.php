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

        // канонические категории
        $categories = [
            'laptops' => ['laptop', 'notebook', 'ultrabook'],
            'mice' => ['mouse', 'pointer'],
            'keyboards' => ['keyboard', 'keypad'],
            'monitors' => ['monitor', 'display', 'screen'],
            'headsets' => ['headset', 'headphones'],
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

        // ключ = нормализованная категория
        $categorySlug = fake()->randomElement(array_keys($categories));

        // синоним для текста
        $categoryWord = fake()->randomElement($categories[$categorySlug]);

        // иногда добавляем второй шумный смысл
        $secondaryCategory = fake()->optional(0.3)->randomElement(
            $categories[fake()->randomElement(array_keys($categories))]
        );

        $titleParts = array_filter([
            $typo($brand),
            fake()->optional(0.7)->randomElement($adjectives),
            $typo($categoryWord),
            $secondaryCategory,
            fake()->optional(0.5)->randomElement($noiseWords),
            fake()->optional(0.3)->words(2, true),
        ]);

        $descriptionParts = [
            $brand,
            $categoryWord,
            fake()->randomElement($adjectives),

            fake()->paragraph(),
            fake()->paragraph(),

            fake()->optional(0.5)->words(10, true),

            // добавляем синонимы в текст
            fake()->optional(0.5)->randomElement($categories[$categorySlug]),

            fake()->sentence(),
        ];

        return [
            'title' => implode(' ', $titleParts),
            'description' => implode(' ', $descriptionParts),
            'price' => fake()->randomFloat(2, 10, 3000),

            // ключевые поля для фильтрации
            'category' => ucfirst($categorySlug), // "Laptops"
            'category_slug' => $categorySlug,     // "laptops"
        ];
    }
}
