<?php
// admin/transaksi/export_excel.php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('admin');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

$autoload_path = __DIR__ . '/../../vendor/autoload.php';
if (!file_exists($autoload_path)) {
    die("Library Excel belum terinstall. Silakan jalankan perintah 'composer install' di folder root aplikasi.");
}
require_once $autoload_path;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Fetch Transaksi Keluar
$sql_keluar = "SELECT tk.*, v.nama_vendor, 
        (SELECT SUM(quantity) FROM detail_keluar WHERE transaksi_keluar_id = tk.id) as total_items 
        FROM transaksi_keluar tk 
        JOIN vendors v ON tk.vendor_id = v.id 
        ORDER BY tk.id DESC";
$stmt_keluar = $conn->query($sql_keluar);
$t_keluar = $stmt_keluar->fetchAll();

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Transaksi Keluar');

// Headers
$sheet->setCellValue('A1', 'No');
$sheet->setCellValue('B1', 'No. Transaksi');
$sheet->setCellValue('C1', 'Vendor Tujuan');
$sheet->setCellValue('D1', 'Tanggal Keluar');
$sheet->setCellValue('E1', 'Keterangan');
$sheet->setCellValue('F1', 'Total Biaya');

// Bold Headers
$sheet->getStyle('A1:F1')->getFont()->setBold(true);

$rowNum = 2;
$no = 1;

foreach($t_keluar as $row) {
    $sheet->setCellValue('A' . $rowNum, $no++);
    $sheet->setCellValue('B' . $rowNum, $row['no_transaksi']);
    $sheet->setCellValue('C' . $rowNum, $row['nama_vendor']);
    $sheet->setCellValue('D' . $rowNum, date('d/m/Y H:i', strtotime($row['tanggal_keluar'])));
    $sheet->setCellValue('E' . $rowNum, $row['keterangan']);
    $sheet->setCellValue('F' . $rowNum, $row['total_biaya']);
    
    // Format currency for Total Biaya
    $sheet->getStyle('F' . $rowNum)->getNumberFormat()->setFormatCode('"Rp "#,##0_-');
    
    $rowNum++;
}

// Auto size columns
foreach (range('A', 'F') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Output to Browser
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="laporan_limbah_keluar.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
