<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentTypeSeeder extends Seeder
{
    public function run()
    {
        $paymentTypes = [
            ['type_code' => 'SPP', 'type_name' => 'Biaya Bulanan (SPP)', 'amount' => 500000, 'is_recurring' => true],
            ['type_code' => 'DAFTAR', 'type_name' => 'Biaya Pendaftaran', 'amount' => 2000000, 'is_recurring' => false],
            ['type_code' => 'UJIAN', 'type_name' => 'Biaya Ujian', 'amount' => 250000, 'is_recurring' => false],
            ['type_code' => 'SERAGAM', 'type_name' => 'Seragam', 'amount' => 350000, 'is_recurring' => false],
            ['type_code' => 'BUKU', 'type_name' => 'Buku Pelajaran', 'amount' => 200000, 'is_recurring' => false],
            ['type_code' => 'KEGIATAN', 'type_name' => 'Biaya Kegiatan', 'amount' => 100000, 'is_recurring' => false],
        ];

        // Insert for each school
        $schools = DB::table('schools')->pluck('id');
        
        foreach ($schools as $schoolId) {
            foreach ($paymentTypes as $type) {
                DB::table('payment_types')->insert([
                    'school_id' => $schoolId,
                    'type_code' => $type['type_code'],
                    'type_name' => $type['type_name'],
                    'amount' => $type['amount'],
                    'is_recurring' => $type['is_recurring'],
                    'allow_installment' => true,
                    'is_active' => true,
                ]);
            }
        }
    }
}
