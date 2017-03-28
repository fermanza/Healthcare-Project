<?php

use Illuminate\Database\Seeder;

class PracticesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $practices = [
            'abc', 'ED', 'ED - PED', 'IPS', 'IPS - ANES', 'IPS - NEURO',
            'IPS - PED', 'IPS - PERI', 'IPS - PSY', 'IPS - RAD',
        ];

        foreach ($practices as $practice) {
            factory(App\Practice::class)->create([
                'name' => $practice,
                'code' => null,
            ]);
        }
    }
}
