<?php

require_once "config/database.php";

$page_title = "تفاصيل العقار | البوصلة العقارية";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: properties.php");
    exit;
}

$stmt = $conn->prepare("
    SELECT *
    FROM properties
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([$id]);

$property = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$property) {
    http_response_code(404);
    die("العقار غير موجود.");
}


// ============================================================
// جلب الصور الإضافية
// ============================================================

$image_stmt = $conn->prepare("
    SELECT *
    FROM property_images
    WHERE property_id = ?
    ORDER BY id ASC
");

$image_stmt->execute([$id]);

$gallery_images = $image_stmt->fetchAll(PDO::FETCH_ASSOC);


$page_title =
    htmlspecialchars($property["title"])
    . " | البوصلة العقارية";

include "includes/header.php";

?>

<section class="property-details-section">

    <div class="container">

        <div class="property-details">


            <!-- ==================================================
                 معرض الصور
            ================================================== -->

            <div class="property-gallery">


                <!-- الصورة الرئيسية -->

                <div class="property-details-image">

                    <?php if (!empty($property["main_image"])): ?>

                        <img
                            src="assets/images/properties/<?= htmlspecialchars($property["main_image"]) ?>"
                            alt="<?= htmlspecialchars($property["title"]) ?>"
                            id="mainPropertyImage"
                        >

                    <?php else: ?>

                        <div class="no-image">
                            لا توجد صورة
                        </div>

                    <?php endif; ?>

                </div>


                <!-- الصور الإضافية -->

                <?php if (!empty($gallery_images)): ?>

                    <div class="property-thumbnails">

                        <?php foreach ($gallery_images as $image): ?>

                            <button
                                type="button"
                                class="property-thumbnail"
                                onclick="changePropertyImage(
                                    'assets/images/properties/<?= htmlspecialchars($image["image"]) ?>'
                                )"
                            >

                                <img
                                    src="assets/images/properties/<?= htmlspecialchars($image["image"]) ?>"
                                    alt="صورة إضافية للعقار"
                                >

                            </button>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>


            </div>


            <!-- ==================================================
                 معلومات العقار
            ================================================== -->

            <div class="property-details-content">


                <span class="property-purpose">

                    <?= htmlspecialchars($property["purpose"]) ?>

                </span>


                <h1>

                    <?= htmlspecialchars($property["title"]) ?>

                </h1>


                <p class="property-location">

                    📍

                    <?= htmlspecialchars($property["city"]) ?>

                    <?php if (!empty($property["address"])): ?>

                        -
                        <?= htmlspecialchars($property["address"]) ?>

                    <?php endif; ?>

                </p>


                <div class="property-details-price">

                    <?= number_format($property["price"], 0) ?>

                    ريال

                </div>


                <!-- معلومات العقار -->

                <div class="property-details-info">


                    <div>

                        <span>🏠 النوع</span>

                        <strong>

                            <?= htmlspecialchars(
                                $property["property_type"]
                            ) ?>

                        </strong>

                    </div>


                    <div>

                        <span>📐 المساحة</span>

                        <strong>

                            <?= htmlspecialchars(
                                $property["area"]
                            ) ?>

                            م²

                        </strong>

                    </div>


                    <div>

                        <span>🛏️ غرف النوم</span>

                        <strong>

                            <?= htmlspecialchars(
                                $property["bedrooms"]
                            ) ?>

                        </strong>

                    </div>


                    <div>

                        <span>🚿 دورات المياه</span>

                        <strong>

                            <?= htmlspecialchars(
                                $property["bathrooms"]
                            ) ?>

                        </strong>

                    </div>


                    <div>

                        <span>📌 الحالة</span>

                        <strong>

                            <?= htmlspecialchars(
                                $property["status"]
                            ) ?>

                        </strong>

                    </div>


                </div>


                <!-- وصف العقار -->

                <div class="property-description">

                    <h2>
                        وصف العقار
                    </h2>

                    <p>

                        <?= nl2br(
                            htmlspecialchars(
                                $property["description"]
                                ?? "لا يوجد وصف."
                            )
                        ) ?>

                    </p>

                </div>


                <!-- أزرار التواصل -->

                <div class="property-contact-buttons">

                    <a
                        href="contact.php"
                        class="btn btn-primary"
                    >
                        📞 تواصل معنا
                    </a>


                    <a
                        href="properties.php"
                        class="btn btn-outline"
                    >
                        ← العودة للعقارات
                    </a>

                </div>


            </div>

        </div>

    </div>

</section>


<!-- ============================================================
     تغيير الصورة الرئيسية
============================================================ -->

<script>

function changePropertyImage(imagePath) {

    const mainImage =
        document.getElementById("mainPropertyImage");

    if (mainImage) {

        mainImage.src = imagePath;

    }

}

</script>


<?php

include "includes/footer.php";

?>