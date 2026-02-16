<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'label',
        'description',
        'icon',
        'is_enabled',
        'email_enabled',
        'priority',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'email_enabled' => 'boolean',
        'priority' => 'integer',
    ];

    // Scopes
    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    public function scopeEmailEnabled($query)
    {
        return $query->where('email_enabled', true);
    }
}
