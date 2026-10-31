<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once "../config/database.php";

$page_title = "رسائل العملاء";

$stmt = $conn->query("
    SELECT *
    FROM messages
    ORDER BY id DESC
");

$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $page_title ?> | البوصلة العقارية</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

<header class="admin-header">

    <div class="container">

        <div class="admin-navbar">

            <div>

                <h1>البوصلة العقارية</h1>

                <span>لوحة التحكم</span>

            </div>

            <div class="admin-user">

                مرحبًا،
                <?= htmlspecialchars($_SESSION["full_name"]) ?>

                <a href="logout.php">تسجيل الخروج</a>

            </div>

        </div>

    </div>

</header>


<main class="admin-main">

    <div class="container">

        <div class="admin-title">

            <div>

                <h2>رسائل العملاء</h2>

                <p>
                    جميع الرسائل المرسلة من نموذج التواصل.
                </p>

            </div>

            <a href="index.php" class="btn btn-outline">
                ← لوحة التحكم
            </a>

        </div>


        <?php if (empty($messages)): ?>

            <div class="empty-properties">

                <h3>لا توجد رسائل حاليًا</h3>

                <p>
                    عندما يرسل أحد العملاء رسالة ستظهر هنا.
                </p>

            </div>

        <?php else: ?>

            <div class="admin-table-container">

                <table class="admin-table">

                    <thead>

    <tr>

        <th>#</th>

        <th>الاسم</th>

        <th>الهاتف</th>

        <th>البريد الإلكتروني</th>

        <th>الرسالة</th>

        <th>الحالة</th>

        <th>التاريخ</th>

        <th>الإجراءات</th>

    </tr>

</thead>


                    <tbody>

    <?php foreach ($messages as $message): ?>

        <tr>

            <!-- رقم الرسالة -->
            <td>
                <?= $message["id"] ?>
            </td>


            <!-- الاسم -->
            <td>
                <?= htmlspecialchars($message["name"]) ?>
            </td>


            <!-- الهاتف -->
            <td>
                <?= htmlspecialchars($message["phone"]) ?>
            </td>


            <!-- البريد الإلكتروني -->
            <td>
                <?= htmlspecialchars($message["email"] ?: "غير متوفر") ?>
            </td>


            <!-- الرسالة -->
            <td class="message-cell">
                <?= nl2br(htmlspecialchars($message["message"])) ?>
            </td>


            <!-- الحالة -->
            <td>

                <?php if ($message["is_read"] == 1): ?>

                    <span class="admin-status">
                        مقروءة
                    </span>

                <?php else: ?>

                    <span class="admin-status unread">
                        جديدة
                    </span>

                <?php endif; ?>

            </td>


            <!-- التاريخ -->
            <td>
                <?= htmlspecialchars($message["created_at"]) ?>
            </td>


            <!-- الإجراءات -->
            <td class="admin-actions">

                <?php if ($message["is_read"] == 0): ?>

                    <a
                        href="mark_message_read.php?id=<?= $message["id"] ?>"
                        class="edit-btn"
                    >
                        ✓ مقروءة
                    </a>

                <?php endif; ?>


                <a
                    href="delete_message.php?id=<?= $message["id"] ?>"
                    class="delete-btn"
                    onclick="return confirm('هل أنت متأكد من حذف هذه الرسالة؟');"
                >
                    🗑 حذف
                </a>

            </td>

        </tr>

    <?php endforeach; ?>

</tbody>

                </table>

            </div>

        <?php endif; ?>

    </div>

</main>


<footer class="admin-footer">

    <div class="container">

        <p>
            © 2026 البوصلة العقارية - لوحة التحكم
        </p>

    </div>

</footer>

</body>

</html>