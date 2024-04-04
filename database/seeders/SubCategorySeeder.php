<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Seeder;

class SubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'name' => 'place',
                'SubCategory' => [
                    ['name' => 'wedding hall'],
                    ['name' => 'park'],
                    ['name' => 'beach'],
                    ['name' => 'hall'],
                ]
            ],
            ['name' => 'restaurant',
                'SubCategory' => [
                    ['name' => 'oriental cuisine'],
                    ['name' => 'western cuisine'],
                    ['name' => 'arab cuisine'],
                    ['name' => 'entrees'],
                    ['name' => 'patsiere'],
                    ['name' => 'cake'],
                    ['name' => 'drinks'],
                ]
            ],
            ['name' => 'clothes',
                'SubCategory' => [
                    ['name' => 'wedding dress'],
                    ['name' => 'men suit'],
                    ['name' => 'dress'],
                    ['name' => 'men shoes'],
                    ['name' => 'women shoes'],
                ]
            ],
            ['name' => 'hairdresser',
                'SubCategory' => [
                    ['name' => 'women hairstyle'],
                    ['name' => 'makeup'],
                    ['name' => 'men hairstyle'],
                ]
            ],
            ['name' => 'singer',
                'SubCategory' => [
                    ['name' => 'team'],
                    ['name' => 'dj'],
                ]
            ],
            ['name' => 'jewelery',
                'SubCategory' => [
                    ['name' => 'accessories'],
                    ['name' => 'jewelery set'],
                ]
            ],
            ['name' => 'decoration',
                'SubCategory' => [
                    ['name' => 'flower'],
                    ['name' => 'balloon'],
                    ['name' => 'other'],
                ]
            ],
            ['name' => 'photographer',
                'SubCategory' => [
                    ['name' => 'photographer'],

                ]
            ],
            ['name' => ' card',
                'SubCategory' => [
                    ['name' => 'call card'],

                ]
            ],
        ];

        // Loops through CategoryOne
        foreach ($data as $key => $value) {
            $categoryOne = Category::where('name', $value['name'])->first();

            if ($categoryOne instanceof Category) {
                if (isset($value['SubCategory']) && is_array($value['SubCategory'])) {
                    // Loops through CategoryTwo
                    foreach ($value['SubCategory'] as $categoryTwo) {
                        $categoryTwo['category_id'] = $categoryOne->id;
                        SubCategory::firstOrCreate($categoryTwo);
                    }
                }
            }
        }

    }
}
