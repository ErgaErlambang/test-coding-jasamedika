<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Vehicle extends Model
{
    use HasFactory, Sluggable;

    protected $table = "vehicles";

    protected $fillable = [
        "brand",
        "model",
        "plate_number",
        "price",
        "is_available",
        "is_active",
        "available_until",
        "image",
        "status",
        "slug"
    ];

    protected $dates = ['available_until'];

    /**
     * slug for vehicles model
     *
     * @return  array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'model'
            ],
        ];
    }

}