<?php

use Illuminate\Database\Seeder;

class DummyFilesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\File::class, 20)->create();
    }
}