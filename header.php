<?php
$currentPage = basename($_SERVER['SCRIPT_NAME']);

$currentBrand = (string) ($_GET['brand'] ?? '');

$isHome = $currentPage === 'index.php';

$isApple = $currentPage === 'sanpham.php'
    && $currentBrand === 'Apple';

$isSamsung = $currentPage === 'sanpham.php'
    && $currentBrand === 'Samsung';

$isAdmin = in_array(
    $currentPage,
    ['quanly.php', 'form.php'],
    true
);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="theme-color" content="#09090d">

    <title><?= h($title ?? 'Tsuki Tech') ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&family=Cinzel:wght@500;600;700&display=swap"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="assets/style.css?v=2">
</head>

<body class="<?= $isAdmin ? 'admin-page' : '' ?>">

<div class="announcement">
    <div class="container announcement-inner">

        <span>
            <span class="tiny-star">✦</span>
            A LITTLE MAGIC. EVERYDAY TECH.
        </span>

        <a href="quanly.php">
            Quản lý cửa hàng ↗
        </a>

    </div>
</div>

<header class="site-header">

    <div class="container header-content">

        <a class="logo" href="index.php" aria-label="Tsuki Tech - Trang chủ">

            <span class="logo-symbol">☾</span>

            <span class="logo-text">
                TSUKI TECH
                <small>YOUR LITTLE TECH UNIVERSE</small>
            </span>

        </a>

        <nav class="main-nav" aria-label="Điều hướng chính">

            <a
                href="index.php"
                class="<?= $isHome ? 'active' : '' ?>"
                <?= $isHome ? 'aria-current="page"' : '' ?>
            >
                Trang chủ
            </a>

            <a
                href="sanpham.php?brand=Apple"
                class="<?= $isApple ? 'active' : '' ?>"
                <?= $isApple ? 'aria-current="page"' : '' ?>
            >
                Apple
            </a>

            <a
                href="sanpham.php?brand=Samsung"
                class="<?= $isSamsung ? 'active' : '' ?>"
                <?= $isSamsung ? 'aria-current="page"' : '' ?>
            >
                Samsung
            </a>

        </nav>

        <form action="sanpham.php" method="get" class="search-form">

            <svg
                width="18"
                height="18"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.7"
                aria-hidden="true"
            >
                <circle cx="10.5" cy="10.5" r="6.5"></circle>
                <path d="m16 16 4.5 4.5"></path>
            </svg>

            <input
                type="search"
                name="q"
                maxlength="120"
                placeholder="Bạn đang tìm gì?"
                aria-label="Tìm tên sản phẩm"
                value="<?= h($_GET['q'] ?? '') ?>"
            >

            <button type="submit">Tìm</button>

        </form>

    </div>

</header>

<main class="container">