<?php
include 'config.php';

try {
    // Ambil data keterlambatan dari database
    $sql = "SELECT p.nama, p.jenis_barang, p.tipe_durasi, p.waktu_mulai, p.durasi, 
            TIMESTAMPDIFF(MINUTE, NOW(), CASE 
                WHEN p.tipe_durasi = 'jam' THEN DATE_ADD(p.waktu_mulai, INTERVAL p.durasi HOUR)
                WHEN p.tipe_durasi = 'hari' THEN DATE_ADD(p.waktu_mulai, INTERVAL p.durasi DAY)
                END) AS keterlambatan
            FROM peminjaman p 
            WHERE p.status = 'Dipinjam' 
            AND (NOW() > CASE 
                WHEN p.tipe_durasi = 'jam' THEN DATE_ADD(p.waktu_mulai, INTERVAL p.durasi HOUR)
                WHEN p.tipe_durasi = 'hari' THEN DATE_ADD(p.waktu_mulai, INTERVAL p.durasi DAY)
                END)";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $lateBorrowings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ZyeLends - Keterlambatan</title>
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
      background-color: #FFC48C; /* pastel orange */
      color: #333333; /* dark grey */
      border: 1px solid #FFC48C;
      transition: all 0.2s ease;
    }
    .header-nav a:hover {
      background-color: #FFA54F; /* darker orange */
      border-color: #FFA54F;
    }
    /* Buttons: light orange background, dark text */
    .btn-inverse {
      background-color: #FFC48C; /* pastel orange */
      color: #333333; /* dark grey */
      border: 1px solid #FFC48C; /* darker orange */
      transition: all 0.3s ease;
    }
    .btn-inverse:hover {
      background-color: #FFA54F; /* darker orange */
      color: #333333; /* dark grey */
      border-color: #FFA54F;
    }
    /* Action button: orange background, white text */
    .btn-primary-pastel {
      background-color: #FFC48C; /* pastel orange */
      color: #333333; /* dark grey */
      border: 1px solid #FFC48C;
    }
    /* Modal header orange color, white text */
    .modal-header-pastel {
      background-color: #FFDAB9; /* pastel orange */
      color: #333333; /* dark grey */
      border-bottom: 1px solid #FFDAB9;
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
  <header class="header px-4 py-2 flex justify-between items-center">
    <div style="display: flex; flex-direction: column; align-items: flex-start; gap: 5px;">
      <h1 style="font-size: 2rem; font-weight: bold;">ZYELENDS</h1>
      <p style="font-size: 0.5rem;">V1.0 - SISTEM PEMINJAMAN BARANG</p>
    </div>
    <nav class="header-nav space-x-2">
      <a href="index.php" class="px-3 py-1 rounded">KEMBALI KE BERANDA</a>
    </nav>
  </header>

  <main class="container mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row gap-6">
      <div class="mt-4 w-full">
        <div class="box-shadow rounded p-4">
          <h2 class="text-xl font-bold mb-4">Daftar Keterlambatan</h2>
          <div class="fixed-header-table" style="max-height: calc(100vh - 200px);">
            <table class="min-w-full border-collapse">
              <thead>
                <tr>
                  <th class="px-2 py-2">Nama</th>
                  <th class="px-2 py-2">Jenis Barang</th>
                  <th class="px-2 py-2">Tipe Durasi</th>
                  <th class="px-2 py-2">Waktu Mulai</th>
                  <th class="px-2 py-2">Durasi</th>
                  <th class="px-2 py-2">Keterlambatan (menit)</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($lateBorrowings as $row): ?>
                <tr>
                  <td class="px-2 py-2"><?= htmlspecialchars($row['nama']) ?></td>
                  <td class="px-2 py-2"><?= htmlspecialchars($row['jenis_barang']) ?></td>
                  <td class="px-2 py-2"><?= htmlspecialchars($row['tipe_durasi']) ?></td>
                  <td class="px-2 py-2"><?= htmlspecialchars($row['waktu_mulai']) ?></td>
                  <td class="px-2 py-2"><?= htmlspecialchars($row['durasi']) ?></td>
                  <td class="px-2 py-2"><?= abs((int)$row['keterlambatan']) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($lateBorrowings)): ?>
                <tr>
                  <td colspan="6" class="text-center py-4">Tidak ada keterlambatan</td>
                </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>