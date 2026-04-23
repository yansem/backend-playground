<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::factory()->count(20000)->create();


        //Тест-запросы:
        //macbook pro
        //apple laptop pro
        //
        //👉 Что увидишь:
        //
        //ES: правильный order (Pro > Air > iPad)
        //PG FTS: часто просто совпадения без нормального ranking
        DB::table('products')->insert([
            [
                'title' => 'Apple MacBook Pro 16 M3 Max',
                'description' => 'Professional laptop for video editing and software development',
                'price' => 2999.99,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'MacBook Air 13 M2',
                'description' => 'Lightweight laptop for everyday tasks',
                'price' => 1299.99,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Apple iPad Pro 12.9',
                'description' => 'Tablet with M2 chip, often used instead of laptop',
                'price' => 1199.99,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        //Тесты:
        //gaming mouse
        //wireless gaming mouse
        //
        //👉 ES:
        //
        //“Gaming Mouse” (mouse product) выше
        //phrase boosting заметен
        //
        //PG:
        //
        //часто “keyboard” и “mouse pad” могут смешиваться по rank
        DB::table('products')->insert([
            [
                'title' => 'Wireless Gaming Mouse Logitech G Pro',
                'description' => 'High precision esports mouse',
                'price' => 149.99,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Gaming Mouse Pad Large',
                'description' => 'Surface for mouse movement',
                'price' => 19.99,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Wireless Keyboard Logitech MX Keys',
                'description' => 'Typing keyboard for office and gaming',
                'price' => 99.99,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        //Тест:
        //27 monitor usb c
        //
        //👉 ES:
        //
        //вытаскивает Dell выше из-за term distribution
        //👉 PG:
        //шум часто ломает ranking
        DB::table('products')->insert([
            [
                'title' => 'Dell UltraSharp 27 Monitor U2723QE',
                'description' => 'Professional display for designers. Lorem ipsum dolor sit amet consectetur adipiscing elit ultricies monitor display screen resolution 4K IPS USB-C docking hub',
                'price' => 699.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Generic 27 inch Monitor',
                'description' => 'Cheap display screen office use random words lorem ipsum junk text monitor',
                'price' => 199.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        //Тест:
        //lenovo laptop stand
        //macbook stand lenovo
        //
        //👉 ES:
        //
        //понимает “stand” как основной intent
        //распределяет score лучше
        DB::table('products')->insert([
            [
                'title' => 'Laptop Stand Adjustable Aluminum',
                'description' => 'Ergonomic stand for MacBook, Dell, Lenovo',
                'price' => 39.99,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Lenovo ThinkPad X1 Carbon Laptop',
                'description' => 'Business ultrabook lightweight portable',
                'price' => 1799.99,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        //Тест:
        //apple mouse gaming
        //
        //👉 ES:
        //
        //magic mouse vs gaming mouse separation по score
        //👉 PG:
        //часто смешивание
        DB::table('products')->insert([
            [
                'title' => 'Apple Magic Mouse',
                'description' => 'Wireless mouse for Mac devices',
                'price' => 79.99,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Gaming Mouse Razer DeathAdder',
                'description' => 'High DPI esports mouse',
                'price' => 59.99,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Mouse Pad RGB Gaming XL',
                'description' => 'Large gaming surface with RGB lighting',
                'price' => 29.99,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        //Тест:
        //best 27 inch gaming monitor 240hz curved
        //
        //👉 ES:
        //
        //собирает intent из разных токенов
        //👉 PG:
        //обычно ломается
        DB::table('products')->insert([
            [
                'title' => 'Samsung Odyssey G7 27 Gaming Monitor',
                'description' => '240Hz curved display for competitive gaming',
                'price' => 599.99,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Budget 27 inch Office Monitor',
                'description' => 'Simple display for work spreadsheets and browsing',
                'price' => 149.99,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        //hedset arctis nova
        //hyperx clud
        //👉 ES:
        //
        //AUTO fuzziness стабильно работает
        //👉 PG:
        //зависит от trigram (если не включён — хуже)
        DB::table('products')->insert([
            [
                'title' => 'HyperX Cloud II Headset',
                'description' => 'Gaming headphones with surround sound',
                'price' => 89.99,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'SteelSeries Arctis Nova 7',
                'description' => 'Wireless gaming headset multi platform',
                'price' => 159.99,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
