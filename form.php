<?php
require_once 'functions.php';
$id = (string) ($_GET['id'] ?? '');
$isEdit = $id !== '';
$title = $isEdit ? 'Sửa sản phẩm' : 'Thêm sản phẩm';
$error = '';
$sp = ['_id' => '', 'name' => '', 'image' => 'assets/images/hh001.svg', 'brand' => 'Apple', 'price' => ''];
if ($isEdit) {
    try {
        $found = $hanghoa->findOne(['_id' => $id]);
        if (!$found) {
            thongbao('Không tìm thấy sản phẩm cần sửa.');
        }
        $sp = $found;
    } catch (Throwable $e) {
        thongbao('Không tải được sản phẩm cần sửa.');
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    kiemtra_token();
    $sp['_id'] = $isEdit ? $id : trim((string) ($_POST['_id'] ?? ''));
    $sp['name'] = trim((string) ($_POST['name'] ?? ''));
    $sp['image'] = trim((string) ($_POST['image'] ?? ''));
    $sp['brand'] = (string) ($_POST['brand'] ?? '');
    $sp['price'] = trim((string) ($_POST['price'] ?? ''));

    if (!preg_match('/^[A-Za-z0-9_-]{1,30}$/', $sp['_id'])) {
        $error = 'Mã sản phẩm chỉ gồm chữ không dấu, số, dấu gạch; tối đa 30 ký tự.';
    } elseif ($sp['name'] === '' || strlen($sp['name']) > 200) {
        $error = 'Tên sản phẩm không được trống và không được quá dài.';
    } elseif (!in_array($sp['brand'], ['Apple', 'Samsung'], true)) {
        $error = 'Thương hiệu phải là Apple hoặc Samsung.';
    } elseif (!is_numeric($sp['price']) || !is_finite((float) $sp['price']) || (float) $sp['price'] <= 0) {
        $error = 'Giá bán phải là số lớn hơn 0. Vui lòng nhập lại.';
    } elseif (!preg_match('~^assets/images/[A-Za-z0-9_-]+\.(svg|png|jpg|jpeg|webp)$~', $sp['image']) || !is_file(__DIR__ . '/' . $sp['image'])) {
        $error = 'Ảnh không hợp lệ. Nhập đường dẫn một ảnh có sẵn trong assets/images.';
    }
    if ($error === '') {
        $data = [
            'name' => $sp['name'],
            'image' => $sp['image'],
            'brand' => $sp['brand'],
            'price' => (float) $sp['price'],
            'ngaycapnhat' => new MongoDB\BSON\UTCDateTime()
        ];
        try {
            if ($isEdit) {
                $result = $hanghoa->updateOne(['_id' => $id], ['$set' => $data]);
                if ($result->getMatchedCount() === 0) {
                    $error = 'Cập nhật thất bại: sản phẩm không còn tồn tại.';
                } else {
                    thongbao('Cập nhật sản phẩm thành công.');
                }
            } else {
                $data['_id'] = $sp['_id'];
                $hanghoa->insertOne($data);
                thongbao('Thêm sản phẩm thành công.');
            }
        } catch (Throwable $e) {
            $error = $e->getCode() === 11000
                ? 'Mã sản phẩm đã tồn tại. Hãy nhập mã khác.'
                : 'Lưu sản phẩm thất bại. Vui lòng kiểm tra kết nối và thử lại.';
        }
    }
}
require 'header.php';
?>
<section class="page-title"><p class="eyebrow">GÓC QUẢN LÝ</p><h1><?= e($title) ?></h1></section>
<?php if ($error): ?><p class="alert error" role="alert"><?= e($error) ?></p><?php endif; ?>
<form class="product-form" method="post">
    <input type="hidden" name="token" value="<?= e($_SESSION['token']) ?>">
    <label for="id">Mã sản phẩm (_id)</label>
    <input id="id" name="_id" value="<?= e($sp['_id']) ?>" maxlength="30" placeholder="Ví dụ: HH009" required <?= $isEdit ? 'readonly' : '' ?>>
    <label for="name">Tên sản phẩm</label>
    <input id="name" name="name" value="<?= e($sp['name']) ?>" maxlength="200" placeholder="Ví dụ: iPhone 16" required>
    <label for="brand">Thương hiệu</label>
    <select id="brand" name="brand">
        <option value="Apple" <?= $sp['brand'] === 'Apple' ? 'selected' : '' ?>>Apple</option>
        <option value="Samsung" <?= $sp['brand'] === 'Samsung' ? 'selected' : '' ?>>Samsung</option>
    </select>
    <label for="price">Giá bán (VNĐ, lớn hơn 0)</label>
    <input id="price" name="price" type="number" step="any" value="<?= e($sp['price']) ?>" placeholder="Ví dụ: 15000000" required>
    <label for="image">Đường dẫn ảnh</label>
    <input id="image" name="image" list="images" value="<?= e($sp['image']) ?>" required>
    <datalist id="images">
        <?php for ($i = 1; $i <= 8; $i++): ?><option value="assets/images/hh<?= sprintf('%03d', $i) ?>.svg"><?php endfor; ?>
    </datalist>
    <small>Chọn ảnh gợi ý hoặc chép ảnh của bạn vào assets/images rồi nhập đường dẫn.</small>
    <div class="toolbar"><button>Lưu sản phẩm</button><a class="button secondary" href="quanly.php">Quay lại</a></div>
</form>
<?php require 'footer.php'; ?>
