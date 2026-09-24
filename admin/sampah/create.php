<?php
// admin/sampah/create.php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('admin');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {

        $error = "Sesi telah kedaluwarsa. Silakan coba lagi.";

    } else {

        $jenis_b3 = sanitize($_POST['jenis_b3'] ?? '');
        $nama_sampah = sanitize($_POST['nama_sampah'] ?? '');
        $satuan = sanitize($_POST['satuan'] ?? '');

        if (empty($jenis_b3) || empty($nama_sampah) || empty($satuan)) {

            $error = "Semua data wajib diisi.";

        } else {

            try {

                // Generate kode limbah berdasarkan jenis
                $prefix = $jenis_b3;

                $stmtKode = $conn->prepare("
                    SELECT TOP 1 kode_sampah
                    FROM sampah_b3
                    WHERE kode_sampah LIKE ?
                    ORDER BY kode_sampah DESC
                ");

                $stmtKode->execute([$prefix . '%']);

                $last = $stmtKode->fetchColumn();

                if ($last) {
                    $angka = intval(substr($last, strlen($prefix)));
                    $angka++;
                } else {
                    $angka = 1;
                }

                $kode_sampah = $prefix . str_pad($angka, 3, '0', STR_PAD_LEFT);

                $stmt = $conn->prepare("
                    INSERT INTO sampah_b3
                    (
                        kode_sampah,
                        jenis_b3,
                        nama_sampah,
                        satuan
                    )
                    VALUES
                    (
                        ?,
                        ?,
                        ?,
                        ?
                    )
                ");

                $stmt->execute([
                    $kode_sampah,
                    $jenis_b3,
                    $nama_sampah,
                    $satuan
                ]);

                $_SESSION['success'] = "Data Limbah berhasil ditambahkan dengan kode $kode_sampah.";

                header("Location: index.php");
                exit;

            } catch (PDOException $e) {

                $error = "Terjadi kesalahan : " . $e->getMessage();

            }

        }

    }

}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Tambah Data Limbah B3</h1>

    <a href="index.php" class="btn btn-secondary" style="width:auto;">
        &larr; Kembali
    </a>
</div>

<div class="card" style="max-width:650px;">

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

        <!-- Jenis Limbah -->

        <div class="form-group">

            <label>Jenis Limbah B3</label>

            <select
                name="jenis_b3"
                id="jenis_b3"
                class="form-control"
                required
            >

                <option value="">-- Pilih Jenis Limbah --</option>

                <option value="B3P">
                    B3P (Limbah B3 Padat)
                </option>

                <option value="B3C">
                    B3C (Limbah B3 Cair)
                </option>

                <option value="NB3">
                    NB3 (Limbah Non B3)
                </option>

            </select>

        </div>

        <!-- Nama Limbah -->

        <div class="form-group">

            <label>Nama Limbah</label>

            <select
                name="nama_sampah"
                id="nama_sampah"
                class="form-control"
                required maxlength="255"
            >

                <option value="">
                    -- Pilih Nama Limbah --
                </option>

            </select>

        </div>

        <!-- Satuan -->

        <div class="form-group">

            <label>Satuan</label>

            <select
                name="satuan"
                class="form-control"
                required
            >

                <option value="">-- Pilih Satuan --</option>

                <option value="Kg">Kg</option>

                <option value="Liter">Liter</option>

                <option value="Pcs">Pcs</option>


            </select>

        </div>

        <button
            type="submit"
            class="btn btn-primary"
            style="width:auto;"
        >

            Simpan Data

        </button>

    </form>

</div>

<script>

const dataLimbah = {

    B3P : [

        "Limbah terkontaminasi B3",

        "Sludge logam mengandung minyak",

        "Kain majun bekas",
        
        "Baterai"

    ],

    B3C : [

        "Emulsi minyak proses cutting dan coolant",

        "Limbah cat dan varnish",

        "Minyak pelumas bekas (Oli Hidrolik, Oli Mesin, Oli Gear, dll.)"

    ],

    NB3 : [

        "Kertas",

        "Plastik",

        "Kardus",

        "Botol"

    ]

};

document.getElementById("jenis_b3").addEventListener("change", function(){

    let jenis = this.value;

    let nama = document.getElementById("nama_sampah");

    nama.innerHTML = '<option value="">-- Pilih Nama Limbah --</option>';

    if(dataLimbah[jenis]){

        dataLimbah[jenis].forEach(function(item){

            let option = document.createElement("option");

            option.value = item;

            option.text = item;

            nama.appendChild(option);

        });

    }

});

</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
