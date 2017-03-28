<?php

use Illuminate\Database\Seeder;

class DummyEmployeesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Employee::class, 20)->create([
            'type' => 'Alliance',
            'is_full_time' => true,
        ]);
    }
}
