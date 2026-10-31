<?php

require_once "config/database.php";

$page_title = "البوصلة العقارية | الرئيسية";

include "includes/header.php";

?>

<section class="hero">

    <div class="container">

        <div class="hero-content">

            <span class="hero-subtitle">
                مرحبًا بكم في
            </span>

            <h1>
                البوصلة العقارية
            </h1>

            <h2>
                وجهتك الموثوقة لعقارات اليمن
            </h2>

            <p>
                نساعدك في العثور على العقار المناسب
                للبيع أو الشراء أو الإيجار،
                بكل سهولة وثقة.
            </p>

            <div class="hero-buttons">

                <a href="properties.php"
                   class="btn btn-primary">
                    استكشف العقارات
                </a>

                <a href="contact.php"
                   class="btn btn-outline">
                    تواصل معنا
                </a>

            </div>

        </div>

    </div>

</section>


<section class="welcome-section">

    <div class="container">

        <div class="section-title">

            <span>
                من نحن
            </span>

            <h2>
                شريكك العقاري الموثوق
            </h2>

        </div>

        <p class="section-description">

            تقدم شركة البوصلة العقارية حلولًا عقارية
            متكاملة في السوق اليمني، ونساعد عملاءنا
            في الوصول إلى العقار المناسب وفق احتياجاتهم
            وميزانيتهم.

        </p>

    </div>

</section>


<section class="services-section">

    <div class="container">

        <div class="section-title">

            <span>
                خدماتنا
            </span>

            <h2>
                ماذا نقدم لك؟
            </h2>

        </div>


        <div class="services-grid">

            <div class="service-card">

                <div class="service-icon">
                    🏠
                </div>

                <h3>
                    بيع العقارات
                </h3>

                <p>
                    نساعدك في تسويق وبيع عقارك
                    والوصول إلى العملاء المناسبين.
                </p>

            </div>


            <div class="service-card">

                <div class="service-icon">
                    🔑
                </div>

                <h3>
                    شراء العقارات
                </h3>

                <p>
                    نساعدك في العثور على العقار
                    المناسب لاحتياجاتك.
                </p>

            </div>


            <div class="service-card">

                <div class="service-icon">
                    🏢
                </div>

                <h3>
                    تأجير العقارات
                </h3>

                <p>
                    خيارات متنوعة من العقارات
                    السكنية والتجارية.
                </p>

            </div>

        </div>

    </div>

</section>


<section class="cta-section">

    <div class="container">

        <h2>
            تبحث عن عقار مناسب؟
        </h2>

        <p>
            تصفح العقارات المتاحة واكتشف
            ما يناسبك.
        </p>

        <a href="properties.php"
           class="btn btn-primary">

            مشاهدة العقارات

        </a>

    </div>

</section>


<?php

include "includes/footer.php";

?>