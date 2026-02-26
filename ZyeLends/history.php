<?php
if (!defined('INCLUDED_FROM_INDEX')) {
    include_once 'config.php';
}

// Tambahkan style khusus untuk status dan border tabel
echo '<style>
.status-dipinjam {
  background-color: #FFDAB9;
  color: #333333;
  padding: 2px 8px;
  border-radius: 4px;
  font-size: 12px;
  border: 1px solid #FFDAB9;
}
.status-dikembalikan {
  background-color: #FFDAB9;
  color: #333333;
  padding: 2px 8px;
  border-radius: 4px;
  font-size: 12px;
  border: 1px solid #FFDAB9;
}
.status-terlambat {
  background-color: #FFA54F;
  color: #fff;
  padding: 2px 8px;
  border-radius: 4px;
  font-size: 12px;
  border: 1px solid #FFDAB9;
}
table.min-w-full, table.min-w-full th, table.min-w-full td {
  border: 1px solid #FFDAB9 !important;
}
table.min-w-full th {
  background-color: #FFDAB9 !important;
  color: #333333 !important;
}
</style>';

try {
    // First check if santri table exists
    $checkTable = "SHOW TABLES LIKE 'santri'";
    $checkStmt = $conn->prepare($checkTable);
    $checkStmt->execute();
    $santriExists = $checkStmt->rowCount() > 0;
    
    // Query to get latest borrowing history
    if ($santriExists) {
        // If santri table exists, join with it to get kelas
        $sql = "SELECT p.id, p.nama, s.kelas, p.jenis_barang, 
                p.tipe_durasi, p.waktu_mulai, p.durasi, p.status, p.admin,
                p.waktu_peminjaman, p.waktu_pengembalian
                FROM peminjaman p
                LEFT JOIN santri s ON p.nama = s.nama
                ORDER BY p.waktu_peminjaman DESC";
    } else {
        // If santri table doesn't exist, get data without kelas
        $sql = "SELECT p.id, p.nama, '' as kelas, p.jenis_barang, 
                p.tipe_durasi, p.waktu_mulai, p.durasi, p.status, p.admin,
                p.waktu_peminjaman, p.waktu_pengembalian
                FROM peminjaman p
                ORDER BY p.waktu_peminjaman DESC";
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $counter = 1;

    foreach ($result as $row) {
        // Format duration based on type
        $durasiDisplay = '';
        if ($row['tipe_durasi'] == 'jam') {
            $durasiDisplay = $row['durasi'] . ' jam';
            // Also show start and end datetime
            $waktuMulai = new DateTime($row['waktu_mulai']);
            $waktuSelesai = clone $waktuMulai;
            $waktuSelesai->modify('+' . $row['durasi'] . ' hours');
            
            $durasiDisplay .= '<br><small>' . 
                              $waktuMulai->format("d/m/Y H:i") . ' - ' . 
                              $waktuSelesai->format("d/m/Y H:i") . '</small>';
        } else { // per day
            $durasiDisplay = $row['durasi'] . ' hari';
            // Also show start and end date
            $tanggalMulai = new DateTime($row['waktu_mulai']);
            $tanggalSelesai = clone $tanggalMulai;
            $tanggalSelesai->modify('+' . $row['durasi'] . ' days');
            
            $durasiDisplay .= '<br><small>' . 
                              $tanggalMulai->format("d/m/Y") . ' - ' . 
                              $tanggalSelesai->format("d/m/Y") . '</small>';
        }
        
        // Check if item is overdue
        $statusClass = '';
        $statusText = $row['status'];
        $lateMinutes = 0;
        
        if ($statusText == 'Dipinjam') {
            $statusClass = 'status-dipinjam';
            
            // Check if currently overdue
            $now = new DateTime();
            $batasPengembalian = null;
            
            if ($row['tipe_durasi'] == 'jam') {
                $waktuMulai = new DateTime($row['waktu_mulai']);
                $batasPengembalian = clone $waktuMulai;
                $batasPengembalian->modify('+' . $row['durasi'] . ' hours');
            } else {
                $tanggalMulai = new DateTime($row['waktu_mulai']);
                $batasPengembalian = clone $tanggalMulai;
                $batasPengembalian->modify('+' . $row['durasi'] . ' days');
            }
            
            if ($now > $batasPengembalian) {
                $statusText = 'Terlambat';
                $statusClass = 'status-terlambat';
                $lateMinutes = $now->getTimestamp() - $batasPengembalian->getTimestamp();
                $lateMinutes = floor($lateMinutes / 60); // Convert to minutes
            }
        } else if ($statusText == 'Dikembalikan') {
            $statusClass = 'status-dikembalikan';
        }
        
        // Action button based on status
        $actionBtn = '';
        if ($statusText == 'Dipinjam' || $statusText == 'Terlambat') {
            $actionBtn = '<button onclick="kembalikanBarang(' . $row['id'] . ')" class="btn btn-sm btn-inverse">Kembalikan</button>';
        } else {
            $actionBtn = '<span class="text-muted">-</span>';
        }
        
        echo '<tr>
                <td class="px-2 py-2">' . $counter++ . '</td>
                <td class="px-2 py-2">' . htmlspecialchars($row['nama'] ?? '') . '</td>
                <td class="px-2 py-2">' . htmlspecialchars($row['kelas'] ?? '-') . '</td>
                <td class="px-2 py-2">' . htmlspecialchars($row['jenis_barang'] ?? '') . '</td>
                <td class="px-2 py-2">' . $durasiDisplay . '</td>
                <td class="px-2 py-2"><span class="' . $statusClass . '">' . $statusText . '</span></td>
                <td class="px-2 py-2">' . $actionBtn . '</td>
                <td class="px-2 py-2">' . ($lateMinutes > 0 ? $lateMinutes . ' menit' : '-') . '</td>
              </tr>';
    }

    // If no records found
    if (count($result) == 0) {
        echo '<tr><td colspan="8" class="text-center py-4">Tidak ada data peminjaman</td></tr>';
    }

} catch (Exception $e) {
    error_log('Error in history.php: ' . $e->getMessage());
    
    // Try fallback query without JOIN if the error might be table-related
    try {
        $fallbackSql = "SELECT p.id, p.nama, p.jenis_barang, 
                       p.tipe_durasi, p.waktu_mulai, p.durasi, p.status, p.admin,
                       p.waktu_peminjaman, p.waktu_pengembalian
                       FROM peminjaman p
                       ORDER BY p.waktu_peminjaman DESC";
        $fallbackStmt = $conn->prepare($fallbackSql);
        $fallbackStmt->execute();
        $result = $fallbackStmt->fetchAll(PDO::FETCH_ASSOC);
        $counter = 1;

        foreach ($result as $row) {
            // Format duration based on type
            $durasiDisplay = '';
            if ($row['tipe_durasi'] == 'jam') {
                $durasiDisplay = $row['durasi'] . ' jam';
                // Also show start and end datetime
                $waktuMulai = new DateTime($row['waktu_mulai']);
                $waktuSelesai = clone $waktuMulai;
                $waktuSelesai->modify('+' . $row['durasi'] . ' hours');
                
                $durasiDisplay .= '<br><small>' . 
                                  $waktuMulai->format("d/m/Y H:i") . ' - ' . 
                                  $waktuSelesai->format("d/m/Y H:i") . '</small>';
            } else { // per day
                $durasiDisplay = $row['durasi'] . ' hari';
                // Also show start and end date
                $tanggalMulai = new DateTime($row['waktu_mulai']);
                $tanggalSelesai = clone $tanggalMulai;
                $tanggalSelesai->modify('+' . $row['durasi'] . ' days');
                
                $durasiDisplay .= '<br><small>' . 
                                  $tanggalMulai->format("d/m/Y") . ' - ' . 
                                  $tanggalSelesai->format("d/m/Y") . '</small>';
            }
            
            // Check if item is overdue
            $statusClass = '';
            $statusText = $row['status'];
            $lateMinutes = 0;
            
            if ($statusText == 'Dipinjam') {
                $statusClass = 'status-dipinjam';
                
                // Check if currently overdue
                $now = new DateTime();
                $batasPengembalian = null;
                
                if ($row['tipe_durasi'] == 'jam') {
                    $waktuMulai = new DateTime($row['waktu_mulai']);
                    $batasPengembalian = clone $waktuMulai;
                    $batasPengembalian->modify('+' . $row['durasi'] . ' hours');
                } else {
                    $tanggalMulai = new DateTime($row['waktu_mulai']);
                    $batasPengembalian = clone $tanggalMulai;
                    $batasPengembalian->modify('+' . $row['durasi'] . ' days');
                }
                
                if ($now > $batasPengembalian) {
                    $statusText = 'Terlambat';
                    $statusClass = 'status-terlambat';
                    $lateMinutes = $now->getTimestamp() - $batasPengembalian->getTimestamp();
                    $lateMinutes = floor($lateMinutes / 60); // Convert to minutes
                }
            } else if ($statusText == 'Dikembalikan') {
                $statusClass = 'status-dikembalikan';
            }
            
            // Action button based on status
            $actionBtn = '';
            if ($statusText == 'Dipinjam' || $statusText == 'Terlambat') {
                $actionBtn = '<button onclick="kembalikanBarang(' . $row['id'] . ')" class="btn btn-sm btn-inverse">Kembalikan</button>';
            } else {
                $actionBtn = '<span class="text-muted">-</span>';
            }
            
            echo '<tr>
                    <td class="px-2 py-2">' . $counter++ . '</td>
                    <td class="px-2 py-2">' . htmlspecialchars($row['nama'] ?? '') . '</td>
                    <td class="px-2 py-2">-</td>
                    <td class="px-2 py-2">' . htmlspecialchars($row['jenis_barang'] ?? '') . '</td>
                    <td class="px-2 py-2">' . $durasiDisplay . '</td>
                    <td class="px-2 py-2"><span class="' . $statusClass . '">' . $statusText . '</span></td>
                    <td class="px-2 py-2">' . $actionBtn . '</td>
                    <td class="px-2 py-2">' . ($lateMinutes > 0 ? $lateMinutes . ' menit' : '-') . '</td>
                  </tr>';
        }

        // If no records found
        if (count($result) == 0) {
            echo '<tr><td colspan="8" class="text-center py-4">Tidak ada data peminjaman</td></tr>';
        }
        
    } catch (Exception $fallbackError) {
        error_log('Fallback error in history.php: ' . $fallbackError->getMessage());
        echo '<tr><td colspan="8" class="text-center">Error loading data: ' . htmlspecialchars($fallbackError->getMessage()) . '</td></tr>';
    }
}
?>