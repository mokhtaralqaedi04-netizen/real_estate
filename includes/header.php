<?php
$page_title = $page_title ?? "البوصلة العقارية";
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <meta name="description"
          content="البوصلة العقارية - خدمات بيع وشراء وتأجير العقارات في اليمن">

    <title><?= htmlspecialchars($page_title) ?></title>

    <link rel="stylesheet"
          href="assets/css/style.css">
</head>

<body>

<header class="site-header">

    <div class="container">

        <nav class="navbar">

            <!-- شعار الشركة -->

            <a href="index.php" class="logo">
                <img src="assets/images/logo.jpg" alt="البوصلة العقارية">
            </a>


            <!-- زر القائمة للجوال -->

            <button class="menu-toggle"
                    id="menuToggle"
                    aria-label="فتح القائمة">

                ☰

            </button>


            <!-- القائمة -->

            <ul class="nav-links" id="navLinks">

                <li>
                    <a href="index.php">
                        الرئيسية
                    </a>
                </li>

                <li>
                    <a href="properties.php">
                        العقارات
                    </a>
                </li>

                <li>
                    <a href="about.php">
                        من نحن
                    </a>
                </li>

                <li>
                    <a href="contact.php">
                        تواصل معنا
                    </a>
                </li>

                <li>
                    <a href="login.php" class="login-btn">
                        تسجيل الدخول
                    </a>
                </li>

            </ul>

        </nav>

    </div>

</header>

<main>