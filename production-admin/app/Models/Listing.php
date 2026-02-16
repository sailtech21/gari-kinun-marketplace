<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Listing extends Model {
    use HasFactory;
    protected $fillable = [
        "user_id",
        "dealer_id",
        "category_id",
        "title",
        "slug",
        "description",
        "price",
        "location",
        "phone",
        "video_link",
        "status",
        "images",
        "is_featured",
        "featured_until",
        "featured_price",
        "featured_at",
        "is_boosted",
        "boosted_until",
        "is_hidden",
        "rejection_reason",
        "expires_at",
        "views",
        // Vehicle details
        "condition",
        "model",
        "year_of_manufacture",
        "engine_capacity",
        "transmission",
        "registration_year",
        "brand",
        "trim_edition",
        "kilometers_run",
        "fuel_type",
        "body_type"
    ];
    protected $casts = [
        "price" => "decimal:2",
        "featured_price" => "decimal:2",
        "images" => "array",
        "is_featured" => "boolean",
        "is_boosted" => "boolean",
        "is_hidden" => "boolean",
        "featured_until" => "datetime",
        "featured_at" => "datetime",
        "boosted_until" => "datetime",
        "expires_at" => "datetime"
    ];
    public function user() { return $this->belongsTo(User::class); }
    public function dealer() { return $this->belongsTo(Dealer::class); }
    public function category() { return $this->belongsTo(Category::class); }
    public function reports() { return $this->hasMany(Report::class); }
}