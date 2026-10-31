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
| الحصول على البيانات
|--------------------------------------------------------------------------
*/

$image_id = filter_input(
    INPUT_GET,
    "id",
    FILTER_VALIDATE_INT
);

$property_id = filter_input(
    INPUT_GET,
    "property_id",
    FILTER_VALIDATE_INT
);


if (!$image_id || !$property_id) {

    header("Location: properties.php");
    exit;

}


/*
|--------------------------------------------------------------------------
| جلب بيانات الصورة
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT *
    FROM property_images
    WHERE id = ?
    AND property_id = ?
    LIMIT 1
");

$stmt->execute([
    $image_id,
    $property_id
]);

$image = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$image) {

    header(
        "Location: edit_property.php?id=" . $property_id
    );

    exit;

}


/*
|--------------------------------------------------------------------------
| حذف الملف من المجلد
|--------------------------------------------------------------------------
*/

$imagePath =
    "../assets/images/properties/"
    . $image["image"];


if (
    file_exists($imagePath)
) {

    unlink($imagePath);

}


/*
|--------------------------------------------------------------------------
| حذف السجل من قاعدة البيانات
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    DELETE FROM property_images
    WHERE id = ?
    AND property_id = ?
");

$stmt->execute([
    $image_id,
    $property_id
]);


/*
|--------------------------------------------------------------------------
| العودة إلى صفحة تعديل العقار
|--------------------------------------------------------------------------
*/

header(
    "Location: edit_property.php?id="
    . $property_id
);

exit;

?>