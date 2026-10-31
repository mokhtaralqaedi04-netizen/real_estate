<?php
$page_title = $page_title ?? "البوصلة العقارية";
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($page_title) ?></title>

    <link rel="stylesheet"
          href="assets/css/style.css">

</head>

<body>

<!-- القائمة العلوية -->

<nav class="navbar">

    <div class="container">

        <a href="index.php" class="logo">
            البوصلة العقارية
        </a>

        <ul class="nav-links">

            <li>
                <a href="index.php">الرئيسية</a>
            </li>

            <li>
                <a href="properties.php">العقارات</a>
            </li>

            <li>
                <a href="about.php">من نحن</a>
            </li>

            <li>
                <a href="contact.php">تواصل معنا</a>
            </li>

            <li>
                <a href="login.php" class="login-btn">
                    تسجيل الدخول
                </a>
            </li>

        </ul>

    </div>

</nav>


<!-- محتوى الصفحة -->

<?php
if (isset($content)) {
    echo $content;
}
?>


<!-- الفوتر -->

<footer class="footer">

    <div class="container">

        <h3>البوصلة العقارية</h3>

        <p>
            خدمات عقارية موثوقة في اليمن
        </p>

        <p>
            © 2026 البوصلة العقارية - جميع الحقوق محفوظة
        </p>

    </div>

</footer>

</body>

</html>