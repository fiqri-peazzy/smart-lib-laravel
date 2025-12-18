<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class DigitalCollection extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'type',
        'author',
        'year',
        'isbn',
        'publisher',
        'file_path',
        'file_type',
        'file_size',
        'thumbnail',
        'major_id',
        'nim',
        'supervisor',
        'description',
        'keywords',
        'language',
        'download_count',
        'view_count',
        'uploaded_by',
        'is_public',
        'is_featured',
    ];

    protected $casts = [
        'year' => 'integer',
        'file_size' => 'integer',
        'download_count' => 'integer',
        'view_count' => 'integer',
        'is_public' => 'boolean',
        'is_featured' => 'boolean',
    ];

    /**
     * Relasi ke Major (Jurusan)
     */
    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
    }

    /**
     * Relasi ke User (Uploader)
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Scope untuk koleksi publik
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope untuk koleksi featured
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope berdasarkan tipe
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Accessor untuk file URL
     */
    public function getFileUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    /**
     * Accessor untuk thumbnail URL
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        if ($this->thumbnail) {
            return Storage::url($this->thumbnail);
        }

        // Default thumbnail berdasarkan tipe
        return asset("images/defaults/{$this->type}.png");
    }

    /**
     * Accessor untuk ukuran file yang readable
     */
    public function getFileSizeReadableAttribute(): string
    {
        $bytes = $this->file_size;

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
     * Accessor untuk keywords array
     */
    public function getKeywordsArrayAttribute(): array
    {
        if (!$this->keywords) {
            return [];
        }

        return array_map('trim', explode(',', $this->keywords));
    }

    /**
     * Method untuk increment download count
     */
    public function incrementDownloads(): void
    {
        $this->increment('download_count');
    }

    /**
     * Method untuk increment view count
     */
    public function incrementViews(): void
    {
        $this->increment('view_count');
    }

    /**
     * Check apakah user bisa akses file ini
     */
    public function canBeAccessedBy(?User $user = null): bool
    {
        // Jika publik, semua bisa akses
        if ($this->is_public) {
            return true;
        }

        // Jika private, harus login
        if (!$user) {
            return false;
        }

        // Admin & staff bisa akses semua
        if ($user->hasAnyRole(['admin', 'staff'])) {
            return true;
        }

        // Owner bisa akses
        if ($this->uploaded_by === $user->id) {
            return true;
        }

        // Untuk skripsi, mahasiswa dari prodi yang sama bisa akses
        if ($this->type === 'skripsi' && $this->major_id === $user->major_id) {
            return true;
        }

        return false;
    }
}
