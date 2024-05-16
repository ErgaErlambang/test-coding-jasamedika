<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Profile;
use DB;

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');    
        DB::table('profile')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Profile::insert([
            [
                "phone_number" => "088218675993",
                "driver_license" => "1234567891234560",
                "address" => "Jl test 123",
                "user_id" => 1,
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s"),
            ],
            [
                "phone_number" => "08812345689",
                "driver_license" => "1234567893216540",
                "address" => "Jl test 123",
                "user_id" => 2,
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s"),
            ]
        ]);
    }
}