<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


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
        'author_id',
        'publisher_id',
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
        'is_digital',
        'digital_file_path',
        'digital_file_type',
        'digital_file_size',
        'digital_download_count',
        'digital_view_count',
        'keywords',
        'nim',
        'supervisor',
    ];

    protected $casts = [
        'publication_year' => 'integer',
        'pages' => 'integer',
        'total_stock' => 'integer',
        'available_stock' => 'integer',
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
        'is_digital' => 'boolean',
        'digital_file_size' => 'integer',
        'digital_download_count' => 'integer',
        'digital_view_count' => 'integer',
    ];

    /**
     * Relasi ke Author (Master)
     */
    public function authorMaster(): BelongsTo
    {
        return $this->belongsTo(Author::class, 'author_id');
    }

    /**
     * Relasi ke Publisher (Master)
     */
    public function publisherMaster(): BelongsTo
    {
        return $this->belongsTo(Publisher::class, 'publisher_id');
    }


    protected static function boot()
    {
        parent::boot();

        static::saving(function ($book) {
            // Auto-generate barcode if empty
            if (empty($book->barcode)) {
                // Random 16 karakter alphanumeric (huruf kapital + angka)
                do {
                    $code = strtoupper(Str::random(16));
                } while (static::withTrashed()->where('barcode', $code)->exists());

                $book->barcode = $code;
            }

            // Sync legacy string fields for compatibility if they are empty
            if ($book->author_id) {
                $book->author = $book->authorMaster?->name;
            }
            if ($book->publisher_id) {
                $book->publisher = $book->publisherMaster?->name;
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
        return $query->physical()
            ->where('is_available', true)
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
     * Scope untuk buku digital
     */
    public function scopeDigital($query)
    {
        return $query->where('is_digital', true);
    }

    /**
     * Scope untuk buku fisik
     */
    public function scopePhysical($query)
    {
        return $query->where('is_digital', false);
    }

    /**
     * Accessor untuk digital file URL
     */
    public function getDigitalFileUrlAttribute(): ?string
    {
        return $this->digital_file_path ? Storage::url($this->digital_file_path) : null;
    }

    /**
     * Accessor untuk ukuran file yang readable
     */
    public function getDigitalFileSizeReadableAttribute(): string
    {
        $bytes = $this->digital_file_size;

        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' bytes';
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
        return !$this->is_digital && $this->is_available && $this->available_stock >= $quantity;
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

    /**
     * Check if user can access digital content
     */
    public function canBeAccessedBy(?User $user = null): bool
    {
        if (!$this->is_digital) {
            return false;
        }

        // Standard books are public for now
        if ($this->digital_file_type !== 'skripsi') {
            return true;
        }

        // If private (skripsi usually), check user
        if (!$user) {
            return false;
        }

        // Admin & staff can access everything
        if (method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['admin', 'staff'])) {
            return true;
        }

        // For skripsi, students from same major can access
        if ($this->digital_file_type === 'skripsi' && $this->recommended_for_major_id === $user->major_id) {
            return true;
        }

        return false;
    }
}

