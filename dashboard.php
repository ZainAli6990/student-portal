<?php
require 'db.php';
session_start();

// Ensure user is logged in
if (empty($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $course = trim($_POST['course'] ?? '');
    $photoName = null;

    // Create upload folder if not exists
    $uploadDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Handle photo upload
    if (!empty($_FILES['photo']['name'])) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $photoName = time() . '_' . basename($_FILES['photo']['name']);
            $target = $uploadDir . $photoName;

            if (!move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
                $photoName = null; // upload failed
            }
        }
    }

    // Validate fields before insert
    if ($name && $email && $course) {
        $stmt = $pdo->prepare("INSERT INTO students (name, email, course, photo) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $course, $photoName]);
        $_SESSION['flash'] = '✅ Student added successfully!';
    } else {
        $_SESSION['flash'] = '⚠️ Please fill in all fields correctly.';
    }

    header('Location: dashboard.php');
    exit;
}

// Fetch students
$students = $pdo->query('SELECT * FROM students ORDER BY id DESC')->fetchAll();

require 'header.php';
?>

<div class="card">
    <h2>Add Student</h2>

    <!-- Flash message (only show if not already shown in header.php) -->
    <?php if (!empty($_SESSION['flash'])): ?>
        <div class="flash"><?php echo htmlspecialchars($_SESSION['flash']); unset($_SESSION['flash']); ?></div>
    <?php endif; ?>

    <form method="post" action="" enctype="multipart/form-data">
        <input type="hidden" name="action" value="add">

        <div class="form-row">
            <input type="text" name="name" placeholder="Full name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="course" placeholder="Course" required>

            <!-- Custom file upload -->
            <label for="fileUpload" class="custom-file-upload">📸 Upload Image</label>
            <input id="fileUpload" type="file" name="photo" accept=".jpg,.jpeg,.png" required>
            <span id="fileName" class="file-name"></span>

            <button class="btn" type="submit">Add Student</button>
        </div>
    </form>
</div>

<div class="card">
    <h2>Students List</h2>
    <table>
        <thead>
            <tr>
                <th>Photo</th><th>Name</th><th>Email</th><th>Course</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($students): ?>
                <?php foreach ($students as $s): ?>
                    <tr>
                        <td>
                            <?php if ($s['photo']): ?>
                                <img class="thumb" src="uploads/<?php echo htmlspecialchars($s['photo']); ?>" alt="">
                            <?php else: ?>
                                <img class="thumb" src="https://via.placeholder.com/60?text=No+Photo" alt="">
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($s['name']); ?></td>
                        <td><?php echo htmlspecialchars($s['email']); ?></td>
                        <td><?php echo htmlspecialchars($s['course']); ?></td>
                        <td>
                            <a class="btn" href="edit.php?id=<?php echo $s['id']; ?>">Edit</a>
                            <form method="post" action="delete.php" style="display:inline-block" onsubmit="return confirm('Delete this student?');">
                                <input type="hidden" name="id" value="<?php echo $s['id']; ?>">
                                <button class="btn danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5">No students found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
// 👇 JS: Show selected file name under the button
document.getElementById('fileUpload').addEventListener('change', function() {
    const fileName = this.files[0] ? this.files[0].name : 'No file chosen';
    document.getElementById('fileName').textContent = '✅ ' + fileName;
});
</script>

</main> <!-- ✅ closing tag added -->
<?php require 'footer.php'; ?>
