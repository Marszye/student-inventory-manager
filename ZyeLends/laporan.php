<?php
include 'config.php';

try {
    // Ambil semua data peminjaman dari database
    $sql = "SELECT p.nama, p.jenis_barang, p.tipe_durasi, p.waktu_mulai, p.durasi, p.status, p.admin, p.waktu_peminjaman 
            FROM peminjaman p 
            ORDER BY p.waktu_peminjaman DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $borrowingReports = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Set header untuk download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="laporan_peminjaman.csv"');
    
    // Membuat output CSV
    $output = fopen('php://output', 'w');
    fputcsv($output, [
        'Nama Peminjam',
        'Jenis Barang',
        'Tipe Durasi',
        'Waktu Mulai',
        'Durasi',
        'Status',
        'Admin',
        'Waktu Peminjaman'
    ]);
    
    foreach ($borrowingReports as $report) {
        fputcsv($output, [
            $report['nama'],
            $report['jenis_barang'],
            $report['tipe_durasi'],
            $report['waktu_mulai'],
            $report['durasi'],
            $report['status'],
            $report['admin'],
            $report['waktu_peminjaman']
        ]);
    }
    
    fclose($output);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

exit;