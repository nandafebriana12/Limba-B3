<?php
// admin/transaksi/index.php

require_once __DIR__ . '/../../includes/auth.php';
requireRole('admin');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/header.php';

/* ======================================================
   TRANSAKSI LIMBAH MASUK
====================================================== */

$sql_masuk = "
SELECT
    tm.*,
    u.username,
    p.nama_upt,

    (
        SELECT SUM(quantity)
        FROM detail_masuk
        WHERE transaksi_masuk_id = tm.id
    ) AS total_items

FROM transaksi_masuk tm

JOIN users u
ON tm.user_id = u.id

LEFT JOIN upts p
ON u.upt_id = p.id

ORDER BY tm.id DESC
";

$stmt_masuk = $conn->query($sql_masuk);

$t_masuk = $stmt_masuk->fetchAll();

/* ======================================================
   TRANSAKSI LIMBAH KELUAR
====================================================== */

$sql_keluar = "
SELECT
    tk.*,
    v.nama_vendor,

    (
        SELECT SUM(quantity)
        FROM detail_keluar
        WHERE transaksi_keluar_id = tk.id
    ) AS total_items

FROM transaksi_keluar tk

JOIN vendors v
ON tk.vendor_id = v.id

ORDER BY tk.id DESC
";

$stmt_keluar = $conn->query($sql_keluar);

$t_keluar = $stmt_keluar->fetchAll();

?>

<div class="page-header">

    <h1 class="page-title">

        Riwayat Transaksi Limbah B3

    </h1>

    <div
    style="
    display:flex;
    gap:10px;
    ">

        <a
        href="create_keluar.php"
        class="btn btn-primary"
        style="width:auto;"
        >

            + Input Limbah Keluar

        </a>

        <a
        href="export_excel.php"
        class="btn btn-secondary"
        style="width:auto;"
        >

            Export Excel

        </a>

        <a
        href="export_pdf.php"
        class="btn btn-danger"
        style="width:auto;"
        >

            Export PDF

        </a>

    </div>

</div>
<!-- ==========================================
     LIMBAH MASUK
========================================== -->

<div class="card">

    <h2
    class="card-title"
    style="
    margin-bottom:20px;
    color:var(--primary);
    ">

        Limbah Masuk (Dari UPT)

    </h2>

    <div class="table-responsive">

        <table>

            <thead>

                <tr>

                    <th>No. Transaksi</th>

                    <th>Asal UPT</th>

                    <th>Tanggal Masuk</th>

                    <th>Total Qty</th>

                    <th>Aksi</th>

                </tr>

            </thead>

            <tbody>

            <?php if(count($t_masuk) > 0): ?>

                <?php foreach($t_masuk as $row): ?>

                <tr>

                    <td>

                        <strong>

                            <?= htmlspecialchars($row['no_transaksi']) ?>

                        </strong>

                    </td>

                    <td>

                        <?= htmlspecialchars($row['nama_upt'] ?? '-') ?>

                        <br>

                        <small>

                            Oleh :

                            <?= htmlspecialchars($row['username']) ?>

                        </small>

                    </td>

                    <td>

                        <?= date(
                            'd/m/Y H:i',
                            strtotime($row['tanggal_masuk'])
                        ) ?>

                    </td>

                    <td>

                        <?= (float)$row['total_items'] ?>

                    </td>

                    <td>

                        <a
                        href="detail_masuk.php?id=<?= $row['id'] ?>"
                        class="btn btn-secondary"
                        style="
                        padding:6px 12px;
                        width:auto;
                        ">

                            Detail

                        </a>

                    </td>

                </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>

                    <td
                    colspan="5"
                    style="text-align:center;">

                        Belum ada data Limbah Masuk.

                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>
<!-- ==========================================
     LIMBAH KELUAR
========================================== -->

<div class="card" style="margin-top:30px;">

    <h2
    class="card-title"
    style="
    margin-bottom:20px;
    color:var(--warning);
    ">

        Limbah Keluar

    </h2>

    <div class="table-responsive">

        <table>

            <thead>

                <tr>

                    <th>No. Transaksi</th>

                    <th>Vendor Tujuan</th>

                    <th>Tanggal Keluar</th>

                    <th>Total Qty</th>

                    <th>Aksi</th>

                </tr>

            </thead>

            <tbody>

            <?php if(count($t_keluar) > 0): ?>

                <?php foreach($t_keluar as $row): ?>

                <tr>

                    <td>

                        <strong>

                            <?= htmlspecialchars($row['no_transaksi']) ?>

                        </strong>

                    </td>

                    <td>

                        <?= htmlspecialchars($row['nama_vendor']) ?>

                    </td>

                    <td>

                        <?= date(
                            'd/m/Y H:i',
                            strtotime($row['tanggal_keluar'])
                        ) ?>

                    </td>

                    <td>

                        <?= (float)$row['total_items'] ?>

                    </td>

                    <td>

                        <a
                        href="detail_keluar.php?id=<?= $row['id'] ?>"
                        class="btn btn-secondary"
                        style="
                        padding:6px 12px;
                        width:auto;
                        ">

                            Detail

                        </a>

                    </td>

                </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>

                    <td
                    colspan="5"
                    style="text-align:center;">

                        Belum ada data Limbah Keluar.

                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>