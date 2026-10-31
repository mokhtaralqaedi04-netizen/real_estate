
<?php

session_start();

require_once "../config/database.php";


/*
|--------------------------------------------------------------------------
| حماية الصفحة
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["user_id"])) {

    header("Location: ../login.php");
    exit;

}


/*
|--------------------------------------------------------------------------
| جلب العقارات
|--------------------------------------------------------------------------
*/

try {

    $stmt = $conn->query("
        SELECT *
        FROM properties
        ORDER BY id DESC
    ");

    $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    die("حدث خطأ أثناء جلب العقارات.");

}

?>

<!DOCTYPE html>

<html lang="ar" dir="rtl">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        إدارة العقارات | البوصلة العقارية
    </title>

    <link rel="stylesheet"
          href="../assets/css/style.css">

</head>


<body>


<header class="admin-header">

    <div class="container admin-navbar">

        <div>

            <h2>
                البوصلة العقارية
            </h2>

            <span>
                إدارة العقارات
            </span>

        </div>


        <div class="admin-user">

            <span>
                مرحبًا،
                <?= htmlspecialchars($_SESSION["full_name"]) ?>
            </span>

            <a href="logout.php">
                تسجيل الخروج
            </a>

        </div>

    </div>

</header>



<main class="admin-main">

    <div class="container">


        <div class="admin-title">

            <div>

                <h1>
                    إدارة العقارات
                </h1>

                <p>
                    إضافة وتعديل وحذف العقارات
                </p>

            </div>


            <a href="add_property.php"
               class="btn btn-primary">

                + إضافة عقار

            </a>

        </div>



        <?php if (count($properties) > 0): ?>


            <div class="admin-table-container">

                <table class="admin-table">

                    <thead>

                        <tr>

                            <th>
                                #
                            </th>

                            <th>
                                الصورة
                            </th>

                            <th>
                                العقار
                            </th>

                            <th>
                                النوع
                            </th>

                            <th>
                                الغرض
                            </th>

                            <th>
                                المدينة
                            </th>

                            <th>
                                السعر
                            </th>

                            <th>
                                الحالة
                            </th>

                            <th>
                                الإجراءات
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php foreach ($properties as $property): ?>


                        <tr>


                            <td>
                                <?= $property["id"] ?>
                            </td>


                            <td>

                                <?php if (!empty($property["main_image"])): ?>

                                    <img
                                        src="../assets/images/properties/<?= htmlspecialchars($property["main_image"]) ?>"
                                        class="admin-property-image"
                                        alt=""
                                    >

                                <?php else: ?>

                                    <span>
                                        لا توجد صورة
                                    </span>

                                <?php endif; ?>

                            </td>


                            <td>

                                <strong>
                                    <?= htmlspecialchars($property["title"]) ?>
                                </strong>

                            </td>


                            <td>

                                <?= htmlspecialchars($property["property_type"]) ?>

                            </td>


                            <td>

                                <?= htmlspecialchars($property["purpose"]) ?>

                            </td>


                            <td>

                                <?= htmlspecialchars($property["city"]) ?>

                            </td>


                            <td>

                                <?= number_format(
                                    $property["price"],
                                    0
                                ) ?>

                                ريال

                            </td>


                            <td>

                                <span class="admin-status">

                                    <?= htmlspecialchars($property["status"]) ?>

                                </span>

                            </td>


                            <td>

                                <div class="admin-actions">

                                    <a
                                        href="edit_property.php?id=<?= $property["id"] ?>"
                                        class="edit-btn"
                                    >
                                        تعديل
                                    </a>


                                    <a
                                        href="delete_property.php?id=<?= $property["id"] ?>"
                                        class="delete-btn"
                                        onclick="return confirm('هل أنت متأكد من حذف هذا العقار؟');"
                                    >
                                        حذف
                                    </a>

                                </div>

                            </td>


                        </tr>


                    <?php endforeach; ?>


                    </tbody>

                </table>

            </div>


        <?php else: ?>


            <div class="empty-properties">

                <h3>
                    لا توجد عقارات
                </h3>

                <p>
                    لم تتم إضافة أي عقارات حتى الآن.
                </p>

                <br>

                <a
                    href="add_property.php"
                    class="btn btn-primary"
                >
                    إضافة أول عقار
                </a>

            </div>


        <?php endif; ?>


        <div style="margin-top: 25px;">

            <a href="index.php">
                ← العودة إلى لوحة التحكم
            </a>

        </div>


    </div>

</main>



<footer class="admin-footer">

    <p>
        © 2026 البوصلة العقارية - لوحة التحكم
    </p>

</footer>


</body>

</html>