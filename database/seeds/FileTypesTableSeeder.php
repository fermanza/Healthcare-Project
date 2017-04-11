<?php

use Illuminate\Database\Seeder;

class FileTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $fileTypes = [
            'Increased Comp',
            'Shifts',
            'Interviews & Applications',
        ];

        foreach ($fileTypes as $fileType) {
            factory(App\FileType::class)->create([
                'name' => $fileType,
            ]);
        }
    }
}
