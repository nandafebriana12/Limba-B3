<?php
// admin/transaksi/create_keluar.php

require_once __DIR__ . '/../../includes/auth.php';
requireRole('admin');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

$error = "";

// ===============================
// Ambil Data Limbah
// ===============================
$stmt = $conn->query("
    SELECT
        id,
        kode_sampah,
        nama_sampah,
        satuan
    FROM sampah_b3
    ORDER BY nama_sampah
");
$limbah_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ===============================
// Ambil Vendor
// ===============================
$stmt = $conn->query("
    SELECT
        id,
        kode_vendor,
        nama_vendor
    FROM vendors
    ORDER BY nama_vendor
");
$vendor_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

$limbah_json = json_encode($limbah_list);

// ===============================
// Simpan Data
// ===============================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {

        $error = "Sesi telah kedaluwarsa.";

    } else {

        $vendor_id = $_POST['vendor_id'] ?? '';
        $tanggal_keluar = $_POST['tanggal_keluar'] ?? '';
        $keterangan = sanitize($_POST['keterangan'] ?? '');

        if (!empty($tanggal_keluar)) {
            try {
                $tanggalObj = new DateTime($tanggal_keluar);
                $tanggal_keluar = $tanggalObj->format('Y-m-d H:i:s');
            } catch (Exception $e) {
                $error = "Format tanggal keluar tidak valid.";
            }
        }

        $limbah_ids = $_POST['limbah_id'] ?? [];
        $qty = $_POST['quantity'] ?? [];

        if (
            empty($vendor_id) ||
            empty($tanggal_keluar)
        ) {

            $error = "Vendor dan tanggal wajib diisi.";

        } elseif (count($limbah_ids) == 0) {

            $error = "Minimal satu limbah dipilih.";

        } else {

            try{

                $conn->beginTransaction();

                // ===============================
                // Generate Nomor Transaksi
                // ===============================

                $no_transaksi = generateKode(
                    $conn,
                    'transaksi_keluar',
                    'no_transaksi',
                    'TK'
                );

                // ===============================
                // Simpan Header
                // ===============================

                $stmtHeader = $conn->prepare("
                    INSERT INTO transaksi_keluar
                    (
                        no_transaksi,
                        vendor_id,
                        tanggal_keluar,
                        keterangan
                    )
                    VALUES
                    (
                        ?,
                        ?,
                        ?,
                        ?
                    )
                ");

                $stmtHeader->execute([
                    $no_transaksi,
                    $vendor_id,
                    $tanggal_keluar,
                    $keterangan
                ]);

                $transaksi_id = $conn->lastInsertId();

                // ===============================
                // Simpan Detail
                // ===============================

                $stmtDetail = $conn->prepare("
                    INSERT INTO detail_keluar
                    (
                        transaksi_keluar_id,
                        sampah_b3_id,
                        quantity
                    )
                    VALUES
                    (
                        ?,
                        ?,
                        ?
                    )
                ");

                for($i=0;$i<count($limbah_ids);$i++){

                    if(
                        !empty($limbah_ids[$i]) &&
                        $qty[$i] > 0
                    ){

                        $stmtDetail->execute([

                            $transaksi_id,
                            $limbah_ids[$i],
                            $qty[$i]

                        ]);

                    }

                }

                $conn->commit();

                $_SESSION['success'] =
                    "Transaksi Limbah Keluar berhasil disimpan. No Transaksi : "
                    .$no_transaksi;

                header("Location: index.php");
                exit;

            }catch(PDOException $e){

                $conn->rollBack();

                $error =
                    "Terjadi kesalahan : "
                    .$e->getMessage();

            }

        }

    }

}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">

    <h1 class="page-title">
        Input Limbah Keluar
    </h1>

    <a
        href="index.php"
        class="btn btn-secondary"
        style="width:auto;"
    >
        &larr; Kembali
    </a>

</div>

<div class="card">

<?php if($error): ?>

<div class="alert alert-danger">
    <?= $error ?>
</div>

<?php endif; ?>

<form method="POST">

<input
type="hidden"
name="csrf_token"
value="<?= generateCSRFToken() ?>"
>

<div
style="
display:grid;
grid-template-columns:1fr 1fr 1fr;
gap:20px;
">

<div class="form-group">

<label>Vendor</label>

<select
name="vendor_id"
class="form-control"
required
>

<option value="">
-- Pilih Vendor --
</option>

<?php foreach($vendor_list as $v): ?>

<option value="<?= $v['id'] ?>">

<?= htmlspecialchars(
$v['kode_vendor']." - ".$v['nama_vendor']
) ?>

</option>

<?php endforeach; ?>

</select>

</div>

<div class="form-group">

<label>Tanggal Keluar</label>

<input
type="datetime-local"
name="tanggal_keluar"
class="form-control"
required
>

</div>

<div class="form-group">

<label>Keterangan</label>

<input
type="text"
name="keterangan"
class="form-control"
>

</div>

</div>

<hr>

<h3>Detail Limbah Keluar</h3>

<div class="table-responsive">

<table id="tabelDetail">

<thead>

<tr>

<th>Pilih Limbah</th>

<th width="180">Jumlah</th>

<th width="120">Satuan</th>

<th width="120">Aksi</th>

</tr>

</thead>

<tbody id="detailBody">

<tr class="item-row">

<td>

<select
name="limbah_id[]"
class="form-control limbah-select"
required
onchange="ubahSatuan(this)"
>

<option value="">
-- Pilih Limbah --
</option>

<?php foreach($limbah_list as $l): ?>

<option value="<?= $l['id'] ?>">

<?= htmlspecialchars(
$l['kode_sampah']." - ".$l['nama_sampah']
) ?>

</option>

<?php endforeach; ?>

</select>

</td>

<td>

<input
type="number"
name="quantity[]"
class="form-control"
min="0.01"
step="0.01"
required
>

</td>

<td>

<input
type="text"
class="form-control satuan"
readonly
>

</td>

<td>

<button
type="button"
class="btn btn-danger"
onclick="hapusBaris(this)"
>

Hapus

</button>

</td>

</tr>

</tbody>

</table>

</div>

<div style="margin-top:20px;">

<button
type="button"
class="btn btn-secondary"
style="width:auto;"
onclick="tambahBaris()"
>

+ Tambah Baris

</button>

</div>

<hr style="margin:30px 0;">

<button
type="submit"
class="btn btn-primary"
style="width:220px;"
>

Simpan Transaksi

</button>

</form>

</div>

<script>

const limbahData = <?= $limbah_json ?>;

function ubahSatuan(select){

let row = select.closest(".item-row");

let satuan = row.querySelector(".satuan");

let item = limbahData.find(x => x.id == select.value);

if(item){

satuan.value = item.satuan;

}else{

satuan.value = "";

}

}

function tambahBaris(){

let tbody = document.getElementById("detailBody");

let tr = document.createElement("tr");

tr.className = "item-row";

tr.innerHTML = `

<td>

<select
name="limbah_id[]"
class="form-control limbah-select"
required
onchange="ubahSatuan(this)"
>

<option value="">
-- Pilih Limbah --
</option>

<?php foreach($limbah_list as $l): ?>

<option value="<?= $l['id'] ?>">

<?= htmlspecialchars(
$l['kode_sampah']." - ".$l['nama_sampah']
) ?>

</option>

<?php endforeach; ?>

</select>

</td>

<td>

<input
type="number"
name="quantity[]"
class="form-control"
min="0.01"
step="0.01"
required
>

</td>

<td>

<input
type="text"
class="form-control satuan"
readonly
>

</td>

<td>

<button
type="button"
class="btn btn-danger"
onclick="hapusBaris(this)"
>

Hapus

</button>

</td>

`;

tbody.appendChild(tr);

}
function hapusBaris(btn){

    let tbody = document.getElementById("detailBody");

    if(tbody.children.length > 1){

        btn.closest(".item-row").remove();

    }else{

        alert("Minimal harus ada satu data limbah.");

    }

}

</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>