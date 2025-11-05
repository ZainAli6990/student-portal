<?php
require 'db.php';
session_start();

// Ensure user is logged in
if (empty($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// Validate request
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) {
    exit('Invalid request.');
}

$id = intval($_POST['id']);

// Fetch student record
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$student = $stmt->fetch();

if (!$student) {
    exit('Student not found.');
}

// Delete photo file if exists
if (!empty($student['photo'])) {
    $filePath = __DIR__ . '/uploads/' . basename($student['photo']);
    if (file_exists($filePath)) {
        @unlink($filePath); // safely delete photo
    }
}

// Delete student record from database
$stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
$stmt->execute([$id]);

// Flash message
$_SESSION['flash'] = 'Student deleted successfully!';

// Redirect to dashboard
header('Location: dashboard.php');
exit;
?>
