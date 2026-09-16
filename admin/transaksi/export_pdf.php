<?php
// admin/transaksi/export_pdf.php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('admin');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

$autoload_path = __DIR__ . '/../../vendor/autoload.php';
if (!file_exists($autoload_path)) {
    die("Library PDF belum terinstall. Silakan jalankan perintah 'composer install' di folder root aplikasi.");
}
require_once $autoload_path;

// Fetch Transaksi Keluar
$sql_keluar = "SELECT tk.*, v.nama_vendor, 
        (SELECT SUM(quantity) FROM detail_keluar WHERE transaksi_keluar_id = tk.id) as total_items 
        FROM transaksi_keluar tk 
        JOIN vendors v ON tk.vendor_id = v.id 
        ORDER BY tk.id DESC";
$stmt_keluar = $conn->query($sql_keluar);
$t_keluar = $stmt_keluar->fetchAll();

// Extend TCPDF
class MYPDF extends TCPDF {
    public function Header() {
        $this->SetFont('helvetica', 'B', 14);
        $this->Cell(0, 15, 'Laporan Transaksi Keluar Limbah B3', 0, false, 'C', 0, '', 0, false, 'M', 'M');
    }
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Halaman '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Sistem Tracking Limbah B3');
$pdf->SetTitle('Laporan Limbah Keluar');
$pdf->SetMargins(15, 25, 15);
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(10);
$pdf->SetAutoPageBreak(TRUE, 20);
$pdf->AddPage();

$html = '
<style>
    th { background-color: #f2f2f2; font-weight: bold; padding: 5px; }
    td { padding: 5px; }
</style>
<table border="1" cellpadding="5">
    <thead>
        <tr>
            <th width="10%">No</th>
            <th width="20%">No. Transaksi</th>
            <th width="25%">Vendor Tujuan</th>
            <th width="20%">Tanggal Keluar</th>
            <th width="25%">Total Biaya</th>
        </tr>
    </thead>
    <tbody>';

$no = 1;
if(count($t_keluar) > 0) {
    foreach($t_keluar as $row) {
        $html .= '<tr>
            <td width="10%">'.$no++.'</td>
            <td width="20%">'.$row['no_transaksi'].'</td>
            <td width="25%">'.$row['nama_vendor'].'</td>
            <td width="20%">'.date('d/m/Y H:i', strtotime($row['tanggal_keluar'])).'</td>
            <td width="25%">'.formatRupiah($row['total_biaya']).'</td>
        </tr>';
    }
} else {
    $html .= '<tr><td colspan="5" align="center">Belum ada transaksi keluar.</td></tr>';
}

$html .= '</tbody></table>';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('laporan_limbah_keluar.pdf', 'I');
