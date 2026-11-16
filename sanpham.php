<?php
require_once 'ketnoi.php';

$filter = boloc();

$brand = $filter['brand'] ?? '';

$q = trim((string) ($_GET['q'] ?? ''));

$title = $brand !== ''
    ? 'Bộ sưu tập ' . $brand
    : 'Tất cả sản phẩm';

$items = [];
$error = '';

try {
    $items = $hanghoa->find(
        $filter,
        [
            'sort' => ['ngaycapnhat' => -1, '_id' => 1]
        ]
    )->toArray();
} catch (Throwable $e) {
    $error = 'Không tải được danh sách sản phẩm.';
}

require 'header.php';
?>

<div class="breadcrumb">
    <a href="index.php">Trang chủ</a>
    <span>/</span>
    <span><?= h($title) ?></span>
</div>

<section class="catalog-heading">

    <div>

        <p class="eyebrow">THE TSUKI COLLECTION</p>

        <h1><?= h($title) ?></h1>

        <p>
            Khám phá thiết bị phù hợp với thế giới của bạn.
        </p>

    </div>

    <span class="catalog-heading-symbol" aria-hidden="true">✦</span>

</section>

<div class="catalog-toolbar">

    <div class="filter-tabs">

        <a
            class="<?= $brand === '' ? 'selected' : '' ?>"
            href="sanpham.php?<?= h(http_build_query(['q' => $q])) ?>"
        >
            Tất cả
        </a>

        <a
            class="<?= $brand === 'Apple' ? 'selected' : '' ?>"
            href="sanpham.php?<?= h(http_build_query(['brand' => 'Apple', 'q' => $q])) ?>"
        >
            Apple
        </a>

        <a
            class="<?= $brand === 'Samsung' ? 'selected' : '' ?>"
            href="sanpham.php?<?= h(http_build_query(['brand' => 'Samsung', 'q' => $q])) ?>"
        >
            Samsung
        </a>

    </div>

    <span class="sort-note">
        Sắp xếp: Mới cập nhật
    </span>

</div>

<?php if ($error !== ''): ?>

    <p class="message error"><?= h($error) ?></p>

<?php else: ?>

    <div class="result-line">

        <p>
            Tìm thấy
            <strong><?= count($items) ?></strong>
            sản phẩm

            <?php if ($q !== ''): ?>
                cho “<strong><?= h($q) ?></strong>”
            <?php endif; ?>
        </p>

        <?php if ($q !== ''): ?>

            <a
                href="sanpham.php?<?= h(http_build_query(['brand' => $brand])) ?>"
            >
                Xóa từ khóa ×
            </a>

        <?php endif; ?>

    </div>

    <div class="product-grid">

        <?php foreach ($items as $sp): ?>
            <?php require 'the_sanpham.php'; ?>
        <?php endforeach; ?>

    </div>

    <?php if (empty($items)): ?>

        <div class="empty">

            <span>⌕</span>

            <h3>Chưa tìm thấy thiết bị phù hợp</h3>

            <p>
                Thử tên ngắn hơn, ví dụ iphone, galaxy hoặc airpods.
            </p>

            <a class="button secondary" href="sanpham.php">
                Xem tất cả sản phẩm
            </a>

        </div>

    <?php endif; ?>

<?php endif; ?>

<?php require 'footer.php'; ?>