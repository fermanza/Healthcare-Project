<?php

use Illuminate\Database\Seeder;

class PositionTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $positionTypes = [
            'Manager', 'Recruiter', 'Director of Recruiting',
        ];

        foreach ($positionTypes as $positionType) {
            factory(App\PositionType::class)->create([
                'name' => $positionType,
            ]);
        }
    }
}
