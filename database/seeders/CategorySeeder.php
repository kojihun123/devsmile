<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = ['감정류', '음료류', '인생류', '에러류'];

        foreach ($categories as $name) {
            \App\Models\Category::create(['name' => $name]);
        }
    }
}
