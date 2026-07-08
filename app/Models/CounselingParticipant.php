<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CounselingParticipant extends Model
{
    use HasFactory;

    protected $table = 'counseling_participants';

    protected $fillable = [
        'counseling_record_id',
        'user_id',
        'role',
        'notes',
    ];

    public const ROLES = [
        'guru_bk' => 'Guru BK',
        'wali_kelas' => 'Wali Kelas',
        'pks' => 'PKS',
        'kepala_sekolah' => 'Kepala Sekolah',
        'guru_mapel' => 'Guru Mapel',
        'orang_tua' => 'Orang Tua',
        'lainnya' => 'Lainnya',
    ];

    public function getRoleLabelAttribute(): string
    {
        return self::ROLES[$this->role] ?? $this->role;
    }

    public function counselingRecord(): BelongsTo
    {
        return $this->belongsTo(StudentCounselingRecord::class, 'counseling_record_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
