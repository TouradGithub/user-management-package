<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserTypeSeeder::class,
        ]);
    }
}