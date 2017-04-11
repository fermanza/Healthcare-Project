<?php

use Illuminate\Database\Seeder;

class FileStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $fileStatuses = [
            'To Process',
            'Failed',
            'Processed',
        ];

        foreach ($fileStatuses as $fileStatus) {
            factory(App\FileStatus::class)->create([
                'name' => $fileStatus,
            ]);
        }
    }
}
