<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'slug',
        'type', 
        'icon', 
        'description',
        'is_active', 
        'parent_id',
        'show_fuel_type',
        'show_transmission',
        'show_body_type',
        'show_year',
        'show_mileage',
        'show_engine_capacity',
        'show_condition',
        'custom_fields',
        'posting_fee',
        'required_fields',
        'order'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'show_fuel_type' => 'boolean',
        'show_transmission' => 'boolean',
        'show_body_type' => 'boolean',
        'show_year' => 'boolean',
        'show_mileage' => 'boolean',
        'show_engine_capacity' => 'boolean',
        'show_condition' => 'boolean',
        'custom_fields' => 'array',
        'required_fields' => 'array',
        'posting_fee' => 'decimal:2',
        'order' => 'integer',
    ];

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function childrenWithCount()
    {
        return $this->children()->withCount('listings');
    }

    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }
}
