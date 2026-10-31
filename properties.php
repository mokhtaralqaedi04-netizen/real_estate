<?php

require_once "config/database.php";

$page_title = "العقارات | البوصلة العقارية";

include "includes/header.php";

?>

<section class="page-header">

    <div class="container">

        <h1>عقاراتنا</h1>

        <p>
            اكتشف مجموعة من العقارات المتاحة للبيع والإيجار
        </p>

    </div>

</section>


<section class="properties-section">

    <div class="container">

        <div class="section-title">

            <span>العقارات</span>

            <h2>أحدث العقارات المتاحة</h2>

        </div>


        <div class="properties-grid">

            <?php

            try {

                $stmt = $conn->query("
                    SELECT *
                    FROM properties
                    ORDER BY id DESC
                ");

                $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($properties) > 0):

                    foreach ($properties as $property):

            ?>

                        <div class="property-card">

                            <div class="property-image">

                                <?php if (!empty($property['main_image'])): ?>

                                    <img
                                        src="assets/images/properties/<?= htmlspecialchars($property['main_image']) ?>"
                                        alt="<?= htmlspecialchars($property['title']) ?>"
                                    >

                                <?php else: ?>

                                    <div class="no-image">
                                        لا توجد صورة
                                    </div>

                                <?php endif; ?>

                            </div>


                            <div class="property-content">

                                <span class="property-purpose">

                                    <?= htmlspecialchars($property['purpose']) ?>

                                </span>


                                <h3>

    <a
    href="/property.php?id=<?= $property['id'] ?>"
    class="property-title-link"
>

        <?= htmlspecialchars($property['title']) ?>

    </a>
</h3>

                                <p class="property-location">

                                    📍 <?= htmlspecialchars($property['city']) ?>

                                </p>


                                <div class="property-info">

                                    <span>
                                        🏠
                                        <?= htmlspecialchars($property['property_type']) ?>
                                    </span>

                                    <span>
                                        📐
                                        <?= htmlspecialchars($property['area']) ?>
                                        م²
                                    </span>

                                </div>


                                <div class="property-bottom">

                                    <strong>

                                        <?= number_format(
                                            $property['price'],
                                            0
                                        ) ?>

                                        ريال

                                    </strong>

                                    <span class="status">

                                        <?= htmlspecialchars($property['status']) ?>

                                    </span>

                                </div>

                            </div>

                        </div>

            <?php

                    endforeach;

                else:

            ?>

                <div class="empty-properties">

                    <h3>
                        لا توجد عقارات حاليًا
                    </h3>

                    <p>
                        سيتم عرض العقارات هنا عند إضافتها.
                    </p>

                </div>

            <?php

                endif;

            } catch (PDOException $e) {

                echo "<p>حدث خطأ أثناء جلب العقارات.</p>";

            }

            ?>

        </div>

    </div>

</section>


<?php

include "includes/footer.php";

?>