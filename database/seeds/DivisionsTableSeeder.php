<?php

use Illuminate\Database\Seeder;

class DivisionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $americanGroup = App\Group::where('name', 'American')->first();
        $nationalGroup = App\Group::where('name', 'National')->first();

        $divisions = [
            [
                'group_id' => $americanGroup->id,
                'name' => 'Tri Star',
                'is_jv' => true,
            ],
            [
                'group_id' => $americanGroup->id,
                'name' => 'Mid America',
                'is_jv' => true,
            ],
            [
                'group_id' => $americanGroup->id,
                'name' => 'Continental',
                'is_jv' => true,
            ],
            [
                'group_id' => $americanGroup->id,
                'name' => 'Blank',
                'is_jv' => true,
            ],
            [
                'group_id' => $americanGroup->id,
                'name' => 'San Antonio',
                'is_jv' => true,
            ],
            [
                'group_id' => $americanGroup->id,
                'name' => 'Gulf Coast',
                'is_jv' => true,
            ],
            [
                'group_id' => $nationalGroup->id,
                'name' => 'Capital',
                'is_jv' => true,
            ],
            [
                'group_id' => $nationalGroup->id,
                'name' => 'North Florida',
                'is_jv' => true,
            ],
            [
                'group_id' => $nationalGroup->id,
                'name' => 'West Florida',
                'is_jv' => true,
            ],
            [
                'group_id' => $nationalGroup->id,
                'name' => 'South Atlantic',
                'is_jv' => false,
            ],
            [
                'group_id' => $nationalGroup->id,
                'name' => 'East Florida',
                'is_jv' => true,
            ],
        ];

        foreach ($divisions as $division) {
            factory(App\Division::class)->create([
                'group_id' => $division['group_id'],
                'name' => $division['name'],
                'code' => null,
                'is_jv' => $division['is_jv'],
            ]);
        }
    }
}
