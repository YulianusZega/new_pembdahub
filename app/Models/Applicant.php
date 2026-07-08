<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Applicant extends Model
{
    protected $fillable = [
        'school_id',
        'program_keahlian_id',
        'konsentrasi_keahlian_id',
        'major_id',
        'academic_year_id',
        'student_id',
        'registration_number',
        'registration_type',
        'wave_id',
        'registered_by',
        'admission_path',
        'nisn',
        'full_name',
        'photo_path',
        'submission_date',
        'payment_verified_at',
        'document_verified_at',
        'test_date',
        'announcement_date',
        'gender',
        'birth_place',
        'birth_date',
        'religion',
        'address',
        'phone',
        'email',
        'father_name',
        'father_phone',
        'father_occupation',
        'mother_name',
        'mother_phone',
        'mother_occupation',
        'parent_income',
        'previous_school',
        'previous_school_address',
        'major_choice_1',
        'major_choice_2',
        'raport_score',
        'test_score',
        'interview_score',
        'achievement_score',
        'final_score',
        'ranking',
        'status',
        'notes',
        'submitted_at',
        'accepted_at',
        'rejected_at',
        'registered_at',
        'prestasi_verified_at',
        'prestasi_rejection_reason',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'raport_score' => 'decimal:2',
        'test_score' => 'decimal:2',
        'interview_score' => 'decimal:2',
        'achievement_score' => 'decimal:2',
        'final_score' => 'decimal:2',
        'ranking' => 'integer',
        'submission_date' => 'datetime',
        'payment_verified_at' => 'datetime',
        'document_verified_at' => 'datetime',
        'test_date' => 'date',
        'announcement_date' => 'date',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
        'registered_at' => 'datetime',
        'prestasi_verified_at' => 'datetime',
    ];

    // Relationships
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
    }

    public function programKeahlian(): BelongsTo
    {
        return $this->belongsTo(ProgramKeahlian::class, 'program_keahlian_id');
    }

    public function konsentrasiKeahlian(): BelongsTo
    {
        return $this->belongsTo(KonsentrasiKeahlian::class, 'konsentrasi_keahlian_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function wave(): BelongsTo
    {
        return $this->belongsTo(RegistrationWave::class, 'wave_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function majorChoice1(): BelongsTo
    {
        return $this->belongsTo(Major::class, 'major_choice_1');
    }

    public function majorChoice2(): BelongsTo
    {
        return $this->belongsTo(Major::class, 'major_choice_2');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ApplicantDocument::class);
    }

    public function testScores(): HasMany
    {
        return $this->hasMany(ApplicantTestScore::class);
    }

    public function achievements(): HasMany
    {
        return $this->hasMany(ApplicantAchievement::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(ApplicantPayment::class);
    }

    public function discounts(): HasMany
    {
        return $this->hasMany(ApplicantDiscount::class);
    }

    public function feeExemptions(): HasMany
    {
        return $this->hasMany(ApplicantFeeExemption::class);
    }

    // Helpers
    public function calculateFinalScore(): float
    {
        $raport = $this->raport_score ?? 0;
        $test = $this->test_score ?? 0;
        $interview = $this->interview_score ?? 0;
        $achievement = $this->achievement_score ?? 0;

        // Formula: 40% Raport + 30% Test + 20% Interview + 10% Achievement
        return round(($raport * 0.4) + ($test * 0.3) + ($interview * 0.2) + ($achievement * 0.1), 2);
    }

    public function getStatusBadgeColor(): string
    {
        return match($this->status) {
            'draft' => 'gray',
            'submitted' => 'blue',
            'payment_verified' => 'indigo',
            'prestasi_verified' => 'amber',
            'document_verified' => 'purple',
            'tested' => 'pink',
            'scored' => 'yellow',
            'accepted' => 'green',
            'rejected' => 'red',
            'reregistered' => 'teal',
            'registered' => 'emerald',
            default => 'gray',
        };
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'draft' => 'Draft',
            'submitted' => 'Menunggu Verifikasi',
            'payment_verified' => 'Pembayaran Terverifikasi',
            'prestasi_verified' => 'Prestasi Terverifikasi',
            'document_verified' => 'Dokumen Terverifikasi',
            'tested' => 'Telah Tes',
            'scored' => 'Sudah Dinilai',
            'accepted' => 'Diterima',
            'rejected' => 'Ditolak',
            'reregistered' => 'Sudah Daftar Ulang',
            'registered' => 'Aktif sebagai Siswa',
            default => 'Unknown',
        };
    }

    public function canMigrateToStudent(): bool
    {
        return in_array($this->status, ['accepted', 'reregistered']) && $this->student_id === null;
    }

    /**
     * Check if this applicant uses the prestasi (achievement) admission path.
     */
    public function isPrestasiPath(): bool
    {
        return $this->admission_path === 'prestasi';
    }

    /**
     * Get the next required action label for this applicant.
     */
    public function getNextActionLabel(): string
    {
        if ($this->status === 'submitted' && $this->isPrestasiPath()) {
            return 'Menunggu Verifikasi Prestasi';
        }

        return match($this->status) {
            'submitted' => 'Menunggu Verifikasi Pembayaran',
            'payment_verified', 'prestasi_verified' => 'Menunggu Verifikasi Dokumen',
            'document_verified' => ($this->school?->requires_test ?? false) ? 'Menunggu Tes Masuk' : 'Siap Diterima',
            'tested' => 'Menunggu Penilaian',
            'scored' => 'Menunggu Keputusan',
            'accepted' => 'Menunggu Daftar Ulang',
            default => '-',
        };
    }
}
