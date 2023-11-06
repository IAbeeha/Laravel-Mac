<?php

namespace Database\Seeders;
use App\Models\Post; 
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $faker->title
        // $faker->text
        Post::factory()
        ->times(2500)
        ->create();
        //
    }
}
