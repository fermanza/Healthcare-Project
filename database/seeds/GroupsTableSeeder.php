<?php

use Illuminate\Database\Seeder;

class GroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $groups = [
            'American', 'National',
        ];

        foreach ($groups as $group) {
            factory(App\Group::class)->create([
                'name' => $group,
                'code' => null,
            ]);
        }
    }
}
