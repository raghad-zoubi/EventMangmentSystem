<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            ['name' => 'place'],
            ['name' => 'restaurant'],
            ['name' => 'clothes'],
            ['name' => 'hairdresser'],
            ['name' => 'singer'],
            ['name' => 'jewelery'],
            ['name' => 'decoration'],
            ['name' => 'photographer'],
            ['name' => 'card'],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
            ]);
        }
    }
}

