<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'isbn',
        'barcode',
        'title',
        'subtitle',
        'author',
        'publisher',
        'publication_year',
        'edition',
        'pages',
        'language',
        'cover_image',
        'description',
        'total_stock',
        'available_stock',
        'rack_location',
        'recommended_for_major_id',
        'is_available',
        'is_featured',
        'added_by',
    ];

    protected $casts = [
        'publication_year' => 'integer',
        'pages' => 'integer',
        'total_stock' => 'integer',
        'available_stock' => 'integer',
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($book) {
            if (empty($book->barcode)) {
                // Random 16 karakter alphanumeric (huruf kapital + angka)
                do {
                    $code = strtoupper(Str::random(16));
                } while (static::withTrashed()->where('barcode', $code)->exists());

                $book->barcode = $code;
            }
        });
    }

    /**
     * Relasi ke categories (many-to-many)
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(BookCategory::class, 'book_category');
    }

    /**
     * Relasi ke loans
     */
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    /**
     * Relasi ke major (rekomendasi)
     */
    public function recommendedForMajor(): BelongsTo
    {
        return $this->belongsTo(Major::class, 'recommended_for_major_id');
    }

    /**
     * Relasi ke user (yang menambahkan)
     */
    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    /**
     * Scope untuk buku available
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)
            ->where('available_stock', '>', 0);
    }

    /**
     * Scope untuk buku featured
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope berdasarkan kategori
     */
    public function scopeInCategory($query, $categoryId)
    {
        return $query->whereHas('categories', function ($q) use ($categoryId) {
            $q->where('book_categories.id', $categoryId);
        });
    }

    /**
     * Accessor untuk cover URL
     */
    public function getCoverUrlAttribute(): string
    {
        if ($this->cover_image) {
            return Storage::url($this->cover_image);
        }

        return asset('images/default-book-cover.png');
    }

    /**
     * Accessor untuk full title
     */
    public function getFullTitleAttribute(): string
    {
        if ($this->subtitle) {
            return "{$this->title}: {$this->subtitle}";
        }

        return $this->title;
    }

    /**
     * Check apakah buku bisa dipinjam
     */
    public function canBeBorrowed(int $quantity = 1): bool
    {
        return $this->is_available && $this->available_stock >= $quantity;
    }

    /**
     * Decrement available stock
     */
    public function decrementStock(int $qty = 1): void
    {
        $this->decrement('available_stock', $qty);
        if ($this->available_stock <= 0) {
            $this->update(['is_available' => false]);
        }
    }

    /**
     * Increment available stock
     */
    public function incrementStock(int $qty = 1): void
    {
        $this->increment('available_stock', $qty);
        if ($this->available_stock > 0 && ! $this->is_available) {
            $this->update(['is_available' => true]);
        }
    }
}
