<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'city',
        'state',
        'country',
        'postal_code',
        'latitude',
        'longitude',
        'is_active',
        'listings_count',
        'parent_id',
        'type',
        'order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'order' => 'integer',
    ];

    public function parent()
    {
        return $this->belongsTo(Location::class, 'parent_id');
    }

    public function districts()
    {
        return $this->hasMany(Location::class, 'parent_id')->where('type', 'district');
    }

    public function districtsWithCount()
    {
        return $this->hasMany(Location::class, 'parent_id')
            ->where('type', 'district')
            ->orderBy('order')
            ->orderBy('name');
    }

    public function scopeDivisions($query)
    {
        return $query->where('type', 'division');
    }

    public function scopeDistricts($query)
    {
        return $query->where('type', 'district');
    }
}
