<?php
// Halaman Kelola Koleksi (Ubah & Hapus) — Khusus Kurator Terautentikasi
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/data/koleksi.php';
auth_check();

if (empty($_SESSION['kurator_logged_in'])) {
    header("Location: login.php?pesan=wajib_login");
    exit;
}

$hasil_aksi = null;

// Proses Simpan Perubahan (Ubah) atau Hapus Koleksi via POST
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $aksi = $_POST['action'] ?? '';
    $id_target = (int)($_POST['id_koleksi'] ?? 0);

    if ($aksi === 'delete' && $id_target > 0) {
        $hasil_aksi = delete_koleksi_db($id_target);
    } elseif ($aksi === 'update' && $id_target > 0) {
        $hasil_aksi = save_koleksi_db([
            'nomor_inventaris' => $_POST['nomor_inventaris'] ?? '',
            'judul_koleksi'    => $_POST['judul_koleksi'] ?? '',
            'kategori_koleksi' => $_POST['kategori_koleksi'] ?? 'lukisan',
            'seniman_pencipta' => $_POST['seniman_pencipta'] ?? '',
            'tahun_perolehan'  => $_POST['tahun_perolehan'] ?? '',
            'asal_daerah'      => $_POST['asal_daerah'] ?? '',
            'medium_teknik'    => $_POST['medium_teknik'] ?? '',
            'dimensi'          => $_POST['dimensi'] ?? '',
            'lokasi_ruang'     => $_POST['lokasi_ruang'] ?? '',
            'deskripsi'        => $_POST['deskripsi'] ?? '',
        ], $id_target);
    }
    // Muat ulang daftar koleksi setelah perubahan
    if ($hasil_aksi && $hasil_aksi['ok'] && ($db = get_db_connection())) {
        $KOLEKSI_DATA = [];
        foreach ($db->query("SELECT * FROM koleksi_seni ORDER BY id_koleksi ASC")->fetchAll() as $row) {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $row['judul_koleksi']), '-'));
            $KOLEKSI_DATA[$slug] = [
                'id'            => $slug,
                'id_koleksi'    => (int)$row['id_koleksi'],
                'inventaris'    => $row['nomor_inventaris'],
                'judul'         => $row['judul_koleksi'],
                'kategori'      => ucfirst($row['kategori_koleksi']),
                'kategori_code' => $row['kategori_koleksi'],
                'pencipta'      => $row['seniman_pencipta'],
                'tahun_num'     => (int)$row['tahun_perolehan'],
                'asal'          => $row['asal_daerah'],
                'ruang3D'       => $row['lokasi_ruang'],
            ];
        }
    }
}

$edit_id  = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$edit_row = $edit_id > 0 ? get_koleksi_row_by_id($edit_id) : null;

$kurator_nama  = $_SESSION['kurator_nama'] ?? 'Mohammad Yanuar Rizky';
$kurator_user  = $_SESSION['kurator_user'] ?? 'kurator.yanel';
$kurator_peran = $_SESSION['kurator_peran'] ?? 'Kurator Koleksi & Preservasi';

$page_title       = "Kelola Koleksi (Ubah & Hapus) — Nusantara Gallery Art";
$page_description = "Panel kurator untuk mengubah metadata dan menghapus koleksi warisan budaya Nusantara.";
$current_page     = "kelola";

include "layout/header.php";
?>

    <div class="curator-session-bar" data-curator-session-bar>
      <div class="page-width session-bar-inner">
        <div class="session-info">
          <span class="session-indicator-dot"></span>
          <span class="session-label">Sesi Kurator Aktif:</span>
          <strong class="session-user"><?= htmlspecialchars($kurator_nama) ?> (<?= htmlspecialchars($kurator_user) ?>)</strong>
          <span class="session-badge">Hak Akses: <?= htmlspecialchars($kurator_peran) ?></span>
        </div>
        <div class="session-actions">
          <a href="formulir.php" class="btn-session-exit">+ Tambah Koleksi Baru</a>
          <a href="login.php?action=logout" class="btn-session-exit">Keluar Sesi</a>
        </div>
      </div>
    </div>

    <main id="main-content" class="page-width form-page-container">
      <header class="page-title editorial-header">
        <h2>Kelola Koleksi: Ubah &amp; Hapus Metadata</h2>
        <p>Perbarui rincian kuratorial atau hapus entri koleksi dari basis data Nusantara Gallery Art.</p>
      </header>

      <?php if ($hasil_aksi !== null): ?>
        <div class="form-status-banner <?= $hasil_aksi['ok'] ? 'is-success' : 'is-error' ?>" role="alert">
          <strong><?= $hasil_aksi['ok'] ? 'Berhasil:' : 'Perhatian:' ?></strong>
          <?= htmlspecialchars($hasil_aksi['msg']) ?>
        </div>
      <?php endif; ?>

      <?php if ($edit_row !== null): ?>
      <section class="form-wrapper-card" id="form-edit" aria-labelledby="edit-heading" style="margin-bottom: 2.5rem;">
        <header class="form-card-header">
          <div class="archive-stamp">
            <span class="stamp-code">ID #<?= (int)$edit_row['id_koleksi'] ?></span>
            <span class="stamp-label">Mode Sunting Metadata</span>
          </div>
          <h3 id="edit-heading">Ubah Koleksi: <?= htmlspecialchars($edit_row['judul_koleksi']) ?></h3>
        </header>

        <form action="kelola.php" method="POST" class="glam-entry-form">
          <input type="hidden" name="action" value="update" />
          <input type="hidden" name="id_koleksi" value="<?= (int)$edit_row['id_koleksi'] ?>" />

          <fieldset class="form-fieldset">
            <legend class="fieldset-legend">Rincian Metadata Koleksi</legend>

            <div class="form-row-grid">
              <div class="form-group">
                <label for="judul_koleksi">Judul Koleksi <span class="required-indicator">*</span></label>
                <input type="text" id="judul_koleksi" name="judul_koleksi" class="form-control" required
                  value="<?= htmlspecialchars($edit_row['judul_koleksi']) ?>" />
              </div>
              <div class="form-group">
                <label for="nomor_inventaris">Nomor Inventaris (NG-YYYY-XXX) <span class="required-indicator">*</span></label>
                <input type="text" id="nomor_inventaris" name="nomor_inventaris" class="form-control" required
                  pattern="NG-[0-9]{4}-[0-9]{3}" value="<?= htmlspecialchars($edit_row['nomor_inventaris']) ?>" />
              </div>
            </div>

            <div class="form-row-grid">
              <div class="form-group">
                <label for="kategori_koleksi">Kategori Koleksi <span class="required-indicator">*</span></label>
                <select id="kategori_koleksi" name="kategori_koleksi" class="form-control" required>
                  <?php foreach (['lukisan' => 'Lukisan', 'naskah' => 'Naskah', 'artefak' => 'Artefak', 'wastra' => 'Wastra'] as $k => $lbl): ?>
                    <option value="<?= $k ?>" <?= ($edit_row['kategori_koleksi'] === $k) ? 'selected' : '' ?>><?= $lbl ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group">
                <label for="tahun_perolehan">Tahun Penciptaan / Perolehan</label>
                <input type="number" id="tahun_perolehan" name="tahun_perolehan" class="form-control"
                  value="<?= (int)$edit_row['tahun_perolehan'] ?>" />
              </div>
            </div>

            <div class="form-row-grid">
              <div class="form-group">
                <label for="seniman_pencipta">Seniman / Pencipta <span class="required-indicator">*</span></label>
                <input type="text" id="seniman_pencipta" name="seniman_pencipta" class="form-control" required
                  value="<?= htmlspecialchars($edit_row['seniman_pencipta']) ?>" />
              </div>
              <div class="form-group">
                <label for="asal_daerah">Asal Daerah</label>
                <input type="text" id="asal_daerah" name="asal_daerah" class="form-control"
                  value="<?= htmlspecialchars($edit_row['asal_daerah'] ?? '') ?>" />
              </div>
            </div>

            <div class="form-row-grid">
              <div class="form-group">
                <label for="medium_teknik">Medium &amp; Teknik</label>
                <input type="text" id="medium_teknik" name="medium_teknik" class="form-control"
                  value="<?= htmlspecialchars($edit_row['medium_teknik'] ?? '') ?>" />
              </div>
              <div class="form-group">
                <label for="dimensi">Dimensi Fisik</label>
                <input type="text" id="dimensi" name="dimensi" class="form-control"
                  value="<?= htmlspecialchars($edit_row['dimensi'] ?? '') ?>" />
              </div>
            </div>

            <div class="form-group">
              <label for="lokasi_ruang">Lokasi Ruang Pamer</label>
              <input type="text" id="lokasi_ruang" name="lokasi_ruang" class="form-control"
                value="<?= htmlspecialchars($edit_row['lokasi_ruang'] ?? '') ?>" />
            </div>

            <div class="form-group">
              <label for="deskripsi">Deskripsi / Catatan Kuratorial</label>
              <textarea id="deskripsi" name="deskripsi" rows="3" class="form-control"><?= htmlspecialchars($edit_row['deskripsi'] ?? '') ?></textarea>
            </div>
          </fieldset>

          <div class="form-actions-bar">
            <a href="kelola.php" class="button button-secondary">Batal</a>
            <button type="submit" class="button">Simpan Perubahan</button>
          </div>
        </form>
      </section>
      <?php endif; ?>

      <section class="featured" aria-labelledby="daftar-koleksi-heading">
        <header class="section-heading section-heading-row">
          <div>
            <h3 id="daftar-koleksi-heading">Daftar Koleksi Terdaftar (<?= count($KOLEKSI_DATA) ?> Koleksi)</h3>
          </div>
          <a href="formulir.php" class="button">+ Tambah Koleksi</a>
        </header>

        <table class="metadata-table">
          <thead>
            <tr>
              <th scope="col">Inventaris</th>
              <th scope="col">Judul Koleksi</th>
              <th scope="col">Kategori</th>
              <th scope="col">Pencipta &amp; Asal</th>
              <th scope="col">Tahun</th>
              <th scope="col">Aksi Kurator</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $idx = 1;
            foreach ($KOLEKSI_DATA as $slug => $item):
                $id_kol = (int)($item['id_koleksi'] ?? $idx);
                $idx++;
            ?>
            <tr>
              <td><code><?= htmlspecialchars($item['inventaris'] ?? '-') ?></code></td>
              <td>
                <strong><a href="detail.php?id=<?= urlencode($slug) ?>" class="text-link"><?= htmlspecialchars($item['judul']) ?></a></strong>
              </td>
              <td><?= htmlspecialchars($item['kategori']) ?></td>
              <td>
                <?= htmlspecialchars($item['pencipta']) ?><br />
                <small><?= htmlspecialchars($item['asal'] ?? '-') ?></small>
              </td>
              <td><?= (int)($item['tahun_num'] ?? 0) ?: '-' ?></td>
              <td>
                <div style="display:flex; gap:0.5rem; align-items:center; flex-wrap:wrap;">
                  <a href="kelola.php?edit=<?= $id_kol ?>#form-edit" class="button button-secondary" style="padding:0.35rem 0.75rem; font-size:0.85rem;">Ubah</a>
                  <form action="kelola.php" method="POST" style="display:inline; margin:0;"
                        onsubmit="return confirm('Hapus koleksi <?= htmlspecialchars(addslashes($item['judul'])) ?>?');">
                    <input type="hidden" name="action" value="delete" />
                    <input type="hidden" name="id_koleksi" value="<?= $id_kol ?>" />
                    <button type="submit" class="button" style="padding:0.35rem 0.75rem; font-size:0.85rem; background:var(--invalid-border, #a33); border-color:var(--invalid-border, #a33); color:#fff;">Hapus</button>
                  </form>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </section>
    </main>

<?php
include "layout/footer.php";
?>
