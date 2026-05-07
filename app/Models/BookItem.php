<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookItem extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'book_id',
        'qr_code',
        'status',
        'condition',
        'notes',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            if (empty($item->qr_code)) {
                $year = date('Y');
                do {
                    $random = strtoupper(\Illuminate\Support\Str::random(6));
                    $code = "BITEM-{$year}-{$random}";
                } while (static::withTrashed()->where('qr_code', $code)->exists());

                $item->qr_code = $code;
            }
        });

        // Event listener to recalculate book stock
        static::saved(function ($item) {
            $item->recalculateBookStock();
        });

        static::deleted(function ($item) {
            $item->recalculateBookStock();
        });
        
        static::restored(function ($item) {
            $item->recalculateBookStock();
        });
    }

    protected function recalculateBookStock()
    {
        if ($this->book) {
            $total = static::where('book_id', $this->book_id)->count();
            $available = static::where('book_id', $this->book_id)
                ->where('status', 'available')
                ->count();

            $this->book->update([
                'total_stock' => $total,
                'available_stock' => $available,
                'is_available' => $available > 0
            ]);
        }
    }
}