<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CustomField extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'options',
        'placeholder',
        'default_value',
        'min_value',
        'max_value',
        'help_text',
        'validation_rules',
        'show_in_add_form',
        'show_in_search',
        'show_in_details',
        'show_on_listing_card',
        'is_required',
        'is_searchable',
        'is_filterable',
        'allow_multiple_selection',
        'field_group',
        'order',
        'is_active',
    ];

    protected $casts = [
        'options' => 'array',
        'validation_rules' => 'array',
        'show_in_add_form' => 'boolean',
        'show_in_search' => 'boolean',
        'show_in_details' => 'boolean',
        'show_on_listing_card' => 'boolean',
        'is_required' => 'boolean',
        'is_searchable' => 'boolean',
        'is_filterable' => 'boolean',
        'allow_multiple_selection' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Automatically generate slug from name
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($field) {
            if (!$field->slug) {
                $field->slug = Str::slug($field->name);
            }
            
            // Ensure unique slug
            $originalSlug = $field->slug;
            $count = 1;
            while (static::where('slug', $field->slug)->exists()) {
                $field->slug = $originalSlug . '-' . $count;
                $count++;
            }
        });

        static::updating(function ($field) {
            if ($field->isDirty('name') && !$field->isDirty('slug')) {
                $baseSlug = Str::slug($field->name);
                
                // Ensure unique slug (excluding current record)
                $originalSlug = $baseSlug;
                $count = 1;
                while (static::where('slug', $baseSlug)->where('id', '!=', $field->id)->exists()) {
                    $baseSlug = $originalSlug . '-' . $count;
                    $count++;
                }
                
                $field->slug = $baseSlug;
            }
        });
    }

    /**
     * Relationship with categories
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_custom_field')
            ->withTimestamps()
            ->withPivot(['order', 'is_required'])
            ->orderBy('category_custom_field.order');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForAddForm($query)
    {
        return $query->where('show_in_add_form', true);
    }

    public function scopeForSearch($query)
    {
        return $query->where('show_in_search', true);
    }

    public function scopeForDetails($query)
    {
        return $query->where('show_in_details', true);
    }

    public function scopeSearchable($query)
    {
        return $query->where('is_searchable', true);
    }

    public function scopeFilterable($query)
    {
        return $query->where('is_filterable', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    /**
     * Available field types
     */
    public static function getFieldTypes()
    {
        return [
            'text' => 'Text Input',
            'textarea' => 'Text Area',
            'number' => 'Number Input',
            'select' => 'Dropdown',
            'radio' => 'Radio Button',
            'checkbox' => 'Checkbox',
            'date' => 'Date',
            'email' => 'Email',
            'url' => 'URL',
            'tel' => 'Phone',
            'file' => 'File Upload',
        ];
    }

    /**
     * Available field groups
     */
    public static function getFieldGroups()
    {
        return [
            'basic_info' => 'Basic Info',
            'technical_info' => 'Technical Info',
            'extra_info' => 'Extra Info',
        ];
    }

    /**
     * Check if field has options (select, radio, checkbox)
     */
    public function hasOptions()
    {
        return in_array($this->type, ['select', 'radio', 'checkbox']);
    }

    /**
     * Get validation rules as Laravel validation string
     */
    public function getValidationRulesString()
    {
        $rules = [];

        if ($this->is_required) {
            $rules[] = 'required';
        }

        if ($this->validation_rules) {
            foreach ($this->validation_rules as $rule => $value) {
                if ($value === true) {
                    $rules[] = $rule;
                } elseif ($value !== false && $value !== null) {
                    $rules[] = "$rule:$value";
                }
            }
        }

        return implode('|', $rules);
    }

    /**
     * Get fields for a specific category
     */
    public static function getFieldsForCategory($categoryId)
    {
        return static::active()
            ->whereHas('categories', function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->with(['categories' => function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            }])
            ->get()
            ->sortBy(function ($field) {
                return $field->categories->first()->pivot->order ?? $field->order;
            });
    }
}
