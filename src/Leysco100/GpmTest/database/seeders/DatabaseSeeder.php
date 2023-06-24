<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Demo Account',
            'email' => 'dev@leysco100.com',
            'account' => 'manager',
            'DfltsGroup' => 1,
            'SUPERUSER' => 1,
            'gate_id' => 1,
            'password' => Hash::make('dev@2022'),
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        ]);

        DB::table('o_a_d_m_s')->insert([
            'CompnyName' => 'Leysco Consulting',
            'created_at' => \Carbon\Carbon::now(),
            'NotifEmail' => 'bereankibet@gmail.com',
            'updated_at' => \Carbon\Carbon::now(),
        ]);
    }
}
