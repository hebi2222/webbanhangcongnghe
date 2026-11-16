<article class="product-card">

    <div class="product-card-top">

        <span class="product-brand-pill">
            <?= h($sp['brand']) ?>
        </span>

        <span class="product-code">
            <?= h($sp['_id']) ?>
        </span>

    </div>

    <a
        class="product-image"
        href="chitiet.php?id=<?= urlencode($sp['_id']) ?>"
        data-fallback="<?= h($sp['brand']) ?>"
    >

        <img
            src="<?= h($sp['image']) ?>"
            alt="<?= h($sp['name']) ?>"
            loading="lazy"
            onerror="this.hidden=true;this.parentElement.classList.add('image-missing')"
        >

        <span class="image-view">
            Xem sản phẩm ↗
        </span>

    </a>

    <div class="product-content">

        <p class="product-category">
            <?= h($sp['brand']) ?> COLLECTION
        </p>

        <h3>
            <a href="chitiet.php?id=<?= urlencode($sp['_id']) ?>">
                <?= h($sp['name']) ?>
            </a>
        </h3>

        <p class="price">
            <?= gia($sp['price']) ?>
        </p>

        <div class="product-card-bottom">

            <small>
                <?= h(ngay($sp['ngaycapnhat'])) ?>
            </small>

            <a
                class="product-detail-link"
                href="chitiet.php?id=<?= urlencode($sp['_id']) ?>"
                aria-label="Xem chi tiết <?= h($sp['name']) ?>"
            >
                ↗
            </a>

        </div>

    </div>

</article>