<?php
include 'config.php';

try {
    // Ambil data top borrower dari database
    $sql = "SELECT nama, total_peminjaman FROM peminjam ORDER BY total_peminjaman DESC LIMIT 5";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $topBorrowers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ZyeLends - Top Borrower</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            pastel: {
              pink: '#FFC0CB',
              blue: '#ADD8E6',
              green: '#90EE90',
              yellow: '#FFFF99',
              purple: '#E6E6FA',
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
      background-color: #FFFFFF;
      color: #333333;
    }
    .header {
      background-color: #FFDAB9;
      color: #333333;
    }
    .header-title {
      font-size: 1.25rem;
      font-weight: 600;
    }
    .header-subtitle {
      font-size: 0.75rem;
    }
    .header-nav a {
      background-color: #FFC48C;
      color: #333333;
      border: 1px solid #FFC48C;
      transition: all 0.2s ease;
    }
    .header-nav a:hover {
      background-color: #FFA54F;
      border-color: #FFA54F;
    }
    .btn-inverse {
      background-color: #FFC48C;
      color: #333333;
      border: 1px solid #FFC48C;
      transition: all 0.3s ease;
    }
    .btn-inverse:hover {
      background-color: #FFA54F;
      color: #333333;
      border-color: #FFA54F;
    }
    .btn-primary-pastel {
      background-color: #FFC48C;
      color: #333333;
      border: 1px solid #FFC48C;
    }
    .modal-header-pastel {
      background-color: #FFDAB9;
      color: #333333;
      border-bottom: 1px solid #FFDAB9;
    }
    .box-shadow {
      background-color: #FFFFFF;
      border: 1px solid #FFDAB9;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    table.min-w-full {
      border: 1px solid #FFDAB9;
    }
    table.min-w-full th {
      background-color: #FFDAB9;
      color: #333333;
      border: 1px solid #FFDAB9;
      padding: 8px;
    }
    table.min-w-full td {
      border: 1px solid #FFDAB9;
      padding: 8px;
    }
    .status-dipinjam,
    .status-dikembalikan,
    .status-terlambat {
      background-color: #FFDAB9;
      color: #333333;
      padding: 2px 8px;
      border-radius: 4px;
      font-size: 12px;
      border: 1px solid #FFDAB9;
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
          <h2 class="text-xl font-bold mb-4">Top 5 Peminjam</h2>
          <div class="fixed-header-table" style="max-height: calc(100vh - 200px);">
            <table class="min-w-full border-collapse">
              <thead>
                <tr>
                  <th class="px-2 py-2">No</th>
                  <th class="px-2 py-2">Nama Peminjam</th>
                  <th class="px-2 py-2">Total Peminjaman</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($topBorrowers as $index => $borrower): ?>
                <tr>
                  <td class="px-2 py-2"><?= $index + 1 ?></td>
                  <td class="px-2 py-2"><?= htmlspecialchars($borrower['nama']) ?></td>
                  <td class="px-2 py-2"><?= $borrower['total_peminjaman'] ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($topBorrowers)): ?>
                <tr>
                  <td colspan="3" class="text-center py-4">Tidak ada data peminjam</td>
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