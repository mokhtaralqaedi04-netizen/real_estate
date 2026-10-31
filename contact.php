<?php

require_once "config/database.php";

$page_title = "تواصل معنا | البوصلة العقارية";

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $message = trim($_POST["message"] ?? "");

    if ($name === "" || $phone === "" || $message === "") {

        $error = "يرجى تعبئة جميع الحقول المطلوبة.";

    } else {

        try {

            $stmt = $conn->prepare("
                INSERT INTO messages
                (name, phone, email, message)
                VALUES (?, ?, ?, ?)
            ");

            $stmt->execute([
                $name,
                $phone,
                $email,
                $message
            ]);

            $success = "تم إرسال رسالتك بنجاح، وسنتواصل معك قريبًا.";

        } catch (PDOException $e) {

            $error = "حدث خطأ أثناء إرسال الرسالة.";
        }
    }
}

include "includes/header.php";
?>

<section class="page-header">
    <div class="container">

        <h1>تواصل معنا</h1>

        <p>
            يسعدنا تواصلك معنا والاستفسار عن أي عقار أو خدمة.
        </p>

    </div>
</section>


<section class="contact-section">

    <div class="container">

        <div class="contact-grid">

            <div class="contact-info">

                <h2>تواصل مع البوصلة العقارية</h2>

                <p>
                    فريقنا جاهز لمساعدتك في العثور على العقار المناسب
                    للبيع أو الشراء أو التأجير.
                </p>

                <div class="contact-item">
                    <strong>📞 الهاتف</strong>
                    <span>+967 XXX XXX XXX</span>
                </div>

                <div class="contact-item">
                    <strong>📧 البريد الإلكتروني</strong>
                    <span>info@example.com</span>
                </div>

                <div class="contact-item">
                    <strong>📍 العنوان</strong>
                    <span>صنعاء - اليمن</span>
                </div>

            </div>


            <div class="contact-form-card">

                <h2>أرسل لنا رسالة</h2>

                <?php if ($success): ?>

                    <div class="form-alert success">
                        <?= htmlspecialchars($success) ?>
                    </div>

                <?php endif; ?>


                <?php if ($error): ?>

                    <div class="form-alert error">
                        <?= htmlspecialchars($error) ?>
                    </div>

                <?php endif; ?>


                <form method="POST">

                    <div class="form-group">

                        <label for="name">
                            الاسم الكامل *
                        </label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            required
                            value="<?= htmlspecialchars($_POST["name"] ?? "") ?>"
                        >

                    </div>


                    <div class="form-group">

                        <label for="phone">
                            رقم الهاتف *
                        </label>

                        <input
                            type="text"
                            id="phone"
                            name="phone"
                            required
                            value="<?= htmlspecialchars($_POST["phone"] ?? "") ?>"
                        >

                    </div>


                    <div class="form-group">

                        <label for="email">
                            البريد الإلكتروني
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="<?= htmlspecialchars($_POST["email"] ?? "") ?>"
                        >

                    </div>


                    <div class="form-group">

                        <label for="message">
                            الرسالة *
                        </label>

                        <textarea
                            id="message"
                            name="message"
                            rows="6"
                            required
                        ><?= htmlspecialchars($_POST["message"] ?? "") ?></textarea>

                    </div>


                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        إرسال الرسالة
                    </button>

                </form>

            </div>

        </div>

    </div>

</section>


<?php include "includes/footer.php"; ?>