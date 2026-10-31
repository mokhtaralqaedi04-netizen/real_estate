<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once "../config/database.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: messages.php");
    exit;
}

$stmt = $conn->prepare("
    UPDATE messages
    SET is_read = 1
    WHERE id = ?
");

$stmt->execute([$id]);

header("Location: messages.php");
exit;