<?php
session_start();
include 'config.php';

// Validate form submission
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['danger'] = 'Akses ditolak';
    header('Location: index.php');
    exit;
}

try {
    // Get form data
    $nama = $_POST['nama'];
    $jenis_barang = $_POST['jenis_barang'];
    $tipe_durasi = $_POST['tipe_durasi'];
    $admin = $_POST['admin'];
    
    // Set waktu mulai and durasi based on type
    $waktu_mulai = null;
    $durasi = null;
    
    if ($tipe_durasi == 'jam') {
        $waktu_mulai = $_POST['waktu_mulai_jam'];
        $durasi = $_POST['durasi_jam'];
    } else { // per day
        $waktu_mulai = $_POST['tanggal_mulai'];
        $durasi = $_POST['durasi_hari'];
    }
    
    // Check if item is available (stock > 0)
    $check_sql = "SELECT stok FROM barang_list WHERE nama_barang = :jenis_barang";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bindParam(':jenis_barang', $jenis_barang);
    $check_stmt->execute();
    
    $stok = $check_stmt->fetchColumn();
    
    if ($stok <= 0) {
        throw new Exception('Stok barang tidak tersedia');
    }
    
    // Start transaction
    $conn->beginTransaction();
    
    // Insert borrowing record
    $sql = "INSERT INTO peminjaman (nama, jenis_barang, tipe_durasi, waktu_mulai, durasi, status, admin, waktu_peminjaman) 
            VALUES (:nama, :jenis_barang, :tipe_durasi, :waktu_mulai, :durasi, 'Dipinjam', :admin, NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nama', $nama);
    $stmt->bindParam(':jenis_barang', $jenis_barang);
    $stmt->bindParam(':tipe_durasi', $tipe_durasi);
    $stmt->bindParam(':waktu_mulai', $waktu_mulai);
    $stmt->bindParam(':durasi', $durasi);
    $stmt->bindParam(':admin', $admin);
    $stmt->execute();
    
    // Update stock
    $update_sql = "UPDATE barang_list SET stok = stok - 1 WHERE nama_barang = :jenis_barang";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bindParam(':jenis_barang', $jenis_barang);
    $update_stmt->execute();
    
    // Add/update borrower in the peminjam table if they don't exist
    $borrower_sql = "INSERT INTO peminjam (nama, total_peminjaman) 
                    VALUES (:nama, 1) 
                    ON DUPLICATE KEY UPDATE total_peminjaman = total_peminjaman + 1";
    $borrower_stmt = $conn->prepare($borrower_sql);
    $borrower_stmt->bindParam(':nama', $nama);
    $borrower_stmt->execute();
    
    // Commit transaction
    $conn->commit();
    
    $_SESSION['message'] = [
        'type' => 'Success',
        'text' => 'Peminjaman berhasil dicatat'
    ];
    
} catch (Exception $e) {
    // Rollback transaction if error
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    
    $_SESSION['danger'] = 'Error: ' . $e->getMessage();
}

// Redirect back to index
header('Location: index.php');
exit;