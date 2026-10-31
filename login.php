
<?php

session_start();

require_once "config/database.php";

$page_title = "تسجيل الدخول | البوصلة العقارية";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {

        $error = "يرجى إدخال اسم المستخدم وكلمة المرور.";

    } else {

        try {

            $stmt = $conn->prepare("
                SELECT *
                FROM users
                WHERE username = ?
                LIMIT 1
            ");

            $stmt->execute([$username]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user["password"])) {

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["username"] = $user["username"];
                $_SESSION["full_name"] = $user["full_name"];
                $_SESSION["role"] = $user["role"];

                header("Location: admin/index.php");
                exit;

            } else {

                $error = "اسم المستخدم أو كلمة المرور غير صحيحة.";

            }

        } catch (PDOException $e) {

            $error = "حدث خطأ أثناء تسجيل الدخول.";

        }

    }
}

include "includes/header.php";

?>

<section class="login-section">

    <div class="login-container">

        <div class="login-card">

            <div class="login-header">

                <div class="login-icon">
                    🔐
                </div>

                <h1>
                    تسجيل الدخول
                </h1>

                <p>
                    الدخول إلى لوحة تحكم البوصلة العقارية
                </p>

            </div>


            <?php if ($error): ?>

                <div class="login-error">
                    <?= htmlspecialchars($error) ?>
                </div>

            <?php endif; ?>


            <form method="POST" action="">

                <div class="form-group">

                    <label for="username">
                        اسم المستخدم
                    </label>

                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="أدخل اسم المستخدم"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="password">
                        كلمة المرور
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="أدخل كلمة المرور"
                        required
                    >

                </div>


                <button
                    type="submit"
                    class="btn btn-primary login-submit"
                >
                    تسجيل الدخول
                </button>

            </form>


            <div class="login-back">

                <a href="index.php">
                    ← العودة إلى الموقع
                </a>

            </div>

        </div>

    </div>

</section>


<?php

include "includes/footer.php";

?>