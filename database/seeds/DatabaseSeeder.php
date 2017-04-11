<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        // $this->call(PracticesTableSeeder::class);
        // $this->call(PositionTypesTableSeeder::class);
        // $this->call(GroupsTableSeeder::class);
        // $this->call(DivisionsTableSeeder::class);
        // $this->call(FileTypesTableSeeder::class);
        // $this->call(FileStatusesTableSeeder::class);
    }
}
