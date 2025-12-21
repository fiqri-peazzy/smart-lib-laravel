<?php

namespace App\Models;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class BookItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'book_id',
        'barcode',
        'qr_code',
        'condition',
        'status',
        'current_location',
        'notes',
        'acquisition_date',
        'acquisition_price',
    ];

    protected $casts = [
        'acquisition_date' => 'date',
        'acquisition_price' => 'decimal:2',
    ];

    /**
     * Boot method untuk auto-generate barcode
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            if (empty($item->barcode)) {
                $item->barcode = self::generateBarcode();
            }
        });

        static::created(function ($item) {
            // Generate QR code setelah item dibuat
            $item->generateQrCode();
        });

        // Update stock count di book setelah create/update/delete
        static::saved(function ($item) {
            $item->book->updateStockCounts();
        });

        static::deleted(function ($item) {
            // Hapus QR code file
            if ($item->qr_code && Storage::disk('public')->exists($item->qr_code)) {
                Storage::disk('public')->delete($item->qr_code);
            }
            $item->book->updateStockCounts();
        });
    }

    /**
     * Relasi ke book
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Generate unique barcode
     */
    public static function generateBarcode(): string
    {
        $year = date('Y');
        $lastItem = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastItem ? intval(substr($lastItem->barcode, -5)) + 1 : 1;

        return 'BOOK-' . $year . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Generate QR Code menggunakan endroid/qr-code
     */
    public function generateQrCode(): void
    {
        try {
            // Data yang akan di-encode ke QR
            $qrContent = json_encode([
                'type' => 'book_item',
                'id' => $this->id,
                'barcode' => $this->barcode,
                'book_id' => $this->book_id,
                'book_title' => $this->book->title ?? 'Unknown',
            ], JSON_UNESCAPED_SLASHES);

            // Build QR Code
            $builder = new Builder(
                writer: new PngWriter(),
                writerOptions: [],
                validateResult: false,
                data: $qrContent,
                encoding: new Encoding('UTF-8'),
                errorCorrectionLevel: ErrorCorrectionLevel::High,
                size: 300,
                margin: 10,
                roundBlockSizeMode: \Endroid\QrCode\RoundBlockSizeMode::Margin,
            );

            $result = $builder->build();

            // Path untuk save
            $filename = "qrcodes/book-items/{$this->barcode}.png";

            // Save ke storage public
            Storage::disk('public')->put($filename, $result->getString());

            // Update database
            $this->updateQuietly(['qr_code' => $filename]);
        } catch (\Exception $e) {
            Log::error('QR Code generation failed: ' . $e->getMessage());
        }
    }

    /**
     * Get QR code URL
     */
    public function getQrCodeUrlAttribute(): ?string
    {
        if ($this->qr_code && Storage::disk('public')->exists($this->qr_code)) {
            return Storage::url($this->qr_code);
        }

        return null;
    }

    /**
     * Scope untuk items available
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')
            ->where('condition', '!=', 'damaged');
    }

    /**
     * Check apakah item bisa dipinjam
     */
    public function canBeBorrowed(): bool
    {
        return $this->status === 'available' &&
            $this->condition !== 'damaged';
    }

    /**
     * Update status item
     */
    public function updateStatus(string $status, ?string $notes = null): void
    {
        $this->update([
            'status' => $status,
            'notes' => $notes ? $this->notes . "\n" . date('Y-m-d H:i') . ": " . $notes : $this->notes,
        ]);
    }

    /**
     * Mark as borrowed
     */
    public function markAsBorrowed(): void
    {
        $this->updateStatus('on_loan', 'Item dipinjam');
    }

    /**
     * Mark as returned
     */
    public function markAsReturned(): void
    {
        $this->updateStatus('available', 'Item dikembalikan');
    }

    /**
     * Mark as lost
     */
    public function markAsLost(?string $reason = null): void
    {
        $this->updateStatus('lost', $reason ?? 'Item hilang');
    }

    /**
     * Mark as damaged
     */
    public function markAsDamaged(?string $reason = null): void
    {
        $this->update([
            'status' => 'damaged',
            'condition' => 'damaged',
            'notes' => $this->notes . "\n" . date('Y-m-d H:i') . ": " . ($reason ?? 'Item rusak'),
        ]);
    }

    /**
     * Accessor untuk status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'available' => 'success',
            'on_loan' => 'warning',
            'maintenance' => 'info',
            'lost' => 'danger',
            'damaged' => 'danger',
            default => 'gray',
        };
    }

    /**
     * Accessor untuk condition badge color
     */
    public function getConditionColorAttribute(): string
    {
        return match ($this->condition) {
            'excellent' => 'success',
            'good' => 'info',
            'fair' => 'warning',
            'poor' => 'danger',
            'damaged' => 'danger',
            default => 'gray',
        };
    }
}
