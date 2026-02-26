<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

try {
    $id = $_POST['id'];
    $waktu_pengembalian = date('Y-m-d H:i:s');

    $conn->beginTransaction();

    // Update borrowing record to "Dikembalikan"
    $sql = "UPDATE peminjaman 
            SET status = 'Dikembalikan', waktu_pengembalian = :waktu 
            WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':waktu', $waktu_pengembalian);
    $stmt->execute();

    // Get the item type from the borrowing record
    $item_sql = "SELECT jenis_barang FROM peminjaman WHERE id = :id";
    $item_stmt = $conn->prepare($item_sql);
    $item_stmt->bindParam(':id', $id);
    $item_stmt->execute();
    $jenis_barang = $item_stmt->fetchColumn();

    // Increment stock
    $update_stock_sql = "UPDATE barang_list SET stok = stok + 1 WHERE nama_barang = :jenis_barang";
    $stock_stmt = $conn->prepare($update_stock_sql);
    $stock_stmt->bindParam(':jenis_barang', $jenis_barang);
    $stock_stmt->execute();

    $conn->commit();

    echo json_encode(['success' => true, 'message' => 'Barang berhasil dikembalikan']);

} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>