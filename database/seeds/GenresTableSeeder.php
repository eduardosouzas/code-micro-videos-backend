<?php

use Illuminate\Database\Seeder;
use App\Models\Genre;
class GenresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Genre::class, 100)->create();
    }
}
