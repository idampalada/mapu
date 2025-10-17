<?php
namespace App\Models;
use CodeIgniter\Model;

class KomputerModel extends Model
{
    protected $table = 'komputer';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
    'tgl_tarik',
    'nama_kl',
    'nama_kpknl',
    'nama_satker',
    'kode_barang',
    'nama_barang',
    'nilai_perolehan',
    'nilai_penyusutan',
    'nilai_buku',
    'nup',
    'tanggal_perolehan',
    'kondisi',
    'merk',
    'kuantitas',
    'status_penggunaan',
    'jns_processor',
    'processor',
    'memori',
    'hardisk',
    'monitor',
    'spek_lain',
    'kelompok', // KOMPUTER UNIT, PERALATAN KOMPUTER
    'sub_kelompok',
    'created_at',
    'updated_at',
    // Field baru yang ditambahkan
    'bidang',
    'pengguna_sebelumnya',
    'pengguna_sekarang',
    'status_barang',
    'keterangan'
];

// Kolom untuk pencarian LIKE (key search ke banyak kolom)
protected array $searchableColumns = [
    'kode_barang','nama_barang','merk','nup','bidang','kelompok','sub_kelompok',
    'kondisi','status_penggunaan','status_barang','keterangan','spek_lain',
    'jns_processor','processor','memori','hardisk','monitor',
    'pengguna_sebelumnya','pengguna_sekarang'
];

// Kolom yang diizinkan untuk sorting (whitelist)
protected array $sortableColumns = [
    'kode_barang','nama_barang','nilai_perolehan','tanggal_perolehan',
    'merk','bidang','kelompok','kondisi'
];

    
    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    // Validation Rules
    protected $validationRules = [
    'kode_barang' => 'required|max_length[100]',
    'nama_barang' => 'required|max_length[255]',
    'kelompok' => 'required|max_length[100]',
    'nup' => 'permit_empty|max_length[100]',
    'merk' => 'permit_empty|max_length[100]',
    'kondisi' => 'permit_empty|max_length[50]',
    'kuantitas' => 'permit_empty|integer',
    'status_penggunaan' => 'permit_empty|max_length[200]',
    'jns_processor' => 'permit_empty|max_length[100]',
    'processor' => 'permit_empty|max_length[100]',
    'memori' => 'permit_empty|max_length[100]',
    'hardisk' => 'permit_empty|max_length[100]',
    'monitor' => 'permit_empty|max_length[100]',
    'spek_lain' => 'permit_empty|max_length[500]',
    'nilai_perolehan' => 'permit_empty|decimal',
    'nilai_penyusutan' => 'permit_empty|decimal',
    'nilai_buku' => 'permit_empty|decimal',
    // Validasi untuk field baru
    'bidang' => 'permit_empty|max_length[200]',
    'pengguna_sebelumnya' => 'permit_empty|max_length[255]',
    'pengguna_sekarang' => 'permit_empty|max_length[255]',
    'status_barang' => 'permit_empty|max_length[100]',
    'keterangan' => 'permit_empty'
];
    
    protected $validationMessages = [
        'kode_barang' => [
            'required' => 'Kode barang harus diisi',
            'max_length' => 'Kode barang maksimal 100 karakter'
        ],
        'nama_barang' => [
            'required' => 'Nama barang harus diisi',
            'max_length' => 'Nama barang maksimal 255 karakter'
        ],
        'kelompok' => [
            'required' => 'Kelompok harus diisi',
            'max_length' => 'Kelompok maksimal 100 karakter'
        ],
        'kuantitas' => [
            'integer' => 'Kuantitas harus berupa angka'
        ],
        'nilai_perolehan' => [
            'decimal' => 'Nilai perolehan harus berupa angka'
        ],
        'nilai_penyusutan' => [
            'decimal' => 'Nilai penyusutan harus berupa angka'
        ],
        'nilai_buku' => [
            'decimal' => 'Nilai buku harus berupa angka'
        ]
    ];
    
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
    
    
    // ============ CRUD DASAR ============
    
    // 1. CRUD DASAR - Get all data
    public function getAllData($limit = null, $offset = 0)
    {
        $builder = $this->builder();
        
        if ($limit) {
            $builder->limit($limit, $offset);
        }
        
        return $builder->orderBy('kode_barang', 'ASC')
                      ->get()
                      ->getResultArray();
    }
    
    // 2. CRUD DASAR - Find by ID
    public function findById($id)
    {
        $builder = $this->builder();
        return $builder->where('id', $id)
                      ->get()
                      ->getRowArray();
    }
    
    // 3. CRUD DASAR - Insert data
    public function insertData($data)
    {
        $builder = $this->builder();
        
        // Add timestamps
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return $builder->insert($data);
    }
    
    // 4. CRUD DASAR - Update data
    public function updateData($id, $data)
    {
        $builder = $this->builder();
        
        // Add updated timestamp
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return $builder->where('id', $id)
                      ->update($data);
    }
    
    // 5. CRUD DASAR - Delete data
    public function deleteData($id)
    {
        $builder = $this->builder();
        return $builder->where('id', $id)
                      ->delete();
    }
    
    // 6. CRUD DASAR - Count all records
    public function countAll()
    {
        $builder = $this->builder();
        return $builder->countAllResults();
    }
    
    // ============ BUSINESS LOGIC ============
    
    // 7. BUSINESS LOGIC - Find by kode barang
    public function findByKodeBarang($kodeBarang)
    {
        $builder = $this->builder();
        return $builder->where('kode_barang', $kodeBarang)
                      ->get()
                      ->getRowArray();
    }
    
    // 8. BUSINESS LOGIC - Search functionality dengan filter kelompok
public function searchKomputer(
    string $searchTerm = '',
    string $kelompok = '',
    int $limit = 100,
    int $offset = 0,
    string $sort = 'nama_barang',
    string $order = 'asc'
) {
    return $this->getSearchResults($searchTerm, $kelompok, $sort, $order, $limit, $offset);
}

    /**
 * Terapkan pencarian ke banyak kolom (OR LIKE terkelompok, chainable).
 */
public function scopeSearchAll(\CodeIgniter\Database\BaseBuilder $builder, ?string $term): \CodeIgniter\Database\BaseBuilder
{
    if (empty($term)) return $builder;

    $escaped = $this->db->escapeLikeString($term);
    $builder->groupStart();

    // LIKE untuk semua kolom teks
    foreach ($this->searchableColumns as $i => $col) {
        $i === 0 ? $builder->like($col, $escaped) : $builder->orLike($col, $escaped);
    }

    // Jika term mengandung angka, ikutkan ke nilai_perolehan
    $numeric = preg_replace('/\D+/', '', (string) $term);
if ($numeric !== '') {
    $likeNum = $this->db->escapeLikeString($numeric);
    // CAST ke text dan pakai ILIKE biar case-insensitive (Postgres)
    $builder->orWhere("CAST(nilai_perolehan AS TEXT) ILIKE '%{$likeNum}%'", null, false);
}

// Coba cocokkan tanggal (bandingkan exact ke kolom tipe date)
$dateGuess = $this->normalizeDateToYmd($term);
if ($dateGuess) {
    $builder->orWhere('tanggal_perolehan =', $dateGuess);
    }

    $builder->groupEnd();
    return $builder;
}
/**
 * Ambil hasil pencarian + filter kelompok + sort + paginate.
 */
public function getSearchResults(
    ?string $term = null,
    ?string $kelompok = null,
    string $sort = 'nama_barang',
    string $order = 'asc',
    int $limit = 20,
    int $offset = 0
): array {
    $builder = $this->builder();

    if (!empty($kelompok)) {
        $builder->where('UPPER(kelompok)', strtoupper($kelompok));
    }

    $this->scopeSearchAll($builder, $term);

    // Sanitasi sorting
    $sort  = in_array($sort, $this->sortableColumns, true) ? $sort : 'nama_barang';
    $order = in_array(strtolower($order), ['asc','desc'], true) ? $order : 'asc';

    return $builder
        ->orderBy($sort, $order)
        ->limit($limit, $offset)
        ->get()
        ->getResultArray();
}

/**
 * Hitung total baris untuk hasil pencarian.
 */
public function countSearchResults(?string $term = null, ?string $kelompok = null): int
{
    $builder = $this->builder();

    if (!empty($kelompok)) {
        $builder->where('UPPER(kelompok)', strtoupper($kelompok));
    }

    $this->scopeSearchAll($builder, $term);

    return (int) $builder->countAllResults(false);
}

    
    // 9. BUSINESS LOGIC - Get data by kelompok (untuk filter kategori)
    public function getByKelompok($kelompok, $limit = null, $offset = 0)
    {
        $builder = $this->builder();
        
        $builder->where('UPPER(kelompok)', strtoupper($kelompok));
        
        if ($limit) {
            $builder->limit($limit, $offset);
        }
        
        return $builder->orderBy('kode_barang', 'ASC')
                      ->get()
                      ->getResultArray();
    }
    
    // 10. BUSINESS LOGIC - Count by kelompok
    public function countByKelompok($kelompok)
    {
        $builder = $this->builder();
        return $builder->where('UPPER(kelompok)', strtoupper($kelompok))
                      ->countAllResults();
    }
    
    // 11. BUSINESS LOGIC - Get statistics per kelompok
    public function getStatistikKelompok()
    {
        $builder = $this->builder();
        
        return $builder->select('kelompok, COUNT(*) as jumlah')
                      ->groupBy('kelompok')
                      ->orderBy('kelompok', 'ASC')
                      ->get()
                      ->getResultArray();
    }
    
    // 12. BUSINESS LOGIC - Get statistics per kondisi
    public function getStatistikKondisi($kelompok = '')
    {
        $builder = $this->builder();
        
        if (!empty($kelompok)) {
            $builder->where('UPPER(kelompok)', strtoupper($kelompok));
        }
        
        return $builder->select('kondisi, COUNT(*) as jumlah')
                      ->groupBy('kondisi')
                      ->orderBy('kondisi', 'ASC')
                      ->get()
                      ->getResultArray();
    }
    
    // 13. BUSINESS LOGIC - Get data untuk dashboard (ringkasan per kelompok)
    public function getDashboardData()
    {
        $data = [];
        
        // Total semua data
        $data['total_all'] = $this->countAll();
        
        // Per kelompok
        $kelompokList = ['KOMPUTER UNIT', 'PERALATAN KOMPUTER'];
        
        foreach ($kelompokList as $kelompok) {
            $key = strtolower(str_replace(' ', '_', $kelompok));
            $data[$key] = $this->countByKelompok($kelompok);
        }
        
        // Statistik kondisi
        $data['kondisi_stats'] = $this->getStatistikKondisi();
        
        return $data;
    }
    
    // 14. BUSINESS LOGIC - Check if kode_barang exists (untuk validasi import)
    public function isKodeBarangExists($kodeBarang, $excludeId = null)
    {
        $builder = $this->builder();
        $builder->where('kode_barang', $kodeBarang);
        
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->countAllResults() > 0;
    }
    
    // 15. BUSINESS LOGIC - Bulk insert for import (dengan validasi)
    public function bulkInsert($dataArray)
    {
        if (empty($dataArray)) {
            return false;
        }
        
        $builder = $this->builder();
        
        // Add timestamps to all records
        foreach ($dataArray as &$data) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        return $builder->insertBatch($dataArray);
    }
    
    // ============ VALIDATION METHODS (Pure PHP) ============
    
    // 16. Validation method - Pure PHP validation untuk import data
    public function validateImportData($data)
    {
        $errors = [];
        
        // Required fields
        $requiredFields = ['kode_barang', 'nama_barang', 'kelompok'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' tidak boleh kosong';
            }
        }
        
        // Length validation
        $lengthValidation = [
            'kode_barang' => 100,
            'nama_barang' => 255,
            'kelompok' => 100,
            'nup' => 100,
            'merk' => 100,
            'kondisi' => 50,
            'status_penggunaan' => 200,
            'processor' => 100,
            'memori' => 100,
            'hardisk' => 100,
            'monitor' => 100,
            'spek_lain' => 500
        ];
        
        foreach ($lengthValidation as $field => $maxLength) {
            if (isset($data[$field]) && strlen($data[$field]) > $maxLength) {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . " maksimal {$maxLength} karakter";
            }
        }
        
        // Kelompok validation (harus salah satu dari kelompok yang valid)
        $validKelompok = ['KOMPUTER UNIT', 'PERALATAN KOMPUTER'];
        if (isset($data['kelompok']) && !in_array(strtoupper($data['kelompok']), $validKelompok)) {
            $errors[] = 'Kelompok harus salah satu dari: ' . implode(', ', $validKelompok);
        }
        
        // Kondisi validation (jika ada)
        $validKondisi = ['BAIK', 'RUSAK RINGAN', 'RUSAK BERAT'];
        if (isset($data['kondisi']) && 
            !empty($data['kondisi']) && 
            !in_array(strtoupper($data['kondisi']), $validKondisi)) {
            $errors[] = 'Kondisi harus salah satu dari: ' . implode(', ', $validKondisi);
        }
        
        // Numeric validation
        $numericFields = ['kuantitas', 'nilai_perolehan', 'nilai_penyusutan', 'nilai_buku'];
        foreach ($numericFields as $field) {
            if (isset($data[$field]) && 
                !empty($data[$field]) && 
                !is_numeric($data[$field])) {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' harus berupa angka';
            }
        }
        
        // Date validation
        if (isset($data['tanggal_perolehan']) && 
            !empty($data['tanggal_perolehan'])) {
            $date = \DateTime::createFromFormat('Y-m-d', $data['tanggal_perolehan']);
            if (!$date || $date->format('Y-m-d') !== $data['tanggal_perolehan']) {
                $errors[] = 'Format tanggal perolehan tidak valid (Y-m-d)';
            }
        }
        
        return $errors;
    }
    
    // 17. Validation method - Validate kelompok (untuk form input)
    public function validateKelompok($kelompok)
    {
        $validKelompok = ['KOMPUTER UNIT', 'PERALATAN KOMPUTER'];
        return in_array(strtoupper($kelompok), $validKelompok);
    }
    
    // 18. Helper method - Get valid kelompok list
    public function getValidKelompok()
    {
        return ['KOMPUTER UNIT', 'PERALATAN KOMPUTER'];
    }
    
    // 19. Helper method - Clean data untuk import
/**
 * Bersihkan data sebelum disimpan ke database
 */
private function cleanImportData($data)
{
    $cleaned = [];
    
    // Bersihkan data dari rumus Excel
    foreach ($data as $key => $value) {
        // Cek apakah nilai dimulai dengan "=" yang menandakan rumus Excel
        if (is_string($value) && strpos($value, '=') === 0) {
            // Jika iya, kosongkan saja nilainya
            $cleaned[$key] = '';
        } 
        // Cek jika ada text seperti "Master Aset 'IK4'" atau variasinya
        else if (is_string($value) && 
                (strpos($value, 'Master Aset') !== false || 
                 strpos($value, '\'IK') !== false)) {
            $cleaned[$key] = '';
        } else {
            $cleaned[$key] = $value;
        }
    }
    
    // Set nilai default untuk field wajib
    $cleaned['nama_barang'] = $this->truncateString(trim($cleaned['nama_barang'] ?? '') ?: 'Unknown', 255);
    $cleaned['kelompok'] = $this->truncateString(strtoupper(trim($cleaned['kelompok'] ?? '')), 100);
    $cleaned['bidang'] = $this->truncateString(trim($cleaned['bidang'] ?? ''), 100);
    $cleaned['merk'] = $this->truncateString(trim($cleaned['merk'] ?? ''), 100);
    $cleaned['nup'] = $this->truncateString(trim($cleaned['nup'] ?? ''), 100);
    $cleaned['kondisi'] = $this->truncateString(strtoupper(trim($cleaned['kondisi'] ?? '')), 50);
    $cleaned['kuantitas'] = intval($cleaned['kuantitas'] ?? 1);
    $cleaned['nilai_perolehan'] = $this->safeFloat($cleaned['nilai_perolehan'] ?? 0);
    $cleaned['nilai_penyusutan'] = $this->safeFloat($cleaned['nilai_penyusutan'] ?? 0);
    $cleaned['nilai_buku'] = $this->safeFloat($cleaned['nilai_buku'] ?? 0);
    
    // Batasi panjang string untuk field lain
    $cleaned['kode_barang'] = $this->truncateString(trim($cleaned['kode_barang'] ?? ''), 100);
    $cleaned['status_penggunaan'] = $this->truncateString(trim($cleaned['status_penggunaan'] ?? ''), 100);
    $cleaned['processor'] = $this->truncateString(trim($cleaned['processor'] ?? ''), 100);
    $cleaned['memori'] = $this->truncateString(trim($cleaned['memori'] ?? ''), 100);
    $cleaned['hardisk'] = $this->truncateString(trim($cleaned['hardisk'] ?? ''), 100);
    $cleaned['monitor'] = $this->truncateString(trim($cleaned['monitor'] ?? ''), 100);
    $cleaned['spek_lain'] = $this->truncateString(trim($cleaned['spek_lain'] ?? ''), 500);
    $cleaned['pengguna_sebelumnya'] = $this->truncateString(trim($cleaned['pengguna_sebelumnya'] ?? ''), 255);
    $cleaned['pengguna_sekarang'] = $this->truncateString(trim($cleaned['pengguna_sekarang'] ?? ''), 255);
    $cleaned['status_barang'] = $this->truncateString(trim($cleaned['status_barang'] ?? ''), 100);
    $cleaned['keterangan'] = $this->truncateString(trim($cleaned['keterangan'] ?? ''), 255);
    
    // Handle date
    $cleaned['tanggal_perolehan'] = !empty($cleaned['tanggal_perolehan']) ? $cleaned['tanggal_perolehan'] : null;
    
    return $cleaned;
}
    
    // 20. Helper method - Safe float conversion
private function safeFloat($value)
{
    if (is_null($value) || $value === '') {
        return 0.0;
    }
    
    if (is_string($value)) {
        $value = str_replace(',', '.', $value);
    }
    
    return floatval($value);
}

/**
 * Konversi input tanggal user menjadi 'Y-m-d' bila memungkinkan.
 * Dipakai oleh scopeSearchAll() untuk memungkinkan pencarian tanggal.
 */
private function normalizeDateToYmd(string $raw): ?string
{
    $raw = trim($raw);
    if ($raw === '') return null;

    $formats = ['Y-m-d','d-m-Y','d/m/Y','d.m.Y','d m Y','m/d/Y'];
    foreach ($formats as $fmt) {
        $dt = \DateTime::createFromFormat($fmt, $raw);
        if ($dt && $dt->format($fmt) === $raw) {
            return $dt->format('Y-m-d');
        }
    }

    // fallback parser umum
    try {
        $dt = new \DateTime($raw);
        return $dt->format('Y-m-d');
    } catch (\Exception $e) {
        return null;
    }
}


public function importFromExcel($filePath)
{
    // Pastikan library PhpSpreadsheet tersedia
    if (!class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
        return [
            'success' => false,
            'message' => 'Library PhpSpreadsheet tidak tersedia. Pastikan composer require phpoffice/phpspreadsheet sudah diinstall.'
        ];
    }

    try {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        
        // Array untuk menampung data gabungan
        $combinedData = [];
        
        // Baca data kondisi dari sheet Lampiran BA terlebih dahulu
        $conditionData = $this->extractConditionData($spreadsheet);
        
        // Baca data dari setiap sheet
        $sheetsToRead = [
            'Master Aset', 
            'Master Aset ', // Dengan spasi di akhir
            'MTI',
            'BDI',
            'TU',
            'Daftar Tablet PC',
            'Daftar External Harddisk',
            'Daftar Laptop_All'
        ];
        
        foreach ($sheetsToRead as $sheetName) {
            if ($spreadsheet->getSheetByName($sheetName)) {
                $data = $this->readSheetData($spreadsheet, $sheetName);
                $combinedData = $this->mergeData($combinedData, $data);
            }
        }
        
        // Update kondisi barang berdasarkan data dari sheet lampiran BA
        $combinedData = $this->updateCondition($combinedData, $conditionData);
        
        // Konversi data ke format database dan simpan
        return $this->saveDataToDatabase($combinedData);
    } catch (\Exception $e) {
        log_message('error', 'Error saat import Excel: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error saat import Excel: ' . $e->getMessage()
        ];
    }
}

/**
 * Baca data kondisi dari sheet Lampiran BA
 */
private function extractConditionData($spreadsheet)
{
    $conditionData = [];
    $conditionSheets = [
        'Lampiran BA_Laptop',
        'Lampiran BA_Daftar Tablet PC',
        'Lampiran BA_External Harddisk'
    ];
    
    foreach ($conditionSheets as $sheetName) {
        if ($spreadsheet->getSheetByName($sheetName)) {
            try {
                $worksheet = $spreadsheet->getSheetByName($sheetName);
                
                // Ambil data dengan menangani error formula
                $sheetData = [];
                $highestRow = $worksheet->getHighestRow();
                $highestColumn = $worksheet->getHighestColumn();
                $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
                
                // Cari header row (biasanya baris pertama)
                $headerRow = 1;
                $headers = [];
                
                // Baca header row
                for ($col = 1; $col <= $highestColumnIndex; $col++) {
                    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                    $cellCoordinate = $colLetter . $headerRow;
                    
                    try {
                        $cellValue = $worksheet->getCell($cellCoordinate)->getValue();
                        if ($cellValue) {
                            $headers[$colLetter] = trim($cellValue);
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }
                
                // Cari indeks kolom untuk informasi yang kita butuhkan
                $namaBarangCol = null;
                $merkCol = null;
                $nupCol = null;
                $kondisiCol = null;
                
                foreach ($headers as $col => $header) {
                    if (stripos($header, 'Nama Barang') !== false) {
                        $namaBarangCol = $col;
                    } else if (stripos($header, 'Merk') !== false) {
                        $merkCol = $col;
                    } else if (stripos($header, 'NUP') !== false) {
                        $nupCol = $col;
                    } else if (stripos($header, 'Kondisi') !== false) {
                        $kondisiCol = $col;
                    }
                }
                
                // Jika kolom tidak ditemukan, lanjutkan ke sheet berikutnya
                if (!$namaBarangCol || !$merkCol || !$nupCol) {
                    continue;
                }
                
                // Mulai baca data dari baris setelah header
                for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
                    try {
                        // Baca nilai dari kolom-kolom penting
                        $nama_barang = trim($worksheet->getCell($namaBarangCol . $row)->getValue() ?? '');
                        $merk = trim($worksheet->getCell($merkCol . $row)->getValue() ?? '');
                        $nup = trim($worksheet->getCell($nupCol . $row)->getValue() ?? '');
                        
                        // Hanya proses jika ketiga nilai tersebut ada
                        if ($nama_barang && $merk && $nup) {
                            $key = $nama_barang . '|' . $merk . '|' . $nup;
                            
                            // Baca kondisi dari kolom kondisi jika ada
                            if ($kondisiCol) {
                                $kondisi = trim($worksheet->getCell($kondisiCol . $row)->getValue() ?? '');
                                if (!empty($kondisi)) {
                                    $conditionData[$key] = $kondisi;
                                }
                            } else {
                                // Cari kolom Rusak/Baik jika tidak ada kolom Kondisi eksplisit
                                for ($col = 1; $col <= $highestColumnIndex; $col++) {
                                    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                                    $header = $headers[$colLetter] ?? '';
                                    
                                    if (stripos($header, 'Rusak') !== false || stripos($header, 'Baik') !== false) {
                                        $cellValue = $worksheet->getCell($colLetter . $row)->getValue();
                                        
                                        if ($cellValue === '1' || $cellValue === 1 || strtoupper(trim($cellValue)) === 'YA') {
                                            // Jika nilainya 1 atau YA di kolom Rusak, tandai sebagai Rusak
                                            if (stripos($header, 'Rusak') !== false) {
                                                $conditionData[$key] = 'RUSAK';
                                            } else if (stripos($header, 'Baik') !== false) {
                                                $conditionData[$key] = 'BAIK';
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            } catch (\Exception $e) {
                log_message('warning', "Error membaca sheet $sheetName: " . $e->getMessage());
                continue;
            }
        }
    }
    
    return $conditionData;
}

private function readCellValue($worksheet, $cell, $isConditionColumn = false)
{
    try {
        // Coba ambil nilai terhitung (calculated value) terlebih dahulu
        $cellValue = $worksheet->getCell($cell)->getCalculatedValue();
        
        // Jika masih berupa rumus (dimulai dengan "="), coba ambil nilai mentah saja
        if (is_string($cellValue) && strpos($cellValue, '=') === 0) {
            $cellValue = $worksheet->getCell($cell)->getValue();
            // Jika masih berupa rumus, kosongkan saja
            if (is_string($cellValue) && strpos($cellValue, '=') === 0) {
                $cellValue = '';
            }
        }
        
        // Jika berisi "Master Aset" atau pola serupa, bersihkan
        if (is_string($cellValue) && 
            (stripos($cellValue, 'Master Aset') !== false || 
             preg_match("/[\'\"]\s*IK\d+\s*[\'\"]/i", $cellValue))) {
            $cellValue = '';
        }
        
        // Jika ini kolom kondisi dan nilainya adalah 1 atau angka, konversi ke BAIK
        if ($isConditionColumn && ($cellValue === '1' || $cellValue === 1)) {
            $cellValue = 'BAIK';
        }
        
        return $cellValue;
    } catch (\Exception $e) {
        return '';
    }
}

/**
 * Baca dan standarisasi data dari sebuah sheet
 */
/**
 * Baca dan standarisasi data dari sebuah sheet dengan penanganan error formula
 */
/**
 * Baca dan standarisasi data dari sebuah sheet
 */
private function readSheetData($spreadsheet, $sheetName)
{
    $data = [];
    
    if ($spreadsheet->getSheetByName($sheetName)) {
        try {
            $worksheet = $spreadsheet->getSheetByName($sheetName);
            
            // Ambil data dengan menangani error formula
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
            
            // Cari header row (biasanya baris pertama)
            $headerRow = 1;
            $headers = [];
            
            // Baca header row dengan penanganan error
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                $cellCoordinate = $colLetter . $headerRow;
                
                try {
                    $cellValue = $worksheet->getCell($cellCoordinate)->getValue();
                    if ($cellValue) {
                        $headers[$colLetter] = trim($cellValue);
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
            
            // Mapping header ke nama kolom yang diharapkan
            $headerMap = [];
            
            // Identifikasi kolom kondisi BAIK/RUSAK
            $baikColumn = null;
            $rusakColumn = null;
            
            foreach ($headers as $col => $header) {
                $header = trim($header);
                if (empty($header)) continue;
                
                // Standarisasi nama header
                if (stripos($header, 'Nama Barang') !== false) {
                    $headerMap['nama_barang'] = $col;
                } else if (stripos($header, 'Merk') !== false) {
                    $headerMap['merk'] = $col;
                } else if (stripos($header, 'NUP') !== false) {
                    $headerMap['nup'] = $col;
                } else if (stripos($header, 'Bidang') !== false) {
                    $headerMap['bidang'] = $col;
                } else if (stripos($header, 'Kode Barang') !== false) {
                    $headerMap['kode_barang'] = $col;
                } else if (stripos($header, 'Tanggal Perolehan') !== false) {
                    $headerMap['tanggal_perolehan'] = $col;
                } else if (stripos($header, 'Nilai Perolehan') !== false) {
                    $headerMap['nilai_perolehan'] = $col;
                } else if (stripos($header, 'Pengguna Sebelumnya') !== false) {
                    $headerMap['pengguna_sebelumnya'] = $col;
                } else if (stripos($header, 'Pengguna Sekarang') !== false ||
                          stripos($header, 'Pengguna / Lokasi') !== false ||
                          stripos($header, 'Pengguna') !== false) {
                    $headerMap['pengguna_sekarang'] = $col;
                } else if (stripos($header, 'Kondisi') !== false) {
                    $headerMap['kondisi'] = $col;
                } else if (stripos($header, 'Status Penggunaan') !== false ||
                          (stripos($header, 'Status') !== false && !stripos($header, 'Status Barang') !== false)) {
                    $headerMap['status_penggunaan'] = $col;
                } else if (stripos($header, 'Status Barang') !== false) {
                    $headerMap['status_barang'] = $col;
                } else if (stripos($header, 'Keterangan') !== false) {
                    $headerMap['keterangan'] = $col;
                } else if (stripos($header, 'Processor') !== false) {
                    $headerMap['processor'] = $col;
                } else if (stripos($header, 'Memori') !== false || stripos($header, 'RAM') !== false) {
                    $headerMap['memori'] = $col;
                } else if (stripos($header, 'Hardisk') !== false || stripos($header, 'Storage') !== false || stripos($header, 'SSD') !== false) {
                    $headerMap['hardisk'] = $col;
                } else if (stripos($header, 'Baik') !== false) {
                    $baikColumn = $col;
                } else if (stripos($header, 'Rusak') !== false) {
                    $rusakColumn = $col;
                }
            }
            
            // Tentukan kelompok berdasarkan sheet
            $defaultKelompok = 'KOMPUTER UNIT';
            if ($sheetName === 'Daftar External Harddisk') {
                $defaultKelompok = 'PERALATAN KOMPUTER';
            }
            
            // Default bidang berdasarkan nama sheet
            $defaultBidang = 'Master';
            if (stripos($sheetName, 'MTI') !== false) {
                $defaultBidang = 'MTI';
            } else if (stripos($sheetName, 'BDI') !== false) {
                $defaultBidang = 'BDI';
            } else if (stripos($sheetName, 'TU') !== false) {
                $defaultBidang = 'TU';
            }
            
            // Mulai dari baris setelah header
            for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
                try {
                    // Baca nilai dari kolom-kolom penting - minimal harus ada nama_barang
                    $nama_barang = '';
                    if (isset($headerMap['nama_barang'])) {
                        $nama_barang = trim($this->readCellValue($worksheet, $headerMap['nama_barang'] . $row) ?? '');
                    }
                    
                    // Skip jika tidak ada nama barang
                    if (empty($nama_barang)) continue;
                    
                    $merk = '';
                    if (isset($headerMap['merk'])) {
                        $merk = trim($this->readCellValue($worksheet, $headerMap['merk'] . $row) ?? '');
                    }
                    
                    $nup = '';
                    if (isset($headerMap['nup'])) {
                        $nup = trim($this->readCellValue($worksheet, $headerMap['nup'] . $row) ?? '');
                    }
                    
                    // Buat kunci unik untuk identifikasi
                    $key = $nama_barang . '|' . $merk . '|' . $nup;
                    
                    // Baca bidang
                    $bidang = $defaultBidang;
                    if (isset($headerMap['bidang'])) {
                        $tmpBidang = trim($this->readCellValue($worksheet, $headerMap['bidang'] . $row) ?? '');
                        if (!empty($tmpBidang)) {
                            $bidang = $tmpBidang;
                        }
                    }
                    
                    // Baca kode barang
                    $kode_barang = '';
                    if (isset($headerMap['kode_barang'])) {
                        $kode_barang = trim($this->readCellValue($worksheet, $headerMap['kode_barang'] . $row) ?? '');
                    }
                    
                    // Baca tanggal perolehan
                    $tanggal_perolehan = null;
                    if (isset($headerMap['tanggal_perolehan'])) {
                        $tmp = $this->readCellValue($worksheet, $headerMap['tanggal_perolehan'] . $row);
                        if (!empty($tmp)) {
                            try {
                                if (is_numeric($tmp)) {
                                    // Tanggal Excel
                                    $tanggal_perolehan = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tmp)->format('Y-m-d');
                                } else {
                                    // Coba format lain
                                    $date = date_create_from_format('Y-m-d', $tmp);
                                    if (!$date) $date = date_create_from_format('d-m-Y', $tmp);
                                    if (!$date) $date = date_create_from_format('d/m/Y', $tmp);
                                    if (!$date) $date = date_create($tmp);
                                    
                                    if ($date) {
                                        $tanggal_perolehan = $date->format('Y-m-d');
                                    }
                                }
                            } catch (\Exception $e) {
                                log_message('warning', "Error konversi tanggal: " . $e->getMessage());
                            }
                        }
                    }
                    
                    // Baca nilai perolehan
                    $nilai_perolehan = 0;
                    if (isset($headerMap['nilai_perolehan'])) {
                        $tmp = $this->readCellValue($worksheet, $headerMap['nilai_perolehan'] . $row);
                        if (!empty($tmp)) {
                            $nilai_perolehan = str_replace([' ', ',', '.'], '', $tmp);
                            if (!is_numeric($nilai_perolehan)) {
                                preg_match('/[0-9]+/', $nilai_perolehan, $matches);
                                $nilai_perolehan = $matches[0] ?? 0;
                            }
                        }
                    }
                    
                    // Baca pengguna
                    $pengguna_sebelumnya = '';
                    if (isset($headerMap['pengguna_sebelumnya'])) {
                        $pengguna_sebelumnya = trim($this->readCellValue($worksheet, $headerMap['pengguna_sebelumnya'] . $row) ?? '');
                    }
                    
                    $pengguna_sekarang = '';
                    if (isset($headerMap['pengguna_sekarang'])) {
                        $pengguna_sekarang = trim($this->readCellValue($worksheet, $headerMap['pengguna_sekarang'] . $row) ?? '');
                    }
                    
                    // Baca kondisi
                    $kondisi = '';
                    if (isset($headerMap['kondisi'])) {
                        $kondisi = trim($this->readCellValue($worksheet, $headerMap['kondisi'] . $row) ?? '');
                    }
                    
                    // Cek apakah ada nilai di kolom Baik/Rusak
                    if (empty($kondisi) && $baikColumn) {
                        $baikValue = trim($this->readCellValue($worksheet, $baikColumn . $row) ?? '');
                        if ($baikValue === '1' || $baikValue === 1 || strtoupper($baikValue) === 'YA') {
                            $kondisi = 'BAIK';
                        }
                    }
                    
                    if (empty($kondisi) && $rusakColumn) {
                        $rusakValue = trim($this->readCellValue($worksheet, $rusakColumn . $row) ?? '');
                        if ($rusakValue === '1' || $rusakValue === 1 || strtoupper($rusakValue) === 'YA') {
                            $kondisi = 'RUSAK';
                        }
                    }
                    
                    // Baca status
                    $status_penggunaan = '';
                    if (isset($headerMap['status_penggunaan'])) {
                        $status_penggunaan = trim($this->readCellValue($worksheet, $headerMap['status_penggunaan'] . $row) ?? '');
                    }
                    
                    $status_barang = '';
                    if (isset($headerMap['status_barang'])) {
                        $status_barang = trim($this->readCellValue($worksheet, $headerMap['status_barang'] . $row) ?? '');
                        
                        // Jika status_barang berisi nilai 1, konversi ke BAIK
                        if ($status_barang === '1' || $status_barang === 1) {
                            $status_barang = 'BAIK';
                        } else if ($status_barang === '0' || $status_barang === 0) {
                            $status_barang = 'RUSAK';
                        }
                    }
                    
                    $keterangan = '';
                    if (isset($headerMap['keterangan'])) {
                        $keterangan = trim($this->readCellValue($worksheet, $headerMap['keterangan'] . $row) ?? '');
                    }
                    
                    // Baca spesifikasi
                    $processor = '';
                    if (isset($headerMap['processor'])) {
                        $processor = trim($this->readCellValue($worksheet, $headerMap['processor'] . $row) ?? '');
                    }
                    
                    $memori = '';
                    if (isset($headerMap['memori'])) {
                        $memori = trim($this->readCellValue($worksheet, $headerMap['memori'] . $row) ?? '');
                    }
                    
                    $hardisk = '';
                    if (isset($headerMap['hardisk'])) {
                        $hardisk = trim($this->readCellValue($worksheet, $headerMap['hardisk'] . $row) ?? '');
                    }
                    
                    // Simpan data terstandarisasi
                    $data[$key] = [
                        'kode_barang' => $this->truncateString($kode_barang, 100),
                        'nama_barang' => $this->truncateString($nama_barang, 255),
                        'merk' => $this->truncateString($merk, 100),
                        'nup' => $this->truncateString($nup, 100),
                        'bidang' => $this->truncateString($bidang, 100),
                        'kelompok' => $this->truncateString($defaultKelompok, 100),
                        'processor' => $this->truncateString($processor, 100),
                        'memori' => $this->truncateString($memori, 100),
                        'hardisk' => $this->truncateString($hardisk, 100),
                        'tanggal_perolehan' => $tanggal_perolehan,
                        'nilai_perolehan' => $nilai_perolehan,
                        'pengguna_sebelumnya' => $this->truncateString($pengguna_sebelumnya, 255),
                        'pengguna_sekarang' => $this->truncateString($pengguna_sekarang, 255),
                        'kondisi' => $this->truncateString($kondisi, 50),
                        'status_penggunaan' => $this->truncateString($status_penggunaan, 100),
                        'status_barang' => $this->truncateString($status_barang, 100),
                        'keterangan' => $this->truncateString($keterangan, 255)
                    ];
                } catch (\Exception $e) {
                    log_message('warning', "Error membaca baris $row di sheet $sheetName: " . $e->getMessage());
                    continue;
                }
            }
        } catch (\Exception $e) {
            log_message('error', "Error membaca sheet $sheetName: " . $e->getMessage());
        }
    }
    
    return $data;
}

public function getKomputerForDisplay($filter = [])
{
    $data = $this->getKomputer($filter);
    
    // Konversi nilai numerik ke label status
    foreach ($data as &$item) {
        // Konversi kondisi dari angka ke label
        if (isset($item['kondisi'])) {
            if ($item['kondisi'] === '1' || $item['kondisi'] === 1) {
                $item['kondisi'] = 'BAIK';
            } else if ($item['kondisi'] === '0' || $item['kondisi'] === 0) {
                $item['kondisi'] = 'RUSAK';
            }
        }
        
        // Konversi status barang dari angka ke label jika perlu
        if (isset($item['status_barang'])) {
            if ($item['status_barang'] === '1' || $item['status_barang'] === 1) {
                $item['status_barang'] = 'BAIK';
            } else if ($item['status_barang'] === '0' || $item['status_barang'] === 0) {
                $item['status_barang'] = 'RUSAK';
            }
        }
    }
    
    return $data;
}
/**
 * Gabungkan data dari berbagai sheet
 */
private function mergeData($existingData, $newData)
{
    foreach ($newData as $key => $item) {
        if (isset($existingData[$key])) {
            // Barang sudah ada, gabungkan data baru ke data yang sudah ada
            foreach ($item as $field => $value) {
                // Jika field kosong di data existing tapi ada isinya di data baru, 
                // atau jika data existing kosong dan data baru ada isinya
                if ((!isset($existingData[$key][$field]) || empty($existingData[$key][$field])) && !empty($value)) {
                    $existingData[$key][$field] = $value;
                }
            }
        } else {
            // Barang belum ada, tambahkan ke data gabungan
            $existingData[$key] = $item;
        }
    }
    
    return $existingData;
}

/**
 * Update kondisi barang berdasarkan data dari sheet Lampiran BA
 */
private function updateCondition($data, $conditionData)
{
    foreach ($data as $key => $item) {
        if (isset($conditionData[$key]) && !empty($conditionData[$key])) {
            // Prioritaskan kondisi dari sheet Lampiran BA
            $data[$key]['kondisi'] = $this->truncateString($conditionData[$key], 50);
        }
    }
    
    return $data;
}

/**
 * Simpan data ke database
 */
/**
 * Batasi panjang string sesuai batas maksimum kolom
 */
private function truncateString($string, $maxLength = 100)
{
    if (strlen($string) <= $maxLength) {
        return $string;
    }
    return substr($string, 0, $maxLength);
}

/**
 * Simpan data ke database dengan pembatasan panjang field
 */
private function saveDataToDatabase($data)
{
    $db = \Config\Database::connect();
    $builder = $db->table('komputer');
    
    $successCount = 0;
    $failureCount = 0;
    $existingCount = 0;
    $updatedCount = 0;
    
    // Konversi array asosiatif ke array datar untuk batch insert/update
    $batchData = [];
    
    foreach ($data as $key => $item) {
        // Skip jika data tidak memiliki field wajib
        if (empty($item['nama_barang'])) {
            $failureCount++;
            continue;
        }
        
        // Set kelompok default jika tidak ada
        if (empty($item['kelompok'])) {
            $item['kelompok'] = 'KOMPUTER UNIT';
        }
        
        // Cek apakah data sudah ada berdasarkan kombinasi nama_barang, merk, nup
        $query = $builder->where('nama_barang', $item['nama_barang'])
                         ->where('merk', $item['merk']);
        
        if (!empty($item['nup'])) {
            $query = $query->where('nup', $item['nup']);
        }
        
        $existingRecord = $query->get()->getRowArray();
        
        if ($existingRecord) {
            // Update record yang sudah ada
            $updateData = [];
            foreach ($item as $field => $value) {
                // Prioritaskan data baru yang tidak kosong, kecuali untuk field yang sudah diisi di DB
                if (!empty($value) && (empty($existingRecord[$field]) || $existingRecord[$field] != $value)) {
                    $updateData[$field] = $value;
                }
            }
            
            if (!empty($updateData)) {
                // Add timestamp
                $updateData['updated_at'] = date('Y-m-d H:i:s');
                
                try {
                    $builder->where('id', $existingRecord['id'])
                            ->update($updateData);
                    $updatedCount++;
                } catch (\Exception $e) {
                    log_message('error', 'Error saat update data: ' . $e->getMessage());
                    $failureCount++;
                }
            } else {
                $existingCount++;
            }
        } else {
            // Tambahkan record baru
            // Add timestamps
            $item['created_at'] = date('Y-m-d H:i:s');
            $item['updated_at'] = date('Y-m-d H:i:s');
            
            // Bersihkan data
            $cleanedData = $this->cleanImportData($item);
            
            $batchData[] = $cleanedData;
        }
        
        // Proses data batch setiap 20 records untuk menghindari memory issues
        if (count($batchData) >= 20) {
            try {
                $insertResult = $builder->insertBatch($batchData);
                $successCount += $insertResult;
                $batchData = []; // Reset setelah batch diproses
            } catch (\Exception $e) {
                log_message('error', 'Error saat batch insert: ' . $e->getMessage());
                $failureCount += count($batchData);
                $batchData = []; // Reset batch jika error
            }
        }
    }
    
    // Proses sisa batch terakhir jika ada
    if (!empty($batchData)) {
        try {
            $insertResult = $builder->insertBatch($batchData);
            $successCount += $insertResult;
        } catch (\Exception $e) {
            log_message('error', 'Error saat batch insert: ' . $e->getMessage());
            $failureCount += count($batchData);
        }
    }
    
    return [
        'success' => true,
        'message' => "Impor Excel selesai: $successCount record ditambahkan, $updatedCount diupdate, $existingCount tidak diubah, $failureCount gagal.",
        'success_count' => $successCount,
        'updated_count' => $updatedCount,
        'existing_count' => $existingCount,
        'failure_count' => $failureCount
    ];
}
}