<?php
require 'db.php';
session_start();

// Redirect if already logged in
if (isset($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
}

$error = ''; // prevent undefined variable warning

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // login success
        $_SESSION['user'] = ['id' => $user['id'], 'username' => $user['username']];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid username or password';
    }
} 

require 'header.php';
?>

<div class="card">
    <h2>Login</h2>

    <?php if (!empty($error)): ?>
        <div class="flash"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="post" action="login.php">
        <div class="form-row">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button class="btn" type="submit">Login</button>
        </div>
    </form>
</div>

<?php require 'footer.php'; ?>
