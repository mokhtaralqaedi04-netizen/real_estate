<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once "../config/database.php";

$page_title = "إضافة عقار";

$error = "";


// ============================================================
// إعدادات رفع الصور
// ============================================================

$upload_dir = "../assets/images/properties/";


// إنشاء مجلد الصور إذا لم يكن موجودًا
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}


// ============================================================
// معالجة النموذج
// ============================================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $property_type = trim($_POST["property_type"] ?? "");
    $purpose = trim($_POST["purpose"] ?? "");
    $price = trim($_POST["price"] ?? "");
    $city = trim($_POST["city"] ?? "");
    $address = trim($_POST["address"] ?? "");
    $area = trim($_POST["area"] ?? "");
    $bedrooms = trim($_POST["bedrooms"] ?? "0");
    $bathrooms = trim($_POST["bathrooms"] ?? "0");
    $status = trim($_POST["status"] ?? "متاح");


    // ========================================================
    // التحقق من البيانات الأساسية
    // ========================================================

    if (
        $title === "" ||
        $property_type === "" ||
        $purpose === "" ||
        $price === "" ||
        $city === ""
    ) {

        $error = "يرجى تعبئة جميع الحقول المطلوبة.";

    } else {

        try {

            // ==================================================
            // الصورة الرئيسية
            // ==================================================

            $main_image = null;


            if (
                isset($_FILES["main_image"]) &&
                $_FILES["main_image"]["error"] !== UPLOAD_ERR_NO_FILE
            ) {

                if ($_FILES["main_image"]["error"] !== UPLOAD_ERR_OK) {
                    throw new Exception("حدث خطأ أثناء رفع الصورة الرئيسية.");
                }


                // الحد الأقصى 5 ميجابايت
                if ($_FILES["main_image"]["size"] > 5 * 1024 * 1024) {
                    throw new Exception("حجم الصورة الرئيسية يجب ألا يتجاوز 5 ميجابايت.");
                }


                $allowed_extensions = [
                    "jpg",
                    "jpeg",
                    "png",
                    "webp"
                ];


                $extension = strtolower(
                    pathinfo(
                        $_FILES["main_image"]["name"],
                        PATHINFO_EXTENSION
                    )
                );


                if (!in_array($extension, $allowed_extensions)) {
                    throw new Exception(
                        "صيغة الصورة الرئيسية غير مدعومة. استخدم JPG أو JPEG أو PNG أو WEBP."
                    );
                }


                $main_image = uniqid("property_", true) . "." . $extension;


                $main_image_path = $upload_dir . $main_image;


                if (
                    !move_uploaded_file(
                        $_FILES["main_image"]["tmp_name"],
                        $main_image_path
                    )
                ) {

                    throw new Exception("تعذر حفظ الصورة الرئيسية.");
                }
            }


            // ==================================================
            // إضافة العقار إلى قاعدة البيانات
            // ==================================================

            $stmt = $conn->prepare("
                INSERT INTO properties
                (
                    title,
                    description,
                    property_type,
                    purpose,
                    price,
                    city,
                    address,
                    area,
                    bedrooms,
                    bathrooms,
                    status,
                    main_image
                )
                VALUES
                (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                )
            ");


            $stmt->execute([
                $title,
                $description,
                $property_type,
                $purpose,
                $price,
                $city,
                $address,
                $area !== "" ? $area : null,
                $bedrooms !== "" ? $bedrooms : 0,
                $bathrooms !== "" ? $bathrooms : 0,
                $status,
                $main_image
            ]);


            // الحصول على رقم العقار الجديد
            $property_id = $conn->lastInsertId();


            // ==================================================
            // رفع الصور الإضافية
            // ==================================================

            if (
                isset($_FILES["property_images"]) &&
                isset($_FILES["property_images"]["name"]) &&
                is_array($_FILES["property_images"]["name"])
            ) {

                $allowed_extensions = [
                    "jpg",
                    "jpeg",
                    "png",
                    "webp"
                ];


                $total_images = count(
                    $_FILES["property_images"]["name"]
                );


                for ($i = 0; $i < $total_images; $i++) {

                    // تجاهل الملفات التي لم يتم اختيارها
                    if (
                        $_FILES["property_images"]["error"][$i]
                        === UPLOAD_ERR_NO_FILE
                    ) {
                        continue;
                    }


                    // تجاهل الصورة إذا حدث خطأ في رفعها
                    if (
                        $_FILES["property_images"]["error"][$i]
                        !== UPLOAD_ERR_OK
                    ) {
                        continue;
                    }


                    // الحد الأقصى لكل صورة 5 ميجابايت
                    if (
                        $_FILES["property_images"]["size"][$i]
                        > 5 * 1024 * 1024
                    ) {
                        continue;
                    }


                    $extension = strtolower(
                        pathinfo(
                            $_FILES["property_images"]["name"][$i],
                            PATHINFO_EXTENSION
                        )
                    );


                    if (
                        !in_array(
                            $extension,
                            $allowed_extensions
                        )
                    ) {
                        continue;
                    }


                    $image_name =
                        uniqid("property_gallery_", true)
                        . "."
                        . $extension;


                    $image_path =
                        $upload_dir . $image_name;


                    if (
                        move_uploaded_file(
                            $_FILES["property_images"]["tmp_name"][$i],
                            $image_path
                        )
                    ) {

                        // حفظ الصورة في جدول property_images

                        $image_stmt = $conn->prepare("
                            INSERT INTO property_images
                            (
                                property_id,
                                image
                            )
                            VALUES
                            (?, ?)
                        ");


                        $image_stmt->execute([
                            $property_id,
                            $image_name
                        ]);
                    }
                }
            }


            // ==================================================
            // العودة إلى قائمة العقارات
            // ==================================================

            header(
                "Location: properties.php?success=1"
            );

            exit;


        } catch (Exception $e) {

            $error = $e->getMessage();

        } catch (PDOException $e) {

            $error = "حدث خطأ أثناء حفظ العقار في قاعدة البيانات.";

        }
    }
}

?>

<!DOCTYPE html>

<html lang="ar" dir="rtl">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?= htmlspecialchars($page_title) ?>
        | البوصلة العقارية
    </title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

</head>


<body>


<header class="admin-header">

    <div class="container">

        <div class="admin-navbar">

            <div>

                <h1>
                    البوصلة العقارية
                </h1>

                <span>
                    لوحة التحكم
                </span>

            </div>


            <div class="admin-user">

                مرحبًا،
                <?= htmlspecialchars($_SESSION["full_name"]) ?>

                <a href="logout.php">
                    تسجيل الخروج
                </a>

            </div>

        </div>

    </div>

</header>



<main class="admin-main">

    <div class="container">


        <div class="admin-title">

            <div>

                <h2>
                    إضافة عقار جديد
                </h2>

                <p>
                    أضف بيانات العقار والصور الخاصة به.
                </p>

            </div>


            <a
                href="properties.php"
                class="btn btn-outline"
            >
                ← العودة للعقارات
            </a>

        </div>



        <?php if ($error !== ""): ?>

            <div class="form-alert error">

                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>



        <div class="property-form-card">


            <form
                method="POST"
                enctype="multipart/form-data"
            >


                <!-- ================================================= -->
                <!-- المعلومات الأساسية -->
                <!-- ================================================= -->

                <div class="form-section">

                    <h3>
                        المعلومات الأساسية
                    </h3>


                    <div class="form-grid">


                        <div class="form-group">

                            <label>
                                اسم العقار *
                            </label>

                            <input
                                type="text"
                                name="title"
                                placeholder="مثال: فيلا فاخرة في صنعاء"
                                value="<?= htmlspecialchars($_POST["title"] ?? "") ?>"
                                required
                            >

                        </div>



                        <div class="form-group">

                            <label>
                                نوع العقار *
                            </label>

                            <select
                                name="property_type"
                                required
                            >

                                <option value="">
                                    اختر نوع العقار
                                </option>

                                <option
                                    value="فيلا"
                                    <?= (($_POST["property_type"] ?? "") === "فيلا") ? "selected" : "" ?>
                                >
                                    فيلا
                                </option>

                                <option
                                    value="شقة"
                                    <?= (($_POST["property_type"] ?? "") === "شقة") ? "selected" : "" ?>
                                >
                                    شقة
                                </option>

                                <option
                                    value="أرض"
                                    <?= (($_POST["property_type"] ?? "") === "أرض") ? "selected" : "" ?>
                                >
                                    أرض
                                </option>

                                <option
                                    value="مكتب"
                                    <?= (($_POST["property_type"] ?? "") === "مكتب") ? "selected" : "" ?>
                                >
                                    مكتب
                                </option>

                                <option
                                    value="محل تجاري"
                                    <?= (($_POST["property_type"] ?? "") === "محل تجاري") ? "selected" : "" ?>
                                >
                                    محل تجاري
                                </option>

                                <option
                                    value="عمارة"
                                    <?= (($_POST["property_type"] ?? "") === "عمارة") ? "selected" : "" ?>
                                >
                                    عمارة
                                </option>

                                <option
                                    value="مزرعة"
                                    <?= (($_POST["property_type"] ?? "") === "مزرعة") ? "selected" : "" ?>
                                >
                                    مزرعة
                                </option>

                            </select>

                        </div>



                        <div class="form-group">

                            <label>
                                الغرض *
                            </label>

                            <select
                                name="purpose"
                                required
                            >

                                <option value="">
                                    اختر الغرض
                                </option>

                                <option
                                    value="للبيع"
                                    <?= (($_POST["purpose"] ?? "") === "للبيع") ? "selected" : "" ?>
                                >
                                    للبيع
                                </option>

                                <option
                                    value="للإيجار"
                                    <?= (($_POST["purpose"] ?? "") === "للإيجار") ? "selected" : "" ?>
                                >
                                    للإيجار
                                </option>

                            </select>

                        </div>



                        <div class="form-group">

                            <label>
                                السعر *
                            </label>

                            <input
                                type="number"
                                name="price"
                                min="0"
                                step="0.01"
                                placeholder="مثال: 55000000"
                                value="<?= htmlspecialchars($_POST["price"] ?? "") ?>"
                                required
                            >

                        </div>



                        <div class="form-group">

                            <label>
                                المدينة *
                            </label>

                            <input
                                type="text"
                                name="city"
                                placeholder="مثال: صنعاء"
                                value="<?= htmlspecialchars($_POST["city"] ?? "") ?>"
                                required
                            >

                        </div>



                        <div class="form-group">

                            <label>
                                العنوان
                            </label>

                            <input
                                type="text"
                                name="address"
                                placeholder="مثال: شارع حدة"
                                value="<?= htmlspecialchars($_POST["address"] ?? "") ?>"
                            >

                        </div>



                        <div class="form-group">

                            <label>
                                المساحة م²
                            </label>

                            <input
                                type="number"
                                name="area"
                                min="0"
                                step="0.01"
                                placeholder="مثال: 350"
                                value="<?= htmlspecialchars($_POST["area"] ?? "") ?>"
                            >

                        </div>



                        <div class="form-group">

                            <label>
                                غرف النوم
                            </label>

                            <input
                                type="number"
                                name="bedrooms"
                                min="0"
                                value="<?= htmlspecialchars($_POST["bedrooms"] ?? "0") ?>"
                            >

                        </div>



                        <div class="form-group">

                            <label>
                                دورات المياه
                            </label>

                            <input
                                type="number"
                                name="bathrooms"
                                min="0"
                                value="<?= htmlspecialchars($_POST["bathrooms"] ?? "0") ?>"
                            >

                        </div>



                        <div class="form-group">

                            <label>
                                الحالة
                            </label>

                            <select name="status">

                                <option
                                    value="متاح"
                                    <?= (($_POST["status"] ?? "متاح") === "متاح") ? "selected" : "" ?>
                                >
                                    متاح
                                </option>

                                <option
                                    value="مباع"
                                    <?= (($_POST["status"] ?? "") === "مباع") ? "selected" : "" ?>
                                >
                                    مباع
                                </option>

                                <option
                                    value="مؤجر"
                                    <?= (($_POST["status"] ?? "") === "مؤجر") ? "selected" : "" ?>
                                >
                                    مؤجر
                                </option>

                                <option
                                    value="غير متاح"
                                    <?= (($_POST["status"] ?? "") === "غير متاح") ? "selected" : "" ?>
                                >
                                    غير متاح
                                </option>

                            </select>

                        </div>

                    </div>

                </div>



                <!-- ================================================= -->
                <!-- وصف العقار -->
                <!-- ================================================= -->

                <div class="form-section">

                    <h3>
                        وصف العقار
                    </h3>


                    <div class="form-group">

                        <label>
                            الوصف
                        </label>

                        <textarea
                            name="description"
                            rows="6"
                            placeholder="اكتب وصفًا تفصيليًا للعقار..."
                        ><?= htmlspecialchars($_POST["description"] ?? "") ?></textarea>

                    </div>

                </div>



                <!-- ================================================= -->
                <!-- الصور -->
                <!-- ================================================= -->

                <div class="form-section">

                    <h3>
                        صور العقار
                    </h3>


                    <div class="form-group">

                        <label>
                            الصورة الرئيسية
                        </label>

                        <input
                            type="file"
                            name="main_image"
                            accept=".jpg,.jpeg,.png,.webp"
                        >

                        <small>
                            الصيغ المسموحة: JPG, JPEG, PNG, WEBP — الحد الأقصى 5 ميجابايت.
                        </small>

                    </div>



                    <div class="form-group">

                        <label>
                            صور إضافية للعقار
                        </label>

                        <input
                            type="file"
                            name="property_images[]"
                            accept=".jpg,.jpeg,.png,.webp"
                            multiple
                        >

                        <small>
                            يمكنك اختيار عدة صور في نفس الوقت.
                            الحد الأقصى لكل صورة 5 ميجابايت.
                        </small>

                    </div>

                </div>



                <!-- ================================================= -->
                <!-- أزرار النموذج -->
                <!-- ================================================= -->

                <div class="form-buttons">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        💾 حفظ العقار
                    </button>


                    <a
                        href="properties.php"
                        class="btn btn-outline"
                    >
                        إلغاء
                    </a>

                </div>


            </form>

        </div>

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