<?php
require_once 'functions.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Hay dung nut Xoa tai trang quan ly.');
}
kiemtra_token();
$id = (string) ($_POST['id'] ?? '');
try {
    $result = $hanghoa->deleteOne(['_id' => $id]);
    if ($result->getDeletedCount() === 1) {
        thongbao('Xóa sản phẩm thành công.');
    }
    thongbao('Xóa thất bại: không tìm thấy sản phẩm.');
} catch (Throwable $e) {
    thongbao('Xóa sản phẩm thất bại. Vui lòng thử lại.');
}
