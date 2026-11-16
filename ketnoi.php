<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Asia/Ho_Chi_Minh');

if (!extension_loaded('mongodb')) {
    exit('PHP chua bat extension mongodb.');
}

if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    exit('Khong tim thay vendor/autoload.php trong web_bh_tri.');
}

require_once __DIR__ . '/vendor/autoload.php';

try {
    $client = new MongoDB\Client(
        'mongodb://localhost:27018',
        [
            'serverSelectionTimeoutMS' => 3000
        ],
        [
            'typeMap' => [
                'root' => 'array',
                'document' => 'array',
                'array' => 'array'
            ]
        ]
    );

    $db = $client->selectDatabase('bh_tri');

    $db->command(['ping' => 1]);

    $hanghoa = $db->selectCollection('hanghoa_tri');

} catch (Throwable $e) {
    exit('Khong ket noi duoc MongoDB. Kiem tra cong 27018 va dich vu MongoDB.');
}

function h($text)
{
    return htmlspecialchars(
        (string) $text,
        ENT_QUOTES,
        'UTF-8'
    );
}

function gia($number)
{
    return number_format((float) $number, 0, ',', '.') . ' đ';
}

function ngay($value)
{
    if ($value instanceof MongoDB\BSON\UTCDateTime) {
        $date = $value->toDateTime();

        $date->setTimezone(
            new DateTimeZone('Asia/Ho_Chi_Minh')
        );

        return $date->format('d/m/Y H:i');
    }

    return '';
}

function thongbao($message)
{
    $_SESSION['thongbao'] = $message;

    header('Location: quanly.php');
    exit;
}

if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(16));
}

function kiemtra_token()
{
    $token = $_POST['token'] ?? '';

    if (
        !is_string($token)
        || !hash_equals($_SESSION['token'], $token)
    ) {
        http_response_code(403);
        exit('Phien lam viec khong hop le. Hay tai lai trang.');
    }
}

function boloc()
{
    $filter = [];

    $brand = trim((string) ($_GET['brand'] ?? ''));
    $q = trim((string) ($_GET['q'] ?? ''));

    if ($brand === 'Apple' || $brand === 'Samsung') {
        $filter['brand'] = $brand;
    }

    if ($q !== '') {
        $filter['name'] = new MongoDB\BSON\Regex(
            preg_quote($q),
            'i'
        );
    }

    return $filter;
}