<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <form action="../../logic/auth/register_process.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username" required>
            <label for="password">Password:</label>
            <input type="password" name="password" required>
            <label for="email">Email:</label>
            <input type="email" name="email" required>
            <label for="fullname">Fullname:</label>
            <input type="text" name="fullname" required>
            <label for="gender">Gender:</label>
            <select name="gender" required>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
            </select>
            <label for="birthyear">Birthyear:</label>
            <input type="number" name="birthyear" min="1900" max="2024" required>
            <label for="classid">Class Code:</label>
            <input type="number" name="classid">
            <label for="role">Role:</label>
            <select name="role" required>
                <option value="student">Student</option>
                <option value="teacher">Teacher</option>
                <option value="admin">Admin</option>
                <option value="academic_staff">Academic Staff</option>
            </select>
            <input type="submit" value="Register">
        </form>
        <p>Have an account? <a href="login.php">Login here</a></p>

        <!-- Back Button -->
        <button onclick="history.back()" class="back-button">Back</button>
    </div>
</body>
</html>
