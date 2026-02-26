<?php
session_start();
include 'config.php'; // PDO Connection

// Get items for borrowing
$sql = "SELECT id, nama_barang, stok FROM barang_list ORDER BY nama_barang";
$stmt = $conn->prepare($sql);
$stmt->execute();
$barang_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get admin list
$sql = "SELECT id, nama_admin FROM admin_list ORDER BY nama_admin";
$stmt = $conn->prepare($sql);
$stmt->execute();
$admin_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get borrower names for autocomplete (changed to use santri table like index.php)
$sql = "SELECT nama FROM santri ORDER BY nama";
$stmt = $conn->prepare($sql);
$stmt->execute();
$borrowers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ZyeLends - Sistem Peminjaman Barang</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            pastel: {
              orange: '#FFDAB9',
            },
          },
          fontFamily: {
            sans: ['Plus Jakarta Sans', 'sans-serif'],
          }
        }
      }
    }
  </script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    html, body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      background-color: #FFFFFF; /* white */
      color: #333333; /* dark grey */
    }
    /* Header and navigation */
    .header {
      background-color: #FFDAB9; /* pastel orange */
      color: #333333; /* dark grey */
    }
    .header-title {
      font-size: 1.25rem;
      font-weight: 600;
    }
    .header-subtitle {
      font-size: 0.75rem;
    }
    .header-nav a {
      background-color: #FFC48C; /* pastel orange lebih gelap */
      color: #333333; /* dark grey */
      border: 1px solid #FFC48C;
      transition: all 0.2s ease;
    }
    .header-nav a:hover {
      background-color: #FFA54F;
      border-color: #FFA54F;
    }
    /* Default buttons (except PINJAM): light orange background, dark text */
    .btn-inverse {
      background-color: #FFC48C; /* pastel orange lebih gelap */
      color: #333333; /* dark grey */
      border: 1px solid #FFC48C; /* pastel orange */
      transition: all 0.3s ease;
    }
    .btn-inverse:hover {
      background-color: #FFA54F;
      color: #333333; /* dark grey */
      border-color: #FFA54F;
    }
    /* PINJAM button: orange background, white text */
    .btn-pinjam {
      background-color: #FFC48C; /* pastel orange lebih gelap */
      color: #333333; /* dark grey */
      border: 1px solid #FFC48C;
    }
    .btn-pinjam:hover {
      background-color: #FFA54F;
      color: #333333;
      border-color: #FFA54F;
    }
    /* Modal header orange color, white text */
    .modal-header-pastel {
      background-color: #FFDAB9; /* pastel orange */
      color: #333333; /* dark grey */
      border-bottom: 1px solid #FFDAB9;
    }
    .modal-header-pastel:hover {
      background-color: #FFDAB9;
      color: #333333;
    }
    /* Box styling for forms and tables */
    .box-shadow {
      background-color: #FFFFFF;
      border: 1px solid #FFDAB9; /* pastel orange */
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    /* Table */
    table.min-w-full {
      border: 1px solid #FFDAB9; /* pastel orange */
    }
    table.min-w-full th {
      background-color: #FFDAB9; /* pastel orange */
      color: #333333; /* dark grey */
      border: 1px solid #FFDAB9; /* pastel orange */
      padding: 8px;
    }
    table.min-w-full td {
      border: 1px solid #FFDAB9; /* pastel orange */
      padding: 8px;
    }
    /* Fixed header table */
    .fixed-header-table {
      height: 600px;
      overflow-y: auto;
    }
    .fixed-header-table thead th {
      position: sticky;
      top: 0;
      z-index: 1;
      background-color: #FFDAB9; /* pastel orange */
    }
    /* Status badges */
    .status-dipinjam,
    .status-dikembalikan,
    .status-terlambat {
      background-color: #FFDAB9; /* pastel orange */
      color: #333333; /* dark grey */
      padding: 2px 8px;
      border-radius: 4px;
      font-size: 12px;
      border: 1px solid #FFDAB9; /* pastel orange */
    }
  </style>
</head>
<body>
  <?php if (isset($_SESSION['danger'])): ?>
    <div class="toast-container position-fixed top-0 end-0 p-3">
      <div class="toast show bg-danger text-white" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-danger text-white">
          <strong class="me-auto">Error</strong>
          <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
          <?= htmlspecialchars($_SESSION['danger']) ?>
        </div>
      </div>
    </div>
    <?php unset($_SESSION['danger']); ?>
  <?php endif; ?>

  <?php if (isset($_SESSION['message'])): ?>
    <div class="toast-container position-fixed top-0 end-0 p-3">
      <div class="toast show bg-success text-white" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-success text-white">
          <strong class="me-auto"><?= htmlspecialchars($_SESSION['message']['type']) ?></strong>
          <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
          <?= htmlspecialchars($_SESSION['message']['text']) ?>
        </div>
      </div>
    </div>
    <?php unset($_SESSION['message']); ?>
  <?php endif; ?>

  <header class="header px-4 py-2 flex justify-between items-center">
    <div style="display: flex; flex-direction: column; align-items: flex-start; gap: 5px;">
      <h1 style="font-size: 2rem; font-weight: bold;">ZYELENDS</h1>
      <p style="font-size: 0.5rem;">V1.0 - SISTEM PEMINJAMAN BARANG</p>
    </div>
    <nav class="header-nav space-x-2">
      <a href="admin.php" class="px-3 py-1 rounded">ADMIN</a>
      <a href="top_borrow.php" class="px-3 py-1 rounded">TOP BORROWER</a>
      <a href="keterlambatan.php" class="px-3 py-1 rounded">CEK KETERLAMBATAN</a>
      <a href="laporan.php" class="px-3 py-1 rounded">LAPORAN</a>
    </nav>
  </header>

  <main class="container mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row gap-6">
      <div class="mt-4 w-full md:w-1/4">
        <div class="box-shadow rounded p-4 custom-height">
          <form id="pinjamForm" method="POST" action="proses_pinjam.php">
            <div class="mb-4">
              <label class="block mb-1">Nama Peminjam</label>
              <input type="text" list="namaList" id="nama" name="nama" class="w-full p-2 border rounded" placeholder="Ketik nama..." required>
              <datalist id="namaList">
                <?php
                  foreach ($borrowers as $row) {
                    echo '<option value="' . htmlspecialchars($row['nama']) . '"></option>';
                  }
                ?>
              </datalist>
            </div>
            <div class="mb-4">
              <label class="block mb-1">Kelas</label>
              <input type="text" id="kelas" name="kelas" class="w-full p-2 border rounded" readonly />
            </div>
            <div class="mb-4">
              <label class="block mb-1">Jenis Barang</label>
              <select class="w-full p-2 border rounded" name="jenis_barang" id="jenis_barang" required>
                <option value="">Pilih Jenis Barang</option>
                <?php foreach ($barang_list as $barang): ?>
                  <option value="<?= htmlspecialchars($barang['nama_barang']) ?>">
                    <?= htmlspecialchars($barang['nama_barang']) ?> 
                    (Stok: <?= $barang['stok'] ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="mb-4">
              <label class="block mb-1">Tipe Durasi</label>
              <select class="w-full p-2 border rounded" name="tipe_durasi" id="tipe_durasi" required onchange="toggleDurasiFields()">
                <option value="">Pilih Tipe Durasi</option>
                <option value="jam">Per Jam</option>
                <option value="hari">Per Hari</option>
              </select>
            </div>

            <!-- Durasi Per Jam Fields (hidden by default) -->
            <div id="durasiJamFields" style="display: none;">
              <div class="mb-4">
                <label class="block mb-1">Waktu Mulai</label>
                <input type="datetime-local" id="waktu_mulai_jam" name="waktu_mulai_jam" class="w-full p-2 border rounded">
              </div>
              <div class="mb-4">
                <label class="block mb-1">Durasi (Jam)</label>
                <input type="number" id="durasi_jam" name="durasi_jam" class="w-full p-2 border rounded" min="1" value="1">
              </div>
            </div>

            <!-- Durasi Per Hari Fields (hidden by default) -->
            <div id="durasiHariFields" style="display: none;">
              <div class="mb-4">
                <label class="block mb-1">Tanggal Mulai</label>
                <input type="date" id="tanggal_mulai" name="tanggal_mulai" class="w-full p-2 border rounded">
              </div>
              <div class="mb-4">
                <label class="block mb-1">Durasi (Hari)</label>
                <input type="number" id="durasi_hari" name="durasi_hari" class="w-full p-2 border rounded" min="1" value="1">
              </div>
            </div>

            <div class="mb-4">
              <label class="block mb-1">Admin</label>
              <select class="w-full p-2 border rounded" name="admin" id="admin" required>
                <option value="">Pilih Admin</option>
                <?php foreach ($admin_list as $admin): ?>
                  <option value="<?= htmlspecialchars($admin['nama_admin']) ?>">
                    <?= htmlspecialchars($admin['nama_admin']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            
            <button type="button" id="submitBtn" class="w-full py-2 rounded btn-pinjam" onclick="konfirmasiPinjam()" disabled>
              PINJAM
            </button>
            <p class="text-center text-xs mt-4 text-gray-500">&copy; DEVELOPER BY MARSZYE</p>
          </form>
        </div>
      </div>

      <div class="mt-4 w-full md:w-3/4">
        <div class="box-shadow rounded p-4 overflow-hidden" style="height: 100%;">
          <div class="fixed-header-table" style="max-height: calc(100vh - 200px);">
            <table class="min-w-full border-collapse">
              <thead>
                <tr>
                  <th class="px-2 py-2 text-left">No</th>
                  <th class="px-2 py-2 text-left">Nama</th>
                  <th class="px-2 py-2 text-left">Kelas</th>
                  <th class="px-2 py-2 text-left">Jenis Barang</th>
                  <th class="px-2 py-2 text-left">Durasi</th>
                  <th class="px-2 py-2 text-left">Status</th>
                  <th class="px-2 py-2 text-left">Aksi</th>
                  <th class="px-2 py-2 text-left">Keterlambatan (menit)</th>
                </tr>
              </thead>
              <tbody>
                <?php include 'history.php'; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Modal Konfirmasi Peminjaman -->
  <div class="modal fade" id="konfirmasiModal" tabindex="-1" aria-labelledby="konfirmasiModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header modal-header-pastel">
          <h5 class="modal-title" id="konfirmasiModalLabel">Konfirmasi Peminjaman</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p id="konfirmasiPesan"></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-primary" id="konfirmasiPinjamBtn" style="background-color: #FFC48C; border-color: #FFC48C;">Konfirmasi</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Kembalikan Barang -->
  <div class="modal fade" id="kembalikanModal" tabindex="-1" aria-labelledby="kembalikanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header modal-header-pastel">
          <h5 class="modal-title" id="kembalikanModalLabel">Konfirmasi Pengembalian</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p id="kembalikanPesan">Apakah Anda yakin ingin mengembalikan barang ini?</p>
          <input type="hidden" id="peminjaman_id" value="">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-primary" id="kembalikanBtn" style="background-color: #FFC48C; border-color: #FFC48C;">Konfirmasi</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

  <script>
    // Initialize toasts
    document.addEventListener('DOMContentLoaded', function() {
      var toastElList = [].slice.call(document.querySelectorAll('.toast'));
      var toastList = toastElList.map(function(toastEl) {
        return new bootstrap.Toast(toastEl, { autohide: true, delay: 5000 });
      });

      // Activate all toasts
      toastList.forEach(toast => toast.show());
      
      // Set default date/time values
      setDefaultDateTimeValues();
      
      // Check form validity for button state
      checkFormValidity();
    });

    // Set default date/time values (today/now)
    function setDefaultDateTimeValues() {
      const now = new Date();
      
      // Format for datetime-local: YYYY-MM-DDThh:mm
      const year = now.getFullYear();
      const month = String(now.getMonth() + 1).padStart(2, '0');
      const day = String(now.getDate()).padStart(2, '0');
      const hours = String(now.getHours()).padStart(2, '0');
      const minutes = String(now.getMinutes()).padStart(2, '0');
      
      const dateTimeValue = `${year}-${month}-${day}T${hours}:${minutes}`;
      const dateValue = `${year}-${month}-${day}`;
      
      document.getElementById('waktu_mulai_jam').value = dateTimeValue;
      document.getElementById('tanggal_mulai').value = dateValue;
    }

    // Toggle duration fields based on selected duration type
    function toggleDurasiFields() {
      const tipeDurasi = document.getElementById('tipe_durasi').value;
      const durasiJamFields = document.getElementById('durasiJamFields');
      const durasiHariFields = document.getElementById('durasiHariFields');
      
      if (tipeDurasi === 'jam') {
        durasiJamFields.style.display = 'block';
        durasiHariFields.style.display = 'none';
        document.getElementById('waktu_mulai_jam').required = true;
        document.getElementById('durasi_jam').required = true;
        document.getElementById('tanggal_mulai').required = false;
        document.getElementById('durasi_hari').required = false;
      } else if (tipeDurasi === 'hari') {
        durasiJamFields.style.display = 'none';
        durasiHariFields.style.display = 'block';
        document.getElementById('waktu_mulai_jam').required = false;
        document.getElementById('durasi_jam').required = false;
        document.getElementById('tanggal_mulai').required = true;
        document.getElementById('durasi_hari').required = true;
      } else {
        durasiJamFields.style.display = 'none';
        durasiHariFields.style.display = 'none';
      }
      
      checkFormValidity();
    }

    // AJAX untuk mendapatkan kelas santri berdasarkan nama (seperti di index.php)
    document.getElementById('nama').addEventListener('input', function() {
      const nama = this.value;
      const kelasInput = document.getElementById('kelas');

      if (nama.trim() !== '') {
        $.ajax({
          url: 'get_kelas.php',
          type: 'POST',
          data: { nama: nama },
          success: function(response) {
            kelasInput.value = response;
            checkFormValidity(); // Check form validity setiap kali nama berubah
          },
          error: function() {
            console.error('AJAX call to get_kelas.php failed.');
            kelasInput.value = '';
            checkFormValidity(); // Check form validity (akan menonaktifkan tombol jika perlu)
          }
        });
      } else {
        kelasInput.value = '';
        checkFormValidity(); // Check form validity (akan menonaktifkan tombol)
      }
    });

    // Check if all required fields are filled
    function checkFormValidity() {
      const form = document.getElementById('pinjamForm');
      const submitBtn = document.getElementById('submitBtn');
      const tipeDurasi = document.getElementById('tipe_durasi').value;
      const namaInput = document.getElementById('nama');
      
      let isValid = form.checkValidity() && namaInput.value.trim() !== '';
      
      // Additional check for duration-specific fields
      if (tipeDurasi === 'jam') {
        isValid = isValid && document.getElementById('waktu_mulai_jam').value && 
                 document.getElementById('durasi_jam').value > 0;
      } else if (tipeDurasi === 'hari') {
        isValid = isValid && document.getElementById('tanggal_mulai').value && 
                 document.getElementById('durasi_hari').value > 0;
      } else {
        isValid = false; // Need to select a duration type
      }
      
      submitBtn.disabled = !isValid;
    }

    // Input event listeners for all form fields
    document.getElementById('jenis_barang').addEventListener('change', checkFormValidity);
    document.getElementById('tipe_durasi').addEventListener('change', checkFormValidity);
    document.getElementById('waktu_mulai_jam').addEventListener('input', checkFormValidity);
    document.getElementById('durasi_jam').addEventListener('input', checkFormValidity);
    document.getElementById('tanggal_mulai').addEventListener('input', checkFormValidity);
    document.getElementById('durasi_hari').addEventListener('input', checkFormValidity);
    document.getElementById('admin').addEventListener('change', checkFormValidity);
    
    // Confirm borrowing function
    function konfirmasiPinjam() {
      const form = document.getElementById('pinjamForm');
      
      if (form.checkValidity()) {
        const nama = document.getElementById('nama').value;
        const kelas = document.getElementById('kelas').value;
        const jenisBarang = document.getElementById('jenis_barang').options[document.getElementById('jenis_barang').selectedIndex].text;
        const tipeDurasi = document.getElementById('tipe_durasi').value;
        
        let durasiText = '';
        if (tipeDurasi === 'jam') {
          const durasi = document.getElementById('durasi_jam').value;
          durasiText = `${durasi} jam`;
        } else {
          const durasi = document.getElementById('durasi_hari').value;
          durasiText = `${durasi} hari`;
        }
        
        // Create confirmation message
        const pesan = `Konfirmasi peminjaman barang oleh ${nama} (${kelas}):\n\nBarang: ${jenisBarang}\nDurasi: ${durasiText}\n\nApakah data sudah benar?`;
        document.getElementById('konfirmasiPesan').textContent = pesan;
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('konfirmasiModal'));
        modal.show();
      } else {
        // Show browser's default validation
        form.reportValidity();
      }
    }
    
    // Event listener for confirmation button in modal
    document.getElementById('konfirmasiPinjamBtn').addEventListener('click', function() {
      document.getElementById('pinjamForm').submit(); // Submit form when confirmed
    });
    
    // Function to show return confirmation modal
    function kembalikanBarang(id) {
      document.getElementById('peminjaman_id').value = id;
      const modal = new bootstrap.Modal(document.getElementById('kembalikanModal'));
      modal.show();
    }
    
    // Event listener for return confirmation button
    document.getElementById('kembalikanBtn').addEventListener('click', function() {
      const id = document.getElementById('peminjaman_id').value;
      
      // Submit return request via AJAX
      $.ajax({
        url: 'proses_kembalikan.php',
        type: 'POST',
        data: { id: id },
        success: function(response) {
          try {
            const result = JSON.parse(response);
            
            if (result.success) {
              // Close modal
              bootstrap.Modal.getInstance(document.getElementById('kembalikanModal')).hide();
              
              // Show success message
              alert(result.message);
              
              // Reload page to refresh history
              location.reload();
            } else {
              alert('Error: ' + result.message);
            }
          } catch (e) {
            alert('Error processing response');
          }
        },
        error: function() {
          alert('Error processing request');
        }
      });
    });
  </script>
</body>
</html>