
<?php

session_start();

require_once "../config/database.php";
// عدد العقارات
$stmt = $conn->query("SELECT COUNT(*) FROM properties");
$properties_count = $stmt->fetchColumn();
// عدد الرسائل
$stmt = $conn->query("SELECT COUNT(*) FROM messages");
$messages_count = $stmt->fetchColumn();
// عدد الرسائل الجديدة
$stmt = $conn->query("
    SELECT COUNT(*)
    FROM messages
    WHERE is_read = 0
");
$new_messages_count = $stmt->fetchColumn();

/*
|--------------------------------------------------------------------------
| حماية لوحة التحكم
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["user_id"])) {

 header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>

<html lang="ar" dir="rtl">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>لوحة التحكم | البوصلة العقارية</title>

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
                لوحة التحكم
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

            <h1>
                لوحة التحكم
            </h1>

            <p>
                إدارة محتوى ومعلومات الموقع
            </p>

        </div>


        <div class="dashboard-grid">


            <!-- العقارات -->

            <div class="dashboard-card">

                <div class="dashboard-icon">
                    🏠
                </div>

                <h3>
                    العقارات
                </h3>
                <strong><?= $properties_count ?></strong>

                <p>
                    إضافة وتعديل وحذف العقارات
                </p>

                <a href="properties.php"
                   class="btn btn-primary">

                    إدارة العقارات

                </a>

            </div>


            <!-- الرسائل -->

            <div class="dashboard-card">

                <div class="dashboard-icon">
                    ✉️
                </div>

                <h3>
                    الرسائل
                </h3>
                <strong>
        <?= $messages_count ?>
    </strong>

                <p>
                    الاطلاع على رسائل العملاء
                </p>

                <a href="messages.php"
                   class="btn btn-primary">

                    الرسائل

                </a>

            </div>
<div class="dashboard-card new-messages">

    <h3>الرسائل الجديدة</h3>

    <strong>
        <?= $new_messages_count ?>
    </strong>

    <a href="messages.php">
        مراجعة الرسائل
    </a>

</div>

            <!-- الموقع -->

            <div class="dashboard-card">

                <div class="dashboard-icon">
                    🌐
                </div>

                <h3>
                    الموقع

                </h3>

                <p>
                    الانتقال إلى الموقع الرئيسي
                </p>

                <a href="../index.php"
                   class="btn btn-primary">

                    فتح الموقع

                </a>

            </div>


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
