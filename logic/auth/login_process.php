<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "StudentManagement";

// Kết nối đến cơ sở dữ liệu
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy dữ liệu từ form
$username = $_POST['username'];
$password = $_POST['password'];
$selectedRole = $_POST['role']; // Vai trò được chọn từ form

// Kiểm tra thông tin đăng nhập và vai trò
$sql = "SELECT Users.Id, Users.Password, Roles.Name AS RoleName
        FROM Users
        JOIN UsersToRoles ON Users.Id = UsersToRoles.UserId
        JOIN Roles ON UsersToRoles.RoleId = Roles.Id
        WHERE Users.Username = ? AND Roles.Name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $selectedRole);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    // Kiểm tra mật khẩu
    if (password_verify($password, $user['Password'])) {
        // Lưu thông tin người dùng vào session
        $_SESSION['user_id'] = $user['Id'];
        $_SESSION['role'] = $user['RoleName'];

        // Chuyển hướng dựa trên vai trò
        switch ($user['RoleName']) {
            case 'student':
                header("Location: ../../views/afterLogin/student_dashboard.php");
                break;
            case 'teacher':
                header("Location: ../../views/afterLogin/teacher_dashboard.php");
                break;
            case 'admin':
                header("Location: ../../views/afterLogin/admin_dashboard.php");
                break;
            case 'academic_staff':
                header("Location: ../../views/afterLogin/academic_staff_dashboard.php");
                break;
            default:
                echo "Vai trò không xác định.";
        }
        exit();
    } else {
        echo "Sai mật khẩu.";
    }
} else {
    echo "Tên đăng nhập hoặc vai trò không hợp lệ.";
}

$stmt->close();
$conn->close();
?>
