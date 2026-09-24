<?php
// upt/transaksi_masuk/create.php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('upt');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

$error = '';

// Get Sampah B3 List
$stmt = $conn->query("SELECT id, kode_sampah, nama_sampah, satuan FROM sampah_b3 ORDER BY nama_sampah ASC");
$sampah_list = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = "Sesi telah kedaluwarsa. Silakan coba lagi.";
    } else {
        $tanggal_masuk = $_POST['tanggal_masuk'] ?? '';
        $keterangan = sanitize($_POST['keterangan'] ?? '');
        $sampah_ids = $_POST['sampah_id'] ?? [];
        $quantities = $_POST['quantity'] ?? [];

        // Konversi datetime-local ke format SQL Server.
        if (!empty($tanggal_masuk)) {
            try {
                $tanggalObj = new DateTime($tanggal_masuk);
                $tanggal_masuk = $tanggalObj->format('Y-m-d H:i:s');
            } catch (Exception $e) {
                $error = "Format tanggal masuk tidak valid.";
            }
        }
        
        if (empty($tanggal_masuk)) {
            if (empty($error)) {
                $error = "Tanggal masuk harus diisi.";
            }
        } elseif (empty($sampah_ids) || count($sampah_ids) == 0) {
            $error = "Pilih minimal satu limbah B3.";
        } else {
            try {
                $conn->beginTransaction();
                
                // Generate No Transaksi (TM001)
                $no_transaksi = generateKode($conn, 'transaksi_masuk', 'no_transaksi', 'TM');
                
                // Insert Header
                $stmt = $conn->prepare("INSERT INTO transaksi_masuk (no_transaksi, user_id, tanggal_masuk, keterangan) VALUES (?, ?, ?, ?)");
                $stmt->execute([$no_transaksi, $_SESSION['user_id'], $tanggal_masuk, $keterangan]);
                
                $transaksi_id = $conn->lastInsertId();
                
                // Insert Details
                $stmtDetail = $conn->prepare("INSERT INTO detail_masuk (transaksi_masuk_id, sampah_b3_id, quantity) VALUES (?, ?, ?)");
                
                for ($i = 0; $i < count($sampah_ids); $i++) {
                    $s_id = $sampah_ids[$i];
                    $qty = $quantities[$i];
                    if ($qty > 0) {
                        $stmtDetail->execute([$transaksi_id, $s_id, $qty]);
                    }
                }
                
                $conn->commit();
                
                $_SESSION['success'] = "Transaksi masuk berhasil disimpan dengan No: $no_transaksi.";
                header("Location: index.php");
                exit;
            } catch(PDOException $e) {
                $conn->rollBack();
                $error = "Terjadi kesalahan: " . $e->getMessage();
            }
        }
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Input Limbah Masuk</h1>
    <a href="index.php" class="btn btn-secondary" style="width: auto;">&larr; Kembali</a>
</div>

<div class="card">
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form action="" method="POST" id="formTransaksi">
        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="tanggal_masuk">Tanggal Masuk</label>
                <input type="datetime-local" id="tanggal_masuk" name="tanggal_masuk" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <input type="text" id="keterangan" name="keterangan" class="form-control">
            </div>
        </div>

        <h3 style="margin: 20px 0 10px;">Detail Limbah B3</h3>
        
        <div class="table-responsive">
            <table id="tabelDetail">
                <thead>
                    <tr>
                        <th>Pilih Limbah</th>
                        <th width="150">Quantity</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody id="detailBody">
                    <tr>
                        <td>
                            <select name="sampah_id[]" class="form-control" required>
                                <option value="">-- Pilih Limbah --</option>
                                <?php foreach($sampah_list as $s): ?>
                                    <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['kode_sampah'] . ' - ' . $s['nama_sampah'] . ' (' . $s['satuan'] . ')') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <input type="number" name="quantity[]" class="form-control" min="0.01" step="0.01" required>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger" onclick="hapusBaris(this)">Hapus</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div style="margin-top: 15px;">
            <button type="button" class="btn btn-secondary" style="width: auto;" onclick="tambahBaris()">+ Tambah Baris</button>
        </div>
        
        <hr style="margin: 30px 0; border: 0; border-top: 1px solid var(--border);">
        
        <button type="submit" class="btn btn-primary" style="width: 200px;">Simpan Transaksi</button>
    </form>
</div>

<script>
function tambahBaris() {
    var tbody = document.getElementById('detailBody');
    var tr = document.createElement('tr');
    tr.innerHTML = `
        <td>
            <select name="sampah_id[]" class="form-control" required>
                <option value="">-- Pilih Limbah --</option>
                <?php foreach($sampah_list as $s): ?>
                    <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['kode_sampah'] . ' - ' . $s['nama_sampah'] . ' (' . $s['satuan'] . ')') ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td>
            <input type="number" name="quantity[]" class="form-control" min="0.01" step="0.01" required>
        </td>
        <td>
            <button type="button" class="btn btn-danger" onclick="hapusBaris(this)">Hapus</button>
        </td>
    `;
    tbody.appendChild(tr);
}

function hapusBaris(btn) {
    var tbody = document.getElementById('detailBody');
    if (tbody.children.length > 1) {
        var row = btn.parentNode.parentNode;
        row.parentNode.removeChild(row);
    } else {
        alert("Minimal harus ada satu baris detail.");
    }
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
