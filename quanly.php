<?php
require_once __DIR__ . '/ketnoi.php';

$title = 'Quản lý cửa hàng - Tsuki Tech';

$filter = boloc();

$brand = $filter['brand'] ?? '';
$q = trim((string) ($_GET['q'] ?? ''));

$query = http_build_query([
    'brand' => $brand,
    'q' => $q
]);

$returnUrl = 'quanly.php';

if ($query !== '') {
    $returnUrl .= '?' . $query;
}

$error = '';
$editError = '';

$editData = [
    '_id' => '',
    'name' => '',
    'brand' => 'Apple',
    'price' => '',
    'image' => ''
];

$message = $_SESSION['thongbao'] ?? '';
unset($_SESSION['thongbao']);

// Xu ly sua san pham.
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && ($_POST['action'] ?? '') === 'update'
) {
    kiemtra_token();

    $editData = [
        '_id' => trim((string) ($_POST['id'] ?? '')),
        'name' => trim((string) ($_POST['name'] ?? '')),
        'brand' => trim((string) ($_POST['brand'] ?? '')),
        'price' => trim((string) ($_POST['price'] ?? '')),
        'image' => trim((string) ($_POST['image'] ?? ''))
    ];

    $anh = $editData['image'];

    // Anh la link http hoac https.
    $anhLink = filter_var($anh, FILTER_VALIDATE_URL)
        && in_array(
            strtolower((string) parse_url($anh, PHP_URL_SCHEME)),
            ['http', 'https'],
            true
        );

    // Anh nam trong thu muc assets.
    $anhAssets = preg_match(
        '~^assets/(?:[A-Za-z0-9_-]+/)*[A-Za-z0-9_-]+\.(jpg|jpeg|png|webp|svg|gif|avif)$~i',
        $anh
    ) && is_file(__DIR__ . '/' . $anh);

    if ($editData['_id'] === '') {
        $editError = 'Thiếu mã sản phẩm cần sửa.';
    } elseif (
        $editData['name'] === ''
        || strlen($editData['name']) > 200
    ) {
        $editError = 'Tên sản phẩm không được trống hoặc quá dài.';
    } elseif (
        !in_array($editData['brand'], ['Apple', 'Samsung'], true)
    ) {
        $editError = 'Thương hiệu phải là Apple hoặc Samsung.';
    } elseif (
        !is_numeric($editData['price'])
        || !is_finite((float) $editData['price'])
        || (float) $editData['price'] <= 0
    ) {
        $editError = 'Giá bán phải là số lớn hơn 0.';
    } elseif (!$anhLink && !$anhAssets) {
        $editError = 'Nhập link ảnh http/https hoặc đường dẫn ảnh có sẵn trong assets.';
    }

    if ($editError === '') {
        try {
            $result = $hanghoa->updateOne(
                ['_id' => $editData['_id']],
                [
                    '$set' => [
                        'name' => $editData['name'],
                        'brand' => $editData['brand'],
                        'price' => (float) $editData['price'],
                        'image' => $editData['image'],
                        'ngaycapnhat' => new MongoDB\BSON\UTCDateTime()
                    ]
                ]
            );

            if ($result->getMatchedCount() === 1) {
                $_SESSION['thongbao'] = 'Cập nhật sản phẩm thành công.';

                header('Location: ' . $returnUrl);
                exit;
            }

            $editError = 'Không tìm thấy sản phẩm cần sửa.';
        } catch (Throwable $e) {
            $editError = 'Cập nhật thất bại. Vui lòng kiểm tra kết nối MongoDB.';
        }
    }
}

// Doc danh sach va thong ke.
$items = [];

$tongSanPham = 0;
$tongApple = 0;
$tongSamsung = 0;

try {
    $items = $hanghoa->find(
        $filter,
        [
            'sort' => [
                'ngaycapnhat' => -1,
                '_id' => 1
            ]
        ]
    )->toArray();

    $tongSanPham = $hanghoa->countDocuments();

    $tongApple = $hanghoa->countDocuments([
        'brand' => 'Apple'
    ]);

    $tongSamsung = $hanghoa->countDocuments([
        'brand' => 'Samsung'
    ]);
} catch (Throwable $e) {
    $error = 'Không tải được dữ liệu cửa hàng.';
}

require __DIR__ . '/header.php';
?>

<div class="breadcrumb">
    <a href="index.php">Cửa hàng</a>
    <span>/</span>
    <span>Quản lý sản phẩm</span>
</div>

<section class="admin-heading">

    <div>
        <p class="eyebrow">STORE MANAGEMENT</p>

        <h1>Góc quản lý của Tri</h1>

        <p>
            Quản lý và cập nhật bộ sưu tập của cửa hàng.
        </p>
    </div>

    <a class="button" href="form.php">
        ＋ Thêm sản phẩm
    </a>

</section>

<?php if ($message !== ''): ?>

    <p class="message" role="status">
        <?= h($message) ?>
    </p>

<?php endif; ?>

<?php if ($error !== ''): ?>

    <p class="message error" role="alert">
        <?= h($error) ?>
    </p>

<?php else: ?>

    <div class="admin-stats">

        <div class="admin-stat-card">

            <span class="stat-icon">▦</span>

            <div>
                <span>Tổng sản phẩm</span>
                <strong><?= $tongSanPham ?></strong>
            </div>

            <small>TOÀN BỘ CỬA HÀNG</small>

        </div>

        <div class="admin-stat-card">

            <span class="stat-icon">A</span>

            <div>
                <span>Apple</span>
                <strong><?= $tongApple ?></strong>
            </div>

            <small>APPLE COLLECTION</small>

        </div>

        <div class="admin-stat-card">

            <span class="stat-icon">S</span>

            <div>
                <span>Samsung</span>
                <strong><?= $tongSamsung ?></strong>
            </div>

            <small>SAMSUNG COLLECTION</small>

        </div>

    </div>

<?php endif; ?>

<section class="management-panel">

    <div class="management-panel-heading">

        <div>
            <h2>Danh sách sản phẩm</h2>

            <p>
                Sắp xếp theo ngày cập nhật từ mới đến cũ.
            </p>
        </div>

        <a
            class="button secondary"
            href="xuat_pdf.php?<?= h($query) ?>"
        >
            ↓ Xuất PDF
        </a>

    </div>

    <div class="management-controls">

        <div class="filter-tabs">

            <a
                class="<?= $brand === '' ? 'selected' : '' ?>"
                href="quanly.php?<?= h(http_build_query([
                    'q' => $q
                ])) ?>"
            >
                Tất cả
            </a>

            <a
                class="<?= $brand === 'Apple' ? 'selected' : '' ?>"
                href="quanly.php?<?= h(http_build_query([
                    'brand' => 'Apple',
                    'q' => $q
                ])) ?>"
            >
                Apple
            </a>

            <a
                class="<?= $brand === 'Samsung' ? 'selected' : '' ?>"
                href="quanly.php?<?= h(http_build_query([
                    'brand' => 'Samsung',
                    'q' => $q
                ])) ?>"
            >
                Samsung
            </a>

        </div>

        <form
            action="quanly.php"
            method="get"
            class="admin-search"
        >

            <?php if ($brand !== ''): ?>

                <input
                    type="hidden"
                    name="brand"
                    value="<?= h($brand) ?>"
                >

            <?php endif; ?>

            <input
                type="search"
                name="q"
                maxlength="120"
                placeholder="Tìm theo tên sản phẩm..."
                aria-label="Tên sản phẩm cần tìm"
                value="<?= h($q) ?>"
            >

            <button type="submit">Tìm</button>

        </form>

    </div>

    <?php if ($error === ''): ?>

        <div class="management-result">

            <span>
                Tìm thấy
                <strong><?= count($items) ?></strong>
                sản phẩm

                <?php if ($q !== ''): ?>
                    cho “<?= h($q) ?>”
                <?php endif; ?>
            </span>

            <?php if ($brand !== '' || $q !== ''): ?>

                <a href="quanly.php">
                    Xóa bộ lọc ×
                </a>

            <?php endif; ?>

        </div>

    <?php endif; ?>

    <div class="table-wrap">

        <table>

            <thead>
                <tr>
                    <th>Mã sản phẩm</th>
                    <th>Sản phẩm</th>
                    <th>Thương hiệu</th>
                    <th>Giá bán</th>
                    <th>Cập nhật</th>
                    <th>Thao tác</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($items as $sp): ?>

                    <tr>

                        <td>
                            <span class="table-code">
                                <?= h($sp['_id']) ?>
                            </span>
                        </td>

                        <td>

                            <a
                                class="table-product"
                                href="chitiet.php?id=<?= urlencode($sp['_id']) ?>"
                            >

                                <span class="table-thumbnail">

                                    <img
                                        src="<?= h($sp['image']) ?>"
                                        alt=""
                                        loading="lazy"
                                        onerror="this.hidden=true"
                                    >

                                </span>

                                <span>
                                    <strong>
                                        <?= h($sp['name']) ?>
                                    </strong>

                                    <small>
                                        Xem chi tiết sản phẩm ↗
                                    </small>
                                </span>

                            </a>

                        </td>

                        <td>

                            <span class="table-brand">
                                <?= h($sp['brand']) ?>
                            </span>

                        </td>

                        <td class="nowrap table-price">
                            <?= gia($sp['price']) ?>
                        </td>

                        <td class="nowrap table-date">
                            <?= h(ngay($sp['ngaycapnhat'])) ?>
                        </td>

                        <td class="actions">

                            <button
                                type="button"
                                class="small secondary open-edit"
                                data-id="<?= h($sp['_id']) ?>"
                                data-name="<?= h($sp['name']) ?>"
                                data-brand="<?= h($sp['brand']) ?>"
                                data-price="<?= h($sp['price']) ?>"
                                data-image="<?= h($sp['image']) ?>"
                            >
                                Sửa
                            </button>

                            <form
                                action="xoa.php"
                                method="post"
                                onsubmit="return confirm('Bạn muốn xóa sản phẩm này?')"
                            >

                                <input
                                    type="hidden"
                                    name="id"
                                    value="<?= h($sp['_id']) ?>"
                                >

                                <input
                                    type="hidden"
                                    name="token"
                                    value="<?= h($_SESSION['token']) ?>"
                                >

                                <button
                                    type="submit"
                                    class="small danger"
                                >
                                    Xóa
                                </button>

                            </form>

                        </td>

                    </tr>

                <?php endforeach; ?>

                <?php if (empty($items) && $error === ''): ?>

                    <tr>
                        <td colspan="6">

                            <div class="table-empty">

                                <span>⌕</span>

                                <h3>
                                    Không có sản phẩm phù hợp
                                </h3>

                                <p>
                                    Thử thay đổi từ khóa hoặc bỏ bộ lọc.
                                </p>

                                <a href="quanly.php">
                                    Xem toàn bộ danh sách →
                                </a>

                            </div>

                        </td>
                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

    <div class="management-panel-footer">

        <span>
            TSUKI TECH / PRODUCT MANAGEMENT
        </span>

        <a href="index.php">
            Xem cửa hàng ↗
        </a>

    </div>

</section>

<dialog
    id="edit-modal"
    class="edit-modal"
    aria-labelledby="edit-title"
    data-open="<?= $editError !== '' ? '1' : '0' ?>"
>

    <div class="edit-modal-header">

        <div>
            <p class="eyebrow">EDIT PRODUCT</p>
            <h2 id="edit-title">Chỉnh sửa sản phẩm</h2>
        </div>

        <button
            type="button"
            class="edit-close"
            aria-label="Đóng khung chỉnh sửa"
        >
            ×
        </button>

    </div>

    <form
        method="post"
        action="<?= h($returnUrl) ?>"
        class="edit-modal-form"
    >

        <input
            type="hidden"
            name="action"
            value="update"
        >

        <input
            type="hidden"
            name="token"
            value="<?= h($_SESSION['token']) ?>"
        >

        <input
            type="hidden"
            name="id"
            id="edit-id"
            value="<?= h($editData['_id']) ?>"
        >

        <div class="edit-product-summary">

            <div class="edit-preview-box">

                <img
                    id="edit-preview"
                    alt="Ảnh sản phẩm đang sửa"
                    hidden
                >

                <span id="edit-preview-empty">☾</span>

            </div>

            <div>

                <span class="edit-summary-label">
                    ĐANG CHỈNH SỬA
                </span>

                <h3 id="edit-product-name">
                    <?= h($editData['name']) ?>
                </h3>

                <span id="edit-product-code">
                    <?= h($editData['_id']) ?>
                </span>

            </div>

        </div>

        <?php if ($editError !== ''): ?>

            <p class="message error" role="alert">
                <?= h($editError) ?>
            </p>

        <?php endif; ?>

        <div class="edit-field">

            <label for="edit-name">
                Tên sản phẩm
            </label>

            <input
                id="edit-name"
                type="text"
                name="name"
                maxlength="200"
                value="<?= h($editData['name']) ?>"
                required
            >

        </div>

        <div class="edit-field-row">

            <div class="edit-field">

                <label for="edit-brand">
                    Thương hiệu
                </label>

                <select id="edit-brand" name="brand">

                    <option
                        value="Apple"
                        <?= $editData['brand'] === 'Apple' ? 'selected' : '' ?>
                    >
                        Apple
                    </option>

                    <option
                        value="Samsung"
                        <?= $editData['brand'] === 'Samsung' ? 'selected' : '' ?>
                    >
                        Samsung
                    </option>

                </select>

            </div>

            <div class="edit-field">

                <label for="edit-price">
                    Giá bán (VNĐ)
                </label>

                <input
                    id="edit-price"
                    type="number"
                    name="price"
                    step="any"
                    value="<?= h($editData['price']) ?>"
                    required
                >

            </div>

        </div>

        <div class="edit-field">

            <label for="edit-image">
                Link ảnh hoặc đường dẫn trong assets
            </label>

            <input
                id="edit-image"
                type="text"
                name="image"
                placeholder="https://... hoặc assets/images/iphone.jpg"
                value="<?= h($editData['image']) ?>"
                required
            >

            <small>
                Ví dụ: assets/images/hh001.svg.
                Nếu dùng ảnh trên mạng, nhập link ảnh trực tiếp http/https.
            </small>

        </div>

        <p class="edit-note">
            Mã sản phẩm được giữ nguyên.
            Ngày cập nhật sẽ được làm mới khi lưu thành công.
        </p>

        <div class="edit-modal-actions">

            <button
                type="button"
                class="secondary edit-cancel"
            >
                Hủy
            </button>

            <button type="submit">
                Lưu thay đổi
            </button>

        </div>

    </form>

</dialog>

<script>
const editModal = document.getElementById("edit-modal");

const editId = document.getElementById("edit-id");
const editName = document.getElementById("edit-name");
const editBrand = document.getElementById("edit-brand");
const editPrice = document.getElementById("edit-price");
const editImage = document.getElementById("edit-image");

const productName = document.getElementById("edit-product-name");
const productCode = document.getElementById("edit-product-code");

const preview = document.getElementById("edit-preview");
const previewEmpty = document.getElementById("edit-preview-empty");

let lastEditButton = null;

// Hien thi anh xem truoc.
function updatePreview() {
    const path = editImage.value.trim();

    preview.hidden = true;
    previewEmpty.hidden = false;
    preview.removeAttribute("src");

    let isLink = false;

    try {
        const url = new URL(path);

        isLink = url.protocol === "http:"
            || url.protocol === "https:";
    } catch (error) {
        isLink = false;
    }

    const isAssets =
        /^assets\/(?:[A-Za-z0-9_-]+\/)*[A-Za-z0-9_-]+\.(jpg|jpeg|png|webp|svg|gif|avif)$/i.test(path);

    if (!isLink && !isAssets) {
        return;
    }

    preview.src = path;
}

preview.addEventListener("load", function () {
    preview.hidden = false;
    previewEmpty.hidden = true;
});

preview.addEventListener("error", function () {
    preview.hidden = true;
    previewEmpty.hidden = false;
});

// Mo khung sua.
function openModal() {
    if (!editModal.open) {
        editModal.showModal();
    }

    document.body.classList.add("edit-modal-open");
}

// Dong khung sua.
function closeModal() {
    editModal.close();
}

// Dien thong tin cua san pham vao khung sua.
document.querySelectorAll(".open-edit").forEach(function (button) {
    button.addEventListener("click", function () {
        lastEditButton = button;

        editId.value = button.dataset.id;
        editName.value = button.dataset.name;
        editBrand.value = button.dataset.brand;
        editPrice.value = button.dataset.price;
        editImage.value = button.dataset.image;

        productName.textContent = button.dataset.name;
        productCode.textContent = button.dataset.id;

        const oldError = editModal.querySelector(".message.error");

        if (oldError) {
            oldError.remove();
        }

        updatePreview();
        openModal();

        editName.focus();
    });
});

document.querySelector(".edit-close").addEventListener(
    "click",
    closeModal
);

document.querySelector(".edit-cancel").addEventListener(
    "click",
    closeModal
);

editModal.addEventListener("close", function () {
    document.body.classList.remove("edit-modal-open");

    if (lastEditButton) {
        lastEditButton.focus();
    }
});

// Cap nhat anh xem truoc khi doi duong dan.
editImage.addEventListener("input", updatePreview);

// Cap nhat ten trong phan tom tat.
editName.addEventListener("input", function () {
    productName.textContent = editName.value;
});

// Tu mo lai khung khi PHP bao du lieu khong hop le.
if (editModal.dataset.open === "1") {
    updatePreview();
    openModal();
}
</script>

<?php require __DIR__ . '/footer.php'; ?>