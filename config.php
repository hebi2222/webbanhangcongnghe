<?php
// Sua cong MongoDB o day cho giong ket noi trong Compass.
$mongoUri = 'mongodb://localhost:27018';
$databaseName = 'bh_tri';

if (!extension_loaded('mongodb')) {
    exit('Chua bat extension mongodb trong PHP. Xem HUONG_DAN.md.');
}
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    exit('Chua co thu muc vendor. Mo Terminal tai thu muc web va chay: composer install');
}
require_once __DIR__ . '/vendor/autoload.php';

try {
    $client = new MongoDB\Client($mongoUri, ['serverSelectionTimeoutMS' => 3000], [
        'typeMap' => ['root' => 'array', 'document' => 'array', 'array' => 'array']
    ]);
    $db = $client->selectDatabase($databaseName);
    $db->command(['ping' => 1]);
    $hanghoa = $db->selectCollection('hanghoa_tri');
} catch (Throwable $e) {
    http_response_code(503);
    exit('Khong ket noi duoc MongoDB. Kiem tra dich vu MongoDB va cong trong config.php.');
}
