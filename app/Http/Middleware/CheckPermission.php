<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Define permission matrix for roles
     */
    protected array $permissions = [
        'superadmin' => ['*'], // All permissions
        'admin_sekolah' => [
            'sekolah.view',
            'sekolah.edit',
            'guru.view',
            'guru.create',
            'guru.edit',
            'guru.delete',
            'siswa.view',
            'siswa.create',
            'siswa.edit',
            'siswa.delete',
            'kelas.view',
            'kelas.create',
            'kelas.edit',
            'kelas.delete',
            'jadwal.view',
            'jadwal.create',
            'jadwal.edit',
            'jadwal.delete',
            'nilai.view',
            'nilai.import',
            'tagihan.view',
            'pembayaran.view',
            'laporan.view',
        ],
        'guru' => [
            'jadwal.view',
            'siswa.view',
            'nilai.create',
            'nilai.edit',
            'kehadiran.create',
            'kehadiran.view',
            'materi.create',
            'materi.view',
            'tugas.create',
            'tugas.view',
        ],
        'siswa' => [
            'profil.view',
            'profil.edit',
            'nilai.view',
            'kehadiran.view',
            'jadwal.view',
            'materi.view',
            'tugas.view',
            'tugas.submit',
            'tagihan.view',
        ],
        'orang_tua' => [
            'profil.view',
            'siswa.view',
            'nilai.view',
            'kehadiran.view',
            'tagihan.view',
            'pembayaran.view',
        ],
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        // Get user's permissions
        $userPermissions = $this->permissions[$user->role] ?? [];

        // Check if user has permission (superadmin has all permissions)
        if (in_array('*', $userPermissions) || in_array($permission, $userPermissions)) {
            return $next($request);
        }

        abort(403, 'Anda tidak memiliki izin untuk mengakses resource ini.');
    }
}
