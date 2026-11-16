<?php
require_once 'functions.php';
$id = (string) ($_GET['id'] ?? '');
$sp = null;
$error = '';
try {
    $sp = $hanghoa->findOne(['_id' => $id]);
    if (!$sp) {
        http_response_code(404);
        $error = 'Không tìm thấy sản phẩm.';
    }
} catch (Throwable $e) {
    http_response_code(503);
    $error = 'Không tải được sản phẩm. Vui lòng thử lại.';
}
$title = $sp ? $sp['name'] : 'Chi tiết sản phẩm';
require 'header.php';
?>
<p class="breadcrumb"><a href="index.php">Trang chủ</a> / Chi tiết sản phẩm</p>
<?php if ($error): ?><p class="alert error"><?= e($error) ?></p><?php endif; ?>
<?php if ($sp): ?>
<section class="detail">
    <img class="detail-image" src="<?= e($sp['image']) ?>" alt="<?= e($sp['name']) ?>">
    <div>
        <p class="eyebrow"><?= e($sp['brand']) ?></p>
        <h1><?= e($sp['name']) ?></h1>
        <p class="price big"><?= tien($sp['price']) ?></p>
        <dl>
            <dt>Mã sản phẩm</dt><dd><?= e($sp['_id']) ?></dd>
            <dt>Thương hiệu</dt><dd><?= e($sp['brand']) ?></dd>
            <dt>Ngày cập nhật</dt><dd><?= e(ngay($sp['ngaycapnhat'])) ?></dd>
        </dl>
        <a class="button" href="sanpham.php?brand=<?= urlencode($sp['brand']) ?>">Xem thêm <?= e($sp['brand']) ?></a>
    </div>
</section>
<?php endif; ?>
<?php require 'footer.php'; ?>
