<?php
require_once __DIR__ . '/ketnoi.php';

$title = 'Tsuki Tech - Công nghệ mang dấu ấn của bạn';

$error = '';

$apple = [];
$samsung = [];
$bannerProducts = [];

$tongSanPham = 0;
$soThuongHieu = 0;

$moinhat = null;

try {
    $apple = $hanghoa->find(
        ['brand' => 'Apple'],
        [
            'sort' => ['ngaycapnhat' => -1, '_id' => 1],
            'limit' => 3
        ]
    )->toArray();

    $samsung = $hanghoa->find(
        ['brand' => 'Samsung'],
        [
            'sort' => ['ngaycapnhat' => -1, '_id' => 1],
            'limit' => 3
        ]
    )->toArray();

    $bannerProducts = $hanghoa->find(
        [],
        [
            'sort' => ['ngaycapnhat' => -1, '_id' => 1],
            'limit' => 4
        ]
    )->toArray();

    $tongSanPham = $hanghoa->countDocuments();

    $thuongHieu = $hanghoa->distinct('brand');

    $soThuongHieu = count($thuongHieu);

    $moinhat = $bannerProducts[0] ?? null;
} catch (Throwable $e) {
    $error = 'Không tải được dữ liệu sản phẩm. Vui lòng kiểm tra kết nối MongoDB.';
}

require __DIR__ . '/header.php';
?>

<link rel="stylesheet" href="assets/home.css?v=1">

<div class="tx-home" id="tx-home">

    <div class="tx-scroll-progress" aria-hidden="true">
        <span id="tx-progress"></span>
    </div>

    <section class="tx-hero">

        <div class="tx-hero-glow" aria-hidden="true"></div>

        <div class="tx-stars" aria-hidden="true">
            <?php for ($i = 0; $i < 14; $i++): ?>
                <span style="--i:<?= $i ?>"></span>
            <?php endfor; ?>
        </div>

        <div class="tx-hero-copy tx-reveal">

            <div class="tx-label">
                <span></span>
                WELCOME TO TSUKI'S UNIVERSE
            </div>

            <h1>
                Công nghệ.<br>
                Cá tính.<br>
                <em>Chất riêng.</em>
            </h1>

            <p class="tx-hero-description">
                Tìm thiết bị phù hợp với cách bạn học tập,
                sáng tạo và tận hưởng mỗi ngày.
                Khám phá thế giới Apple và Samsung tại Tsuki Tech.
            </p>

            <div class="tx-hero-actions">

                <a class="button tx-shine" href="sanpham.php">
                    Khám phá ngay
                    <span>↗</span>
                </a>

                <a class="tx-text-link" href="#tx-collections">
                    Xem bộ sưu tập
                    <span>↓</span>
                </a>

            </div>

            <div class="tx-hero-caption">
                <span></span>
                CHỌN CÔNG NGHỆ. THỂ HIỆN BẢN THÂN.
            </div>

            <button
                type="button"
                id="tx-motion-button"
                class="tx-motion-button"
                aria-pressed="false"
            >
                Bật hiệu ứng
            </button>

        </div>

        <div class="tx-showcase tx-reveal">

            <div class="tx-orbits" aria-hidden="true">
                <span></span>
                <span></span>
            </div>

            <span class="tx-spark tx-spark-one" aria-hidden="true">✦</span>
            <span class="tx-spark tx-spark-two" aria-hidden="true">✧</span>
            <span class="tx-spark tx-spark-three" aria-hidden="true">✦</span>

            <div class="tx-showcase-caption">
                <span>THE LATEST SELECTION</span>
                <span>☾ TSUKI TECH</span>
            </div>

            <?php if ($bannerProducts): ?>

                <div class="tx-floating">

                    <div class="tx-tilt" data-tx-tilt>

                        <div class="tx-slider" id="tx-slider">

                            <?php foreach ($bannerProducts as $i => $sp): ?>

                                <article
                                    class="tx-slide"
                                    <?= $i !== 0 ? 'hidden' : '' ?>
                                >

                                    <div class="tx-slide-heading">

                                        <span class="tx-slide-brand">
                                            <?= h($sp['brand']) ?>
                                        </span>

                                        <span class="tx-update-label">
                                            MỚI CẬP NHẬT
                                        </span>

                                    </div>

                                    <a
                                        class="tx-slide-image"
                                        href="chitiet.php?id=<?= urlencode($sp['_id']) ?>"
                                        data-fallback="<?= h($sp['brand']) ?>"
                                    >

                                        <img
                                            src="<?= h($sp['image']) ?>"
                                            alt="<?= h($sp['name']) ?>"
                                            loading="<?= $i === 0 ? 'eager' : 'lazy' ?>"
                                            onerror="this.hidden=true;this.parentElement.classList.add('image-missing')"
                                        >

                                    </a>

                                    <div class="tx-slide-info">

                                        <small><?= h($sp['_id']) ?></small>

                                        <h2>
                                            <a href="chitiet.php?id=<?= urlencode($sp['_id']) ?>">
                                                <?= h($sp['name']) ?>
                                            </a>
                                        </h2>

                                        <div class="tx-slide-price-row">

                                            <strong>
                                                <?= gia($sp['price']) ?>
                                            </strong>

                                            <a
                                                class="tx-round-link"
                                                href="chitiet.php?id=<?= urlencode($sp['_id']) ?>"
                                                aria-label="Xem <?= h($sp['name']) ?>"
                                            >
                                                ↗
                                            </a>

                                        </div>

                                    </div>

                                </article>

                            <?php endforeach; ?>

                        </div>

                    </div>

                </div>

                <div class="tx-slider-controls">

                    <div class="tx-dots">

                        <?php foreach ($bannerProducts as $i => $sp): ?>

                            <button
                                type="button"
                                class="tx-dot <?= $i === 0 ? 'selected' : '' ?>"
                                data-tx-slide="<?= $i ?>"
                                aria-label="Xem <?= h($sp['name']) ?>"
                                aria-pressed="<?= $i === 0 ? 'true' : 'false' ?>"
                            ></button>

                        <?php endforeach; ?>

                    </div>

                    <div class="tx-slider-arrows">

                        <button
                            type="button"
                            id="tx-prev"
                            aria-label="Sản phẩm trước"
                        >
                            ←
                        </button>

                        <span id="tx-slide-counter">
                            01 / <?= sprintf('%02d', count($bannerProducts)) ?>
                        </span>

                        <button
                            type="button"
                            id="tx-next"
                            aria-label="Sản phẩm tiếp theo"
                        >
                            →
                        </button>

                    </div>

                </div>

            <?php else: ?>

                <div class="tx-banner-empty">
                    <span>☾</span>
                    <p>Thế giới công nghệ của bạn bắt đầu tại đây.</p>
                </div>

            <?php endif; ?>

        </div>

    </section>

    <?php if ($error !== ''): ?>

        <p class="message error">
            <?= h($error) ?>
        </p>

    <?php else: ?>

        <section class="tx-stats tx-reveal">

            <div class="tx-stat">

                <strong data-tx-count="<?= $tongSanPham ?>">
                    <?= $tongSanPham ?>
                </strong>

                <div>
                    <span>SẢN PHẨM</span>
                    <small>Trong bộ sưu tập hiện tại</small>
                </div>

            </div>

            <div class="tx-stat">

                <strong data-tx-count="<?= $soThuongHieu ?>">
                    <?= $soThuongHieu ?>
                </strong>

                <div>
                    <span>THƯƠNG HIỆU</span>
                    <small>Khám phá phong cách của bạn</small>
                </div>

            </div>

            <div class="tx-stat tx-stat-update">

                <span class="tx-stat-icon">↻</span>

                <div>
                    <span>CẬP NHẬT GẦN NHẤT</span>

                    <small>
                        <?= $moinhat
                            ? h(ngay($moinhat['ngaycapnhat']))
                            : 'Chưa có sản phẩm' ?>
                    </small>
                </div>

            </div>

        </section>

    <?php endif; ?>

    <div class="tx-ribbon" aria-hidden="true">

        <div class="tx-ribbon-track">

            <?php for ($i = 0; $i < 2; $i++): ?>

                <span>
                    APPLE <b>✦</b>
                    SAMSUNG <b>✦</b>
                    TSUKI TECH <b>✦</b>
                    YOUR TECH UNIVERSE <b>✦</b>
                </span>

            <?php endfor; ?>

        </div>

    </div>

    <section id="tx-collections" class="tx-collections">

        <div class="section-heading tx-reveal">

            <div>
                <p class="eyebrow">TWO BRANDS. YOUR CHOICE.</p>
                <h2>Chọn thế giới của bạn</h2>
            </div>

            <p class="section-description">
                Hai cá tính công nghệ.<br>
                Một lựa chọn dành riêng cho bạn.
            </p>

        </div>

        <div class="tx-brands">

            <div class="tx-reveal">

                <a
                    class="tx-brand tx-apple tx-shine"
                    href="sanpham.php?brand=Apple"
                >

                    <div>
                        <span class="tx-brand-label">COLLECTION 01</span>

                        <h3>Apple</h3>

                        <p>
                            Tinh giản trong thiết kế.<br>
                            Liền mạch trong trải nghiệm.
                        </p>

                        <span class="tx-brand-link">
                            Khám phá Apple
                            <b>↗</b>
                        </span>
                    </div>

                    <span class="tx-brand-letter" aria-hidden="true">A</span>

                </a>

            </div>

            <div class="tx-reveal">

                <a
                    class="tx-brand tx-samsung tx-shine"
                    href="sanpham.php?brand=Samsung"
                >

                    <div>
                        <span class="tx-brand-label">COLLECTION 02</span>

                        <h3>Samsung</h3>

                        <p>
                            Mở rộng góc nhìn.<br>
                            Khơi nguồn ý tưởng mới.
                        </p>

                        <span class="tx-brand-link">
                            Khám phá Samsung
                            <b>↗</b>
                        </span>
                    </div>

                    <span class="tx-brand-letter" aria-hidden="true">S</span>

                </a>

            </div>

        </div>

    </section>

    <section id="tx-products" class="tx-products">

        <div class="section-heading tx-reveal">

            <div>
                <p class="eyebrow">FRESH PICKS FOR YOUR SETUP</p>
                <h2>Mới trong vũ trụ Tsuki</h2>
            </div>

            <div class="tx-tabs" aria-label="Lọc thương hiệu">

                <button
                    type="button"
                    class="selected"
                    data-tx-brand="all"
                    aria-pressed="true"
                >
                    Tất cả
                </button>

                <button
                    type="button"
                    data-tx-brand="Apple"
                    aria-pressed="false"
                >
                    Apple
                </button>

                <button
                    type="button"
                    data-tx-brand="Samsung"
                    aria-pressed="false"
                >
                    Samsung
                </button>

            </div>

        </div>

        <p class="tx-products-note tx-reveal">
            Khám phá 3 sản phẩm mới cập nhật của mỗi thương hiệu.
        </p>

        <?php
        $nhomSanPham = [
            'Apple' => $apple,
            'Samsung' => $samsung
        ];
        ?>

        <?php foreach ($nhomSanPham as $brandName => $products): ?>

            <section
                class="tx-product-group"
                data-tx-group="<?= h($brandName) ?>"
            >

                <div class="tx-group-heading tx-reveal">

                    <h3>
                        <span></span>
                        <?= h($brandName) ?>
                    </h3>

                    <a href="sanpham.php?brand=<?= urlencode($brandName) ?>">
                        Xem bộ sưu tập ↗
                    </a>

                </div>

                <div class="product-grid">

                    <?php foreach ($products as $sp): ?>

                        <div class="tx-product-item tx-reveal">

                            <div class="tx-tilt" data-tx-tilt>

                                <?php require __DIR__ . '/the_sanpham.php'; ?>

                            </div>

                            <button
                                type="button"
                                class="tx-quick-button"
                                data-tx-preview
                                data-id="<?= h($sp['_id']) ?>"
                                data-name="<?= h($sp['name']) ?>"
                                data-brand="<?= h($sp['brand']) ?>"
                                data-price="<?= h($sp['price']) ?>"
                                data-image="<?= h($sp['image']) ?>"
                                data-date="<?= h(ngay($sp['ngaycapnhat'])) ?>"
                            >
                                Xem nhanh
                                <span>＋</span>
                            </button>

                        </div>

                    <?php endforeach; ?>

                </div>

                <?php if (empty($products) && $error === ''): ?>

                    <div class="empty">
                        <span>☾</span>

                        <h3>
                            Chưa có sản phẩm <?= h($brandName) ?>
                        </h3>

                        <p>
                            Sản phẩm của thương hiệu sẽ xuất hiện tại đây.
                        </p>
                    </div>

                <?php endif; ?>

            </section>

        <?php endforeach; ?>

    </section>

    <section class="tx-search-section tx-reveal">

        <div>
            <p class="eyebrow">FIND YOUR NEXT FAVORITE</p>

            <h2>
                Thiết bị tiếp theo,<br>
                <em>cảm hứng tiếp theo.</em>
            </h2>

            <p>
                Tìm theo tên sản phẩm để khám phá
                thiết bị phù hợp với bạn.
            </p>
        </div>

        <form action="sanpham.php" method="get">

            <label for="tx-home-search">
                Bạn đang tìm gì?
            </label>

            <div class="tx-search-input">

                <input
                    id="tx-home-search"
                    type="search"
                    name="q"
                    maxlength="120"
                    placeholder="iPhone, iPad, Galaxy..."
                    required
                >

                <button type="submit">Tìm ↗</button>

            </div>

            <div class="tx-search-tags">
                <a href="sanpham.php?q=iphone">iPhone</a>
                <a href="sanpham.php?q=ipad">iPad</a>
                <a href="sanpham.php?q=galaxy">Galaxy</a>
                <a href="sanpham.php?q=airpods">AirPods</a>
            </div>

        </form>

    </section>

    <section class="tx-discovery tx-reveal">

        <div>
            <p class="eyebrow">MAKE SPACE FOR SOMETHING NEW</p>

            <h2>
                Góc nhỏ của bạn.<br>
                <em>Thế giới lớn hơn.</em>
            </h2>

            <p>
                Từ học tập, làm việc đến những phút giây thư giãn.
                Khám phá bộ sưu tập công nghệ của Tsuki.
            </p>

            <a class="button tx-shine" href="sanpham.php">
                Xem tất cả sản phẩm ↗
            </a>
        </div>

        <div class="tx-discovery-art" aria-hidden="true">
            <span class="tx-discovery-ring"></span>
            <span class="tx-discovery-moon">☾</span>
            <span class="tx-discovery-star">✦</span>
            <small>YOUR LITTLE TECH UNIVERSE</small>
        </div>

    </section>

    <a
        class="tx-back-top"
        href="#tx-home"
        aria-label="Về đầu trang"
    >
        ↑
    </a>

</div>

<dialog
    id="tx-preview"
    class="tx-preview"
    aria-labelledby="tx-preview-name"
>

    <button
        type="button"
        id="tx-preview-close"
        class="tx-preview-close"
        aria-label="Đóng xem nhanh"
    >
        ×
    </button>

    <div class="tx-preview-layout">

        <div class="tx-preview-image">
            <img id="tx-preview-image" alt="" hidden>
            <span id="tx-preview-fallback">☾</span>
        </div>

        <div class="tx-preview-content">

            <p class="eyebrow" id="tx-preview-brand"></p>

            <h2 id="tx-preview-name"></h2>

            <p class="price" id="tx-preview-price"></p>

            <dl>
                <dt>Mã sản phẩm</dt>
                <dd id="tx-preview-code"></dd>

                <dt>Cập nhật</dt>
                <dd id="tx-preview-date"></dd>
            </dl>

            <a
                id="tx-preview-link"
                class="button"
                href="sanpham.php"
            >
                Xem chi tiết đầy đủ ↗
            </a>

        </div>

    </div>

</dialog>

<script>
const txHome = document.getElementById("tx-home");

const txMotionButton =
    document.getElementById("tx-motion-button");

const txReducedMotion =
    window.matchMedia("(prefers-reduced-motion: reduce)");

let txMotionEnabled = !txReducedMotion.matches;

// Bat / tat hieu ung.
function txSetMotion(enabled) {
    txMotionEnabled = enabled && !txReducedMotion.matches;

    txHome.classList.toggle("tx-motion", txMotionEnabled);

    txMotionButton.textContent = txMotionEnabled
        ? "Tắt hiệu ứng"
        : "Bật hiệu ứng";

    txMotionButton.setAttribute(
        "aria-pressed",
        String(txMotionEnabled)
    );

    txMotionButton.disabled = txReducedMotion.matches;

    if (txReducedMotion.matches) {
        txMotionButton.textContent = "Đang giảm chuyển động";
    }

    if (!txMotionEnabled) {
        txHome.querySelectorAll(".tx-tilt").forEach(function (item) {
            item.style.removeProperty("--rx");
            item.style.removeProperty("--ry");
        });

        txHome.querySelectorAll(".tx-will-reveal").forEach(function (item) {
            item.classList.add("tx-visible");
        });
    }
}

txSetMotion(txMotionEnabled);

txMotionButton.addEventListener("click", function () {
    txSetMotion(!txMotionEnabled);
});

txReducedMotion.addEventListener("change", function () {
    txSetMotion(!txReducedMotion.matches);
});

// Banner san pham.
const txSlides = document.querySelectorAll(".tx-slide");
const txDots = document.querySelectorAll(".tx-dot");
const txSlider = document.getElementById("tx-slider");
const txSlideCounter = document.getElementById("tx-slide-counter");

let txCurrentSlide = 0;
let txSliderHover = false;
let txSliderFocus = false;

function txShowSlide(index) {
    if (txSlides.length === 0) {
        return;
    }

    txCurrentSlide =
        (index + txSlides.length) % txSlides.length;

    txSlides.forEach(function (slide, i) {
        slide.hidden = i !== txCurrentSlide;
    });

    txDots.forEach(function (dot, i) {
        const selected = i === txCurrentSlide;

        dot.classList.toggle("selected", selected);
        dot.setAttribute("aria-pressed", String(selected));
    });

    txSlideCounter.textContent =
        String(txCurrentSlide + 1).padStart(2, "0")
        + " / "
        + String(txSlides.length).padStart(2, "0");
}

if (txSlides.length > 0) {
    document.getElementById("tx-prev").addEventListener(
        "click",
        function () {
            txShowSlide(txCurrentSlide - 1);
        }
    );

    document.getElementById("tx-next").addEventListener(
        "click",
        function () {
            txShowSlide(txCurrentSlide + 1);
        }
    );

    txDots.forEach(function (dot) {
        dot.addEventListener("click", function () {
            txShowSlide(Number(dot.dataset.txSlide));
        });
    });

    txSlider.addEventListener("mouseenter", function () {
        txSliderHover = true;
    });

    txSlider.addEventListener("mouseleave", function () {
        txSliderHover = false;
    });

    txSlider.addEventListener("focusin", function () {
        txSliderFocus = true;
    });

    txSlider.addEventListener("focusout", function (event) {
        txSliderFocus = txSlider.contains(event.relatedTarget);
    });

    window.setInterval(function () {
        if (
            txMotionEnabled
            && !document.hidden
            && !txSliderHover
            && !txSliderFocus
            && !document.getElementById("tx-preview").open
        ) {
            txShowSlide(txCurrentSlide + 1);
        }
    }, 6000);
}

// Hien dan khi cuon.
if (
    "IntersectionObserver" in window
    && txMotionEnabled
) {
    const txRevealObserver = new IntersectionObserver(
        function (entries, observer) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add("tx-visible");
                    observer.unobserve(entry.target);
                }
            });
        },
        {
            threshold: 0.08
        }
    );

    document.querySelectorAll(".tx-reveal").forEach(function (item) {
        item.classList.add("tx-will-reveal");
        txRevealObserver.observe(item);
    });
}

// Dem so khi cuon toi.
function txAnimateCount(element) {
    const target = Number(element.dataset.txCount);

    if (!txMotionEnabled || target === 0) {
        element.textContent = target;
        return;
    }

    const start = performance.now();
    const duration = 1100;

    function update(now) {
        if (!txMotionEnabled) {
            element.textContent = target;
            return;
        }

        const progress = Math.min((now - start) / duration, 1);

        const eased = 1 - Math.pow(1 - progress, 3);

        element.textContent = Math.round(target * eased);

        if (progress < 1) {
            requestAnimationFrame(update);
        }
    }

    requestAnimationFrame(update);
}

if ("IntersectionObserver" in window) {
    const txCountObserver = new IntersectionObserver(
        function (entries, observer) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    txAnimateCount(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        },
        {
            threshold: 0.5
        }
    );

    document.querySelectorAll("[data-tx-count]").forEach(function (item) {
        txCountObserver.observe(item);
    });
}

// Nghieng the theo chuot tren may tinh.
if (window.matchMedia("(hover: hover) and (pointer: fine)").matches) {
    document.querySelectorAll("[data-tx-tilt]").forEach(function (item) {
        item.addEventListener("pointermove", function (event) {
            if (!txMotionEnabled) {
                return;
            }

            const rect = item.getBoundingClientRect();

            const x = (event.clientX - rect.left) / rect.width - 0.5;
            const y = (event.clientY - rect.top) / rect.height - 0.5;

            item.style.setProperty("--rx", (-y * 5) + "deg");
            item.style.setProperty("--ry", (x * 7) + "deg");
        });

        item.addEventListener("pointerleave", function () {
            item.style.setProperty("--rx", "0deg");
            item.style.setProperty("--ry", "0deg");
        });
    });
}

// Loc thuong hieu tren trang chu.
document.querySelectorAll("[data-tx-brand]").forEach(function (button) {
    button.addEventListener("click", function () {
        const selectedBrand = button.dataset.txBrand;

        document.querySelectorAll("[data-tx-brand]").forEach(function (item) {
            const selected = item === button;

            item.classList.toggle("selected", selected);
            item.setAttribute("aria-pressed", String(selected));
        });

        document.querySelectorAll("[data-tx-group]").forEach(function (group) {
            group.hidden =
                selectedBrand !== "all"
                && group.dataset.txGroup !== selectedBrand;
        });
    });
});

// Thanh tien trinh cuon.
const txProgress = document.getElementById("tx-progress");

let txScrollScheduled = false;

function txUpdateProgress() {
    const maxScroll =
        document.documentElement.scrollHeight - window.innerHeight;

    const progress = maxScroll > 0
        ? Math.min(1, Math.max(0, window.scrollY / maxScroll))
        : 0;

    txProgress.style.transform = "scaleX(" + progress + ")";

    txScrollScheduled = false;
}

window.addEventListener("scroll", function () {
    if (!txScrollScheduled) {
        txScrollScheduled = true;
        requestAnimationFrame(txUpdateProgress);
    }
}, { passive: true });

window.addEventListener("resize", txUpdateProgress);
window.addEventListener("load", txUpdateProgress);

txUpdateProgress();

// Khung xem nhanh.
const txPreview = document.getElementById("tx-preview");
const txPreviewImage = document.getElementById("tx-preview-image");
const txPreviewFallback = document.getElementById("tx-preview-fallback");

txPreviewImage.addEventListener("load", function () {
    txPreviewImage.hidden = false;
    txPreviewFallback.hidden = true;
});

txPreviewImage.addEventListener("error", function () {
    txPreviewImage.hidden = true;
    txPreviewFallback.hidden = false;
});

document.querySelectorAll("[data-tx-preview]").forEach(function (button) {
    button.addEventListener("click", function () {
        const product = button.dataset;

        document.getElementById("tx-preview-name").textContent =
            product.name;

        document.getElementById("tx-preview-brand").textContent =
            product.brand + " COLLECTION";

        document.getElementById("tx-preview-code").textContent =
            product.id;

        document.getElementById("tx-preview-date").textContent =
            product.date;

        document.getElementById("tx-preview-price").textContent =
            new Intl.NumberFormat("vi-VN").format(
                Number(product.price)
            ) + " đ";

        document.getElementById("tx-preview-link").href =
            "chitiet.php?id=" + encodeURIComponent(product.id);

        txPreviewImage.hidden = true;
        txPreviewFallback.hidden = false;

        txPreviewImage.alt = product.name;
        txPreviewImage.removeAttribute("src");
        txPreviewImage.src = product.image;

        txPreview.showModal();

        document.body.classList.add("tx-dialog-open");
    });
});

document.getElementById("tx-preview-close").addEventListener(
    "click",
    function () {
        txPreview.close();
    }
);

txPreview.addEventListener("close", function () {
    document.body.classList.remove("tx-dialog-open");
});
</script>

<?php require __DIR__ . '/footer.php'; ?>