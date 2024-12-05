<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form action="../../logic/auth/login_process.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username" required>
            <label for="password">Password:</label>
            <input type="password" name="password" required>
            <label for="role">Role:</label>
            <select name="role" required>
                <option value="student">Student</option>
                <option value="teacher">Teacher</option>
                <option value="admin">Admin</option>
                <option value="academic_staff">Academic Staff</option>
            </select>
            <input type="submit" value="Login">
        </form>
        <p>Need an account? <a href="register.php">Register here</a></p>

        <?php
        if (isset($_GET['message'])) {
            echo "<p class='success-message'>" . htmlspecialchars($_GET['message']) . "</p>";
        }
        ?>

        <!-- Back Button -->
        <button onclick="history.back()" class="back-button">Back</button>
    </div>
</body>
</html>
