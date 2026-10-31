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
| الحصول على رقم العقار
|--------------------------------------------------------------------------
*/

$id = filter_input(
    INPUT_GET,
    "id",
    FILTER_VALIDATE_INT
);

if (!$id) {

    header("Location: properties.php");
    exit;

}


/*
|--------------------------------------------------------------------------
| جلب بيانات العقار
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT *
    FROM properties
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([$id]);

$property = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$property) {

    die("العقار غير موجود.");

}


$error = "";


/*
|--------------------------------------------------------------------------
| معالجة التعديل
|--------------------------------------------------------------------------
*/

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


    /*
    |--------------------------------------------------------------------------
    | التحقق من البيانات
    |--------------------------------------------------------------------------
    */

    if (
        $title === "" ||
        $property_type === "" ||
        $purpose === "" ||
        $price === "" ||
        $city === ""
    ) {

        $error = "يرجى تعبئة جميع الحقول المطلوبة.";

    } elseif (!is_numeric($price)) {

        $error = "يرجى إدخال سعر صحيح.";

    }


    /*
    |--------------------------------------------------------------------------
    | الصورة الرئيسية الحالية
    |--------------------------------------------------------------------------
    */

    $imageName = $property["main_image"];


    /*
    |--------------------------------------------------------------------------
    | رفع صورة رئيسية جديدة
    |--------------------------------------------------------------------------
    */

    if (
        $error === "" &&
        isset($_FILES["main_image"]) &&
        $_FILES["main_image"]["error"] === UPLOAD_ERR_OK
    ) {

        $file = $_FILES["main_image"];

        $allowedExtensions = [
            "jpg",
            "jpeg",
            "png",
            "webp"
        ];

        $extension = strtolower(
            pathinfo(
                $file["name"],
                PATHINFO_EXTENSION
            )
        );


        if (!in_array($extension, $allowedExtensions)) {

            $error =
                "نوع الصورة غير مسموح. استخدم JPG أو JPEG أو PNG أو WEBP.";

        } elseif ($file["size"] > 5 * 1024 * 1024) {

            $error =
                "حجم الصورة يجب ألا يتجاوز 5 ميجابايت.";

        } else {

            $uploadDirectory =
                "../assets/images/properties/";


            if (!is_dir($uploadDirectory)) {

                mkdir(
                    $uploadDirectory,
                    0777,
                    true
                );

            }


            $newImageName =
                uniqid("property_", true)
                . "."
                . $extension;


            $destination =
                $uploadDirectory
                . $newImageName;


            if (
                move_uploaded_file(
                    $file["tmp_name"],
                    $destination
                )
            ) {

                /*
                |----------------------------------------------------------
                | حذف الصورة الرئيسية القديمة
                |----------------------------------------------------------
                */

                if (
                    !empty($property["main_image"])
                    &&
                    file_exists(
                        $uploadDirectory
                        . $property["main_image"]
                    )
                ) {

                    unlink(
                        $uploadDirectory
                        . $property["main_image"]
                    );

                }


                $imageName = $newImageName;

            } else {

                $error =
                    "فشل رفع الصورة الجديدة.";

            }

        }

    }


    /*
    |--------------------------------------------------------------------------
    | تحديث بيانات العقار
    |--------------------------------------------------------------------------
    */

    if ($error === "") {

        try {

            $stmt = $conn->prepare("
                UPDATE properties

                SET

                    title = ?,
                    description = ?,
                    property_type = ?,
                    purpose = ?,
                    price = ?,
                    city = ?,
                    address = ?,
                    area = ?,
                    bedrooms = ?,
                    bathrooms = ?,
                    status = ?,
                    main_image = ?

                WHERE id = ?
            ");


            $stmt->execute([

                $title,
                $description,
                $property_type,
                $purpose,
                $price,
                $city,
                $address,
                $area,
                $bedrooms,
                $bathrooms,
                $status,
                $imageName,
                $id

            ]);


            /*
            |--------------------------------------------------------------------------
            | إضافة صور جديدة
            |--------------------------------------------------------------------------
            */

            if (
                isset($_FILES["property_images"])
                &&
                !empty($_FILES["property_images"]["name"][0])
            ) {

                $uploadDirectory =
                    "../assets/images/properties/";


                if (!is_dir($uploadDirectory)) {

                    mkdir(
                        $uploadDirectory,
                        0777,
                        true
                    );

                }


                $allowedExtensions = [
                    "jpg",
                    "jpeg",
                    "png",
                    "webp"
                ];


                $filesCount =
                    count($_FILES["property_images"]["name"]);


                for (
                    $i = 0;
                    $i < $filesCount;
                    $i++
                ) {

                    if (
                        $_FILES["property_images"]["error"][$i]
                        !== UPLOAD_ERR_OK
                    ) {
                        continue;
                    }


                    $fileName =
                        $_FILES["property_images"]["name"][$i];

                    $tmpName =
                        $_FILES["property_images"]["tmp_name"][$i];

                    $fileSize =
                        $_FILES["property_images"]["size"][$i];


                    $extension = strtolower(
                        pathinfo(
                            $fileName,
                            PATHINFO_EXTENSION
                        )
                    );


                    /*
                    |------------------------------------------------------
                    | التحقق من نوع الصورة
                    |------------------------------------------------------
                    */

                    if (
                        !in_array(
                            $extension,
                            $allowedExtensions
                        )
                    ) {

                        continue;

                    }


                    /*
                    |------------------------------------------------------
                    | التحقق من الحجم
                    |------------------------------------------------------
                    */

                    if ($fileSize > 5 * 1024 * 1024) {

                        continue;

                    }


                    /*
                    |------------------------------------------------------
                    | إنشاء اسم جديد
                    |------------------------------------------------------
                    */

                    $newImageName =
                        uniqid("gallery_", true)
                        . "."
                        . $extension;


                    $destination =
                        $uploadDirectory
                        . $newImageName;


                    /*
                    |------------------------------------------------------
                    | نقل الصورة
                    |------------------------------------------------------
                    */

                    if (
                        move_uploaded_file(
                            $tmpName,
                            $destination
                        )
                    ) {

                        /*
                        |--------------------------------------------------
                        | حفظ الصورة في قاعدة البيانات
                        |--------------------------------------------------
                        */

                        $imageStmt = $conn->prepare("
                            INSERT INTO property_images
                            (
                                property_id,
                                image
                            )
                            VALUES
                            (
                                ?,
                                ?
                            )
                        ");


                        $imageStmt->execute([

                            $id,
                            $newImageName

                        ]);

                    }

                }

            }


            /*
            |--------------------------------------------------------------------------
            | العودة إلى قائمة العقارات
            |--------------------------------------------------------------------------
            */

            header(
                "Location: properties.php?updated=1"
            );

            exit;


        } catch (PDOException $e) {

            $error =
                "حدث خطأ أثناء تحديث العقار.";

        }

    }

}


/*
|--------------------------------------------------------------------------
| جلب الصور الإضافية للعقار
|--------------------------------------------------------------------------
*/

$imageStmt = $conn->prepare("
    SELECT *
    FROM property_images
    WHERE property_id = ?
    ORDER BY id ASC
");

$imageStmt->execute([$id]);

$gallery_images =
    $imageStmt->fetchAll(PDO::FETCH_ASSOC);

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
        تعديل العقار | البوصلة العقارية
    </title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

</head>


<body>


<header class="admin-header">

    <div class="container admin-navbar">

        <div>

            <h2>
                البوصلة العقارية
            </h2>

            <span>
                تعديل العقار
            </span>

        </div>


        <div class="admin-user">

            <span>

                مرحبًا،

                <?= htmlspecialchars(
                    $_SESSION["full_name"]
                ) ?>

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
                    تعديل العقار
                </h1>

                <p>
                    تعديل معلومات العقار وحفظ التغييرات
                </p>

            </div>

        </div>


        <?php if ($error !== ""): ?>

            <div class="form-alert error-alert">

                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>


        <div class="property-form-card">


            <form
                method="POST"
                enctype="multipart/form-data"
            >


                <!-- ==================================================
                     المعلومات الأساسية
                ================================================== -->

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
                                value="<?= htmlspecialchars(
                                    $property["title"]
                                ) ?>"
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
                                    اختر النوع
                                </option>

                                <?php

                                $types = [

                                    "فيلا",
                                    "شقة",
                                    "منزل",
                                    "أرض",
                                    "مكتب",
                                    "محل تجاري",
                                    "مبنى"

                                ];

                                foreach ($types as $type):

                                ?>

                                    <option
                                        value="<?= htmlspecialchars(
                                            $type
                                        ) ?>"
                                        <?= $property[
                                            "property_type"
                                        ] === $type
                                            ? "selected"
                                            : "" ?>
                                    >

                                        <?= htmlspecialchars($type) ?>

                                    </option>

                                <?php endforeach; ?>

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

                                <option
                                    value="للبيع"
                                    <?= $property["purpose"] === "للبيع"
                                        ? "selected"
                                        : "" ?>
                                >
                                    للبيع
                                </option>

                                <option
                                    value="للإيجار"
                                    <?= $property["purpose"] === "للإيجار"
                                        ? "selected"
                                        : "" ?>
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
                                value="<?= htmlspecialchars(
                                    $property["price"]
                                ) ?>"
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
                                value="<?= htmlspecialchars(
                                    $property["city"]
                                ) ?>"
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
                                value="<?= htmlspecialchars(
                                    $property["address"] ?? ""
                                ) ?>"
                            >

                        </div>


                    </div>

                </div>



                <!-- ==================================================
                     تفاصيل العقار
                ================================================== -->

                <div class="form-section">

                    <h3>
                        تفاصيل العقار
                    </h3>


                    <div class="form-grid">


                        <div class="form-group">

                            <label>
                                المساحة بالمتر المربع
                            </label>

                            <input
                                type="number"
                                name="area"
                                min="0"
                                step="0.01"
                                value="<?= htmlspecialchars(
                                    $property["area"] ?? ""
                                ) ?>"
                            >

                        </div>


                        <div class="form-group">

                            <label>
                                عدد غرف النوم
                            </label>

                            <input
                                type="number"
                                name="bedrooms"
                                min="0"
                                value="<?= htmlspecialchars(
                                    $property["bedrooms"] ?? "0"
                                ) ?>"
                            >

                        </div>


                        <div class="form-group">

                            <label>
                                عدد دورات المياه
                            </label>

                            <input
                                type="number"
                                name="bathrooms"
                                min="0"
                                value="<?= htmlspecialchars(
                                    $property["bathrooms"] ?? "0"
                                ) ?>"
                            >

                        </div>


                        <div class="form-group">

                            <label>
                                الحالة
                            </label>

                            <select name="status">

                                <?php

                                $statuses = [

                                    "متاح",
                                    "محجوز",
                                    "تم البيع",
                                    "مؤجر"

                                ];

                                foreach ($statuses as $status):

                                ?>

                                    <option
                                        value="<?= htmlspecialchars(
                                            $status
                                        ) ?>"
                                        <?= $property["status"] === $status
                                            ? "selected"
                                            : "" ?>
                                    >

                                        <?= htmlspecialchars($status) ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                    </div>


                    <div class="form-group">

                        <label>
                            وصف العقار
                        </label>

                        <textarea
                            name="description"
                            rows="6"
                        ><?= htmlspecialchars(
                            $property["description"] ?? ""
                        ) ?></textarea>

                    </div>

                </div>



                <!-- ==================================================
                     الصورة الرئيسية
                ================================================== -->

                <div class="form-section">

                    <h3>
                        الصورة الرئيسية
                    </h3>


                    <?php if (!empty($property["main_image"])): ?>

                        <div style="margin-bottom: 20px;">

                            <p>
                                الصورة الحالية:
                            </p>

                            <img
                                src="../assets/images/properties/<?= htmlspecialchars(
                                    $property["main_image"]
                                ) ?>"
                                style="
                                    width:220px;
                                    height:150px;
                                    object-fit:cover;
                                    border-radius:10px;
                                "
                                alt="الصورة الرئيسية للعقار"
                            >

                        </div>

                    <?php endif; ?>


                    <div class="form-group">

                        <label>
                            استبدال الصورة الرئيسية
                        </label>

                        <input
                            type="file"
                            name="main_image"
                            accept=".jpg,.jpeg,.png,.webp"
                        >

                        <small>
                            اترك الحقل فارغًا للاحتفاظ بالصورة الحالية.
                        </small>

                    </div>

                </div>



                <!-- ==================================================
                     الصور الإضافية الحالية
                ================================================== -->

                <div class="form-section">

                    <h3>
                        الصور الإضافية
                    </h3>


                    <?php if (!empty($gallery_images)): ?>

                        <div
                            style="
                                display:grid;
                                grid-template-columns:
                                repeat(auto-fill,minmax(180px,1fr));
                                gap:20px;
                                margin-bottom:25px;
                            "
                        >

                            <?php foreach ($gallery_images as $image): ?>

                                <div
                                    style="
                                        border:1px solid #ddd;
                                        border-radius:12px;
                                        padding:10px;
                                        background:#fff;
                                    "
                                >

                                    <img
                                        src="../assets/images/properties/<?= htmlspecialchars(
                                            $image["image"]
                                        ) ?>"
                                        alt="صورة إضافية"
                                        style="
                                            width:100%;
                                            height:140px;
                                            object-fit:cover;
                                            border-radius:8px;
                                            display:block;
                                        "
                                    >


                                    <a
                                        href="delete_property_image.php?id=<?= $image["id"] ?>&property_id=<?= $id ?>"
                                        class="delete-btn"
                                        style="
                                            display:block;
                                            text-align:center;
                                            margin-top:10px;
                                        "
                                        onclick="
                                            return confirm(
                                                'هل أنت متأكد من حذف هذه الصورة؟'
                                            );
                                        "
                                    >

                                        🗑 حذف الصورة

                                    </a>

                                </div>

                            <?php endforeach; ?>

                        </div>

                    <?php else: ?>

                        <p>
                            لا توجد صور إضافية لهذا العقار.
                        </p>

                    <?php endif; ?>


                    <!-- إضافة صور جديدة -->

                    <div class="form-group">

                        <label>
                            إضافة صور جديدة
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



                <!-- ==================================================
                     الأزرار
                ================================================== -->

                <div class="form-buttons">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        حفظ التعديلات
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

    <p>
        © 2026 البوصلة العقارية - لوحة التحكم
    </p>

</footer>


</body>

</html>