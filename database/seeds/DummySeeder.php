<?php

use Illuminate\Database\Seeder;

class DummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(DummyPersonsSeeder::class);
        $this->call(DummyEmployeesSeeder::class);
        $this->call(DummyAccountsSeeder::class);
    }
}
