<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;

    use Sluggable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'created_at',
        'updated_at',
        'slug',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'products' => 'name'
            ]
        ];
    }
}
