<?php
require 'db.php';
session_start();

// Check if user is logged in
if (empty($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// Get student ID
$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: dashboard.php');
    exit;
}

// Fetch existing student data
$stmt = $pdo->prepare('SELECT * FROM students WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$student = $stmt->fetch();

if (!$student) {
    $_SESSION['flash'] = 'Student not found.';
    header('Location: dashboard.php');
    exit;
}

// Update student record
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $course = trim($_POST['course'] ?? '');

    // Handle photo update
    $photoName = $student['photo'];
    if (!empty($_FILES['photo']['name'])) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            // Delete old photo if exists
            if (!empty($student['photo']) && file_exists('uploads/' . $student['photo'])) {
                unlink('uploads/' . $student['photo']);
            }

            // Save new photo
            $photoName = uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['photo']['tmp_name'], 'uploads/' . $photoName);
        }
    }

    // Update query
    $stmt = $pdo->prepare('UPDATE students SET name = ?, email = ?, course = ?, photo = ? WHERE id = ?');
    $stmt->execute([$name, $email, $course, $photoName, $id]);

    $_SESSION['flash'] = 'Student updated successfully!';
    header('Location: dashboard.php');
    exit;
}
?>

<?php include 'header.php'; ?>

<div class="container">
    <h2>Edit Student</h2>
    <form method="post" enctype="multipart/form-data">
        <label>Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($student['name']) ?>" required><br>

        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" required><br>

        <label>Course:</label>
        <input type="text" name="course" value="<?= htmlspecialchars($student['course']) ?>" required><br>

        <label>Photo:</label>
        <input type="file" name="photo" accept=".jpg,.jpeg,.png"><br>
        
        <?php if (!empty($student['photo'])): ?>
            <img src="uploads/<?= htmlspecialchars($student['photo']) ?>" width="100" height="100" alt="Student Photo"><br>
        <?php endif; ?>

        <button type="submit">Update Student</button>
    </form>
</div>

<?php include 'footer.php'; ?>
