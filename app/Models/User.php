<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Loan;
use App\Models\Booking;
use App\Models\LoanHistory;
use App\Models\Fine;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nim',
        'username',
        'email',
        'password',
        'name',
        'phone',
        'card_number',
        'avatar',
        'major_id',
        'angkatan',
        'credit_score',
        'max_loans',
        'total_fines',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'credit_score' => 'decimal:2',
            'total_fines' => 'decimal:2',
            'angkatan' => 'integer',
        ];
    }

    /**
     * Relasi ke Major (Jurusan/Prodi)
     */
    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
    }

    /**
     * Override method untuk mendukung login dengan NIM/Username/Email
     * 
     * @param string $username
     * @return string
     */
    public function findForPassport($username)
    {
        return $this->where('email', $username)
            ->orWhere('username', $username)
            ->orWhere('nim', $username)
            ->first();
    }

    /**
     * Scope untuk user aktif
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope untuk mahasiswa
     */
    public function scopeMahasiswa($query)
    {
        return $query->role('mahasiswa');
    }

    /**
     * Scope untuk dosen
     */
    public function scopeDosen($query)
    {
        return $query->role('dosen');
    }

    /**
     * Check apakah user adalah mahasiswa
     */
    public function isMahasiswa(): bool
    {
        return $this->hasRole('mahasiswa');
    }

    /**
     * Check apakah user adalah dosen
     */
    public function isDosen(): bool
    {
        return $this->hasRole('dosen');
    }

    /**
     * Check apakah user adalah staff
     */
    public function isStaff(): bool
    {
        return $this->hasRole('staff');
    }

    /**
     * Check apakah user adalah admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Accessor untuk mendapatkan nama role utama
     */
    public function getRoleNameAttribute(): string
    {
        return $this->roles->first()?->name ?? 'guest';
    }

    /**
     * Accessor untuk avatar URL
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }

        // Default avatar menggunakan UI Avatars
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Method untuk kalkulasi ulang credit score
     * (akan digunakan di service layer nanti)
     */
    // public function recalculateCreditScore(): void
    // {
    //     // Implementasi algoritma credit score
    //     // Base score: 100
    //     // - (Late Returns × 5)
    //     // - (Unpaid Fines / 1000)
    //     // + (On-time Returns × 0.5)

    //     // TODO: Implement setelah ada tabel loans
    // }

    /**
     * Method untuk update max_loans berdasarkan credit_score
     */
    public function updateMaxLoans(): void
    {
        $score = $this->credit_score;

        if ($score >= 90) {
            $maxLoans = 4;
        } elseif ($score >= 70) {
            $maxLoans = 3;
        } elseif ($score >= 50) {
            $maxLoans = 2;
        } else {
            $maxLoans = 1;
        }

        // Dosen dapat 2x lipat
        if ($this->isDosen()) {
            $maxLoans *= 2;
        }

        $this->update(['max_loans' => $maxLoans]);
    }

    /**
     * Check apakah user bisa meminjam buku
     */
    public function canBorrow(): bool
    {
        // Cek status aktif
        if ($this->status !== 'active') {
            return false;
        }

        // Cek denda
        if ($this->total_fines > 50000) { // Threshold 50rb
            return false;
        }

        // TODO: Cek jumlah peminjaman aktif (setelah ada tabel loans)

        return true;
    }

    /**
     * Relasi ke loans
     */
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    /**
     * Relasi ke active loans
     */
    public function activeLoans()
    {
        return $this->hasMany(Loan::class)
            ->whereIn('status', ['active', 'overdue', 'extended']);
    }

    /**
     * Relasi ke bookings
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Relasi ke fines
     */
    public function fines()
    {
        return $this->hasMany(Fine::class);
    }

    /**
     * Update loan history
     */
    public function updateLoanHistory(): void
    {
        $history = LoanHistory::firstOrCreate(['user_id' => $this->id]);

        $allLoans = $this->loans()->whereNotNull('return_date')->get();

        $onTimeReturns = $allLoans->filter(function ($loan) {
            return $loan->return_date <= $loan->due_date;
        })->count();

        $lateReturns = $allLoans->filter(function ($loan) {
            return $loan->return_date > $loan->due_date;
        })->count();

        $history->update([
            'total_loans' => $this->loans()->count(),
            'on_time_returns' => $onTimeReturns,
            'late_returns' => $lateReturns,
            'total_extensions' => $this->loans()->where('is_extended', true)->count(),
            'active_loans' => $this->activeLoans()->count(),
            'overdue_loans' => $this->loans()->where('status', 'overdue')->count(),
            'total_fines_incurred' => $this->fines()->sum('amount'),
            'total_fines_paid' => $this->fines()->where('status', 'paid')->sum('paid_amount'),
            'last_loan_at' => $this->loans()->latest('loan_date')->first()?->loan_date,
            'last_return_at' => $this->loans()->whereNotNull('return_date')
                ->latest('return_date')->first()?->return_date,
        ]);
    }

    /**
     * Method untuk kalkulasi ulang credit score
     */
    public function recalculateCreditScore(): void
    {
        // Get or create loan history
        $history = LoanHistory::firstOrCreate(['user_id' => $this->id]);

        // Base score: 100
        $baseScore = 100;

        // - (Late Returns × 5)
        $lateReturnsPenalty = $history->late_returns * 5;

        // - (Unpaid Fines / 1000)
        $unpaidFines = $this->fines()->where('status', 'unpaid')->sum('amount');
        $finesPenalty = $unpaidFines / 1000;

        // + (On-time Returns × 0.5)
        $onTimeBonus = $history->on_time_returns * 0.5;

        // Calculate final score
        $calculatedScore = $baseScore - $lateReturnsPenalty - $finesPenalty + $onTimeBonus;

        // Ensure score is between 0-100
        $finalScore = max(0, min(100, $calculatedScore));

        // Update history
        $history->update(['calculated_score' => $finalScore]);

        // Update user
        $this->update(['credit_score' => $finalScore]);
    }
}
