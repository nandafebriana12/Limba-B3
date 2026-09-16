<?php
// admin/sampah/index.php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('admin');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

// Handle Delete
if (isset($_GET['delete']) && validateCSRFToken($_GET['csrf'] ?? '')) {

    $id = $_GET['delete'];

    try {

        $stmt = $conn->prepare("DELETE FROM sampah_b3 WHERE id = ?");
        $stmt->execute([$id]);

        $_SESSION['success'] = "Data Limbah B3 berhasil dihapus.";

    } catch(PDOException $e) {

        $_SESSION['error'] = "Gagal menghapus data. Pastikan tidak ada data transaksi yang terkait.";

    }

    header("Location: index.php");
    exit;
}

require_once __DIR__ . '/../../includes/header.php';

// Ambil Data
$stmt = $conn->query("SELECT * FROM sampah_b3 ORDER BY id DESC");
$limbah = $stmt->fetchAll();
?>

<div class="page-header">
    <h1 class="page-title">Kelola Data Limbah B3</h1>

    <a href="create.php" class="btn btn-primary" style="width:auto;">
        + Tambah Limbah B3
    </a>
</div>

<div class="card">

    <div class="table-responsive">

        <table>

            <thead>

                <tr>

                    <th>No</th>

                    <th>Kode Limbah</th>

                    <th>Jenis Limbah</th>

                    <th>Nama Limbah</th>

                    <th>Satuan</th>

                    <th>Aksi</th>

                </tr>

            </thead>

            <tbody>

            <?php if(count($limbah) > 0): ?>

                <?php $no = 1; foreach($limbah as $row): ?>

                <tr>

                    <td><?= $no++ ?></td>

                    <td>

                        <?php

                        $kode = $row['kode_sampah'];

                        if (preg_match('/^(B3P|B3C|NB3)(\d+)$/', $kode, $match)) {

                            echo "<strong>" .
                                $match[1] .
                                " - " .
                                str_pad($match[2],4,'0',STR_PAD_LEFT)
                                . "</strong>";

                        } else {

                            echo "<strong>" . htmlspecialchars($kode) . "</strong>";

                        }

                        ?>

                    </td>

                    <td><?= htmlspecialchars($row['jenis_b3']) ?></td>

                    <td><?= htmlspecialchars($row['nama_sampah']) ?></td>

                    <td><?= htmlspecialchars($row['satuan']) ?></td>

                    <td>

                        <a
                            href="edit.php?id=<?= $row['id'] ?>"
                            class="btn btn-secondary"
                            style="padding:5px 10px;font-size:.8rem;"
                        >
                            Edit
                        </a>

                        <a
                            href="index.php?delete=<?= $row['id'] ?>&csrf=<?= generateCSRFToken() ?>"
                            class="btn btn-danger"
                            style="padding:5px 10px;font-size:.8rem;"
                            onclick="return confirm('Yakin ingin menghapus data limbah ini?')"
                        >
                            Hapus
                        </a>

                    </td>

                </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>

                    <td colspan="6" style="text-align:center;">

                        Belum ada data Limbah B3.

                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>