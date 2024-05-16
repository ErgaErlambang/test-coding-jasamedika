<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Vehicle;
use DB;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');    
        DB::table('vehicles')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Vehicle::insert([
            [
                "brand" => "Toyota",
                "model" => "Toyota Avanza All New",
                "plate_number" => "AB 123 CD",
                "price" => 350000,
                "is_available" => true,
                "is_active" => true,
                "image" => "avanza.jpg",
                "slug" => "toyota-avanza-all-new",
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s"),
            ],
            [
                "brand" => "Toyota",
                "model" => "Toyota SUV",
                "plate_number" => "AB 123 CD",
                "price" => 400000,
                "is_available" => true,
                "is_active" => true,
                "image" => "suv.png",
                "slug" => "toyota-suv",
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s"),
            ],
            [
                "brand" => "Honda",
                "model" => "Honda Brio",
                "plate_number" => "AB 123 CD",
                "price" => 250000,
                "is_available" => true,
                "is_active" => true,
                "image" => "brio.png",
                "slug" => "honda-brio",
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s"),
            ],
        ]);
    }
}