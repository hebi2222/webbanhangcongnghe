<?php
require_once 'functions.php';
$error = '';
try {
    $items = $hanghoa->find(loc_sanpham(), [
        'sort' => ['ngaycapnhat' => -1, '_id' => 1]
    ])->toArray();
    ob_start();
    ?>
    <!doctype html><html lang="vi"><head><meta charset="utf-8">
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #252030; }
        h1 { color: #681c30; font-size: 22px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #eee5e9; }
        .price { text-align: right; white-space: nowrap; }
    </style></head><body>
    <h1>TSUKI TECH · Danh sách sản phẩm</h1>
    <p>Sinh viên: Tri | Số sản phẩm: <?= count($items) ?> | <?= date('d/m/Y') ?></p>
    <?php if (!empty($_GET['brand'])): ?><p>Thương hiệu: <?= e($_GET['brand']) ?></p><?php endif; ?>
    <?php if (!empty($_GET['q'])): ?><p>Từ khóa: <?= e($_GET['q']) ?></p><?php endif; ?>
    <table><thead><tr><th>Mã</th><th>Tên sản phẩm</th><th>Hãng</th><th>Giá bán</th><th>Cập nhật</th></tr></thead><tbody>
    <?php foreach ($items as $sp): ?>
        <tr><td><?= e($sp['_id']) ?></td><td><?= e($sp['name']) ?></td><td><?= e($sp['brand']) ?></td><td class="price"><?= tien($sp['price']) ?></td><td><?= e(ngay($sp['ngaycapnhat'])) ?></td></tr>
    <?php endforeach; ?>
    <?php if (!$items): ?><tr><td colspan="5">Không có sản phẩm phù hợp.</td></tr><?php endif; ?>
    </tbody></table></body></html>
    <?php
    $html = ob_get_clean();
    $pdf = new Dompdf\Dompdf();
    $pdf->loadHtml($html, 'UTF-8');
    $pdf->setPaper('A4', 'landscape');
    $pdf->render();
    $pdf->stream('hanghoa_tri.pdf', ['Attachment' => true]);
    exit;
} catch (Throwable $e) {
    if (ob_get_level() > 0) {
        ob_end_clean();
    }
    http_response_code(500);
    $error = 'Xuất PDF thất bại. Kiểm tra đã cài thư viện Dompdf bằng composer install.';
}
$title = 'Xuất PDF';
require 'header.php';
?>
<p class="alert error"><?= e($error) ?></p>
<a class="button" href="quanly.php">Quay lại quản lý</a>
<?php require 'footer.php'; ?>
