<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            background: white;
        }
        
        .slip-container {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        
        .header h2 {
            margin: 5px 0 0 0;
            font-size: 16px;
            font-weight: normal;
        }
        
        .employee-info {
            margin-bottom: 20px;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        
        .info-label {
            width: 120px;
            font-weight: bold;
        }
        
        .info-value {
            flex: 1;
        }
        
        .salary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .salary-table th, .salary-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        
        .salary-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        
        .amount {
            text-align: right;
        }
        
        .total-row {
            background-color: #f9f9f9;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature {
            text-align: center;
            width: 200px;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 60px;
            padding-top: 5px;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 10px;
            }
            
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="slip-container">
        <div class="header">
            <h1>SLIP GAJI</h1>
            <h2 id="unitName">SMP Swasta PEMBDA 2 Gunungsitoli</h2>
            <p id="periode">Periode: -</p>
        </div>
        
        <div class="employee-info">
            <div class="info-row">
                <div class="info-label">Nama</div>
                <div class="info-value">: <span id="employeeName">-</span></div>
            </div>
            <div class="info-row">
                <div class="info-label">Nomor Induk</div>
                <div class="info-value">: <span id="employeeId">-</span></div>
            </div>
            <div class="info-row">
                <div class="info-label">Status</div>
                <div class="info-value">: <span id="employeeStatus">-</span></div>
            </div>
            <div class="info-row">
                <div class="info-label">Unit Kerja</div>
                <div class="info-value">: <span id="workUnit">-</span></div>
            </div>
        </div>
        
        <table class="salary-table">
            <thead>
                <tr>
                    <th style="width: 60%">KOMPONEN GAJI</th>
                    <th style="width: 40%">JUMLAH (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Gaji Pokok</td>
                    <td class="amount" id="gajiPokok">0</td>
                </tr>
                <tr id="rowTunjanganJabatan" style="display: none;">
                    <td>Tunjangan Jabatan</td>
                    <td class="amount" id="tunjanganJabatan">0</td>
                </tr>
                <tr id="rowTunjanganKeluarga" style="display: none;">
                    <td>Tunjangan Keluarga</td>
                    <td class="amount" id="tunjanganKeluarga">0</td>
                </tr>
                <tr id="rowTunjanganAnak" style="display: none;">
                    <td>Tunjangan Anak</td>
                    <td class="amount" id="tunjanganAnak">0</td>
                </tr>
                <tr id="rowTunjanganBeras" style="display: none;">
                    <td>Tunjangan Beras</td>
                    <td class="amount" id="tunjanganBeras">0</td>
                </tr>
                <tr id="rowHonor" style="display: none;">
                    <td>Honor Mengajar (<span id="jamHonor">0</span> jam)</td>
                    <td class="amount" id="honorMengajar">0</td>
                </tr>
                <tr class="total-row">
                    <td><strong>TOTAL GAJI (THP)</strong></td>
                    <td class="amount"><strong id="totalGaji">0</strong></td>
                </tr>
            </tbody>
        </table>
        
        <div class="footer">
            <div class="signature">
                <p>Pegawai</p>
                <div class="signature-line">
                    <span id="employeeNameSign">-</span>
                </div>
            </div>
            <div class="signature">
                <p>Bendahara</p>
                <div class="signature-line">
                    (..............................)
                </div>
            </div>
            <div class="signature">
                <p>Kepala Sekolah</p>
                <div class="signature-line">
                    (..............................)
                </div>
            </div>
        </div>
    </div>
    
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
            Cetak Slip Gaji
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
            Tutup
        </button>
    </div>
    
    <script>
        // Function to format rupiah
        function formatRupiah(amount) {
            if (amount === null || amount === undefined || amount === 0) return '0';
            return new Intl.NumberFormat('id-ID').format(amount);
        }
        
        // Function to populate slip data
        function populateSlipData(data) {
            document.getElementById('employeeName').textContent = data.nama_pegawai || '-';
            document.getElementById('employeeId').textContent = data.nomor_induk || '-';
            document.getElementById('employeeStatus').textContent = data.status_kepegawaian || '-';
            document.getElementById('workUnit').textContent = data.unit_nama || '-';
            document.getElementById('employeeNameSign').textContent = data.nama_pegawai || '-';
            
            // Set periode (current month/year)
            const now = new Date();
            const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni",
                "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
            document.getElementById('periode').textContent = 
                `Periode: ${monthNames[now.getMonth()]} ${now.getFullYear()}`;
            
            // Salary components
            document.getElementById('gajiPokok').textContent = formatRupiah(data.gaji_pokok);
            document.getElementById('totalGaji').textContent = formatRupiah(data.total);
            
            // Show/hide rows based on amounts
            if (data.tunjangan_jabatan > 0) {
                document.getElementById('rowTunjanganJabatan').style.display = 'table-row';
                document.getElementById('tunjanganJabatan').textContent = formatRupiah(data.tunjangan_jabatan);
            }
            
            if (data.tunjangan_keluarga > 0) {
                document.getElementById('rowTunjanganKeluarga').style.display = 'table-row';
                document.getElementById('tunjanganKeluarga').textContent = formatRupiah(data.tunjangan_keluarga);
            }
            
            if (data.tunjangan_anak > 0) {
                document.getElementById('rowTunjanganAnak').style.display = 'table-row';
                document.getElementById('tunjanganAnak').textContent = formatRupiah(data.tunjangan_anak);
            }
            
            if (data.tunjangan_beras > 0) {
                document.getElementById('rowTunjanganBeras').style.display = 'table-row';
                document.getElementById('tunjanganBeras').textContent = formatRupiah(data.tunjangan_beras);
            }
            
            if (data.honor > 0) {
                document.getElementById('rowHonor').style.display = 'table-row';
                document.getElementById('jamHonor').textContent = data.jam_honor || 0;
                document.getElementById('honorMengajar').textContent = formatRupiah(data.honor);
            }
        }
        
        // Get data from URL parameters or localStorage
        window.addEventListener('load', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const penugasanId = urlParams.get('penugasan_id');
            
            if (penugasanId) {
                // Fetch data from API
                fetch(`get_detail_pegawai.php?penugasan_id=${penugasanId}`)
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            populateSlipData(result.data);
                        } else {
                            alert('Error: ' + result.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat mengambil data');
                    });
            } else {
                // Check localStorage for data
                const storedData = localStorage.getItem('slipGajiData');
                if (storedData) {
                    const data = JSON.parse(storedData);
                    populateSlipData(data);
                    localStorage.removeItem('slipGajiData'); // Clean up
                }
            }
        });
    </script>
</body>
</html>
