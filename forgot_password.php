<?php
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $token = bin2hex(random_bytes(16));
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $user = $res->fetch_assoc();
        $user_id = $user['id'];

        $conn->query("DELETE FROM password_resets WHERE user_id = $user_id");

        $stmt = $conn->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $token, $expires);
        $stmt->execute();

        echo "Password reset link (simulated): ";
        echo "<a href='edit_password.php?token=$token'>Click here to reset password</a>";
    } else {
        echo "No user found with that email.";
    }
}
?>

<h2>Forgot Password</h2>
<form method="POST">
    Enter your email: <input type="email" name="email" required><br><br>
    <button type="submit">Request Reset</button>
</form>
