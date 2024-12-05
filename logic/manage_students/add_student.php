<?php
// Kết nối đến cơ sở dữ liệu
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "StudentManagement";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Xử lý khi form được gửi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $fullname = $_POST['fullname'];
    $gender = $_POST['gender'];
    $birthyear = $_POST['birthyear'];
    $classid = $_POST['classid'];

    // Thêm sinh viên vào Users
    $sql = "INSERT INTO Users (Username, Password, Email, FullName, Gender, BirthYear, ClassId) 
VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $username, $password, $email, $fullname, $gender, $birthyear, $classid);

    if ($stmt->execute()) {
        // Lấy ID người dùng vừa thêm
        $userId = $conn->insert_id;

        // Thêm vai trò là sinh viên vào bảng UsersToRoles
        $roleIdQuery = "SELECT Id FROM Roles WHERE Name = 'student'";
        $roleIdResult = $conn->query($roleIdQuery);
        if ($roleIdResult->num_rows > 0) {
            $roleIdRow = $roleIdResult->fetch_assoc();
            $roleId = $roleIdRow['Id'];

            $addRoleSql = "INSERT INTO UsersToRoles (UserId, RoleId) VALUES (?, ?)";
            $addRoleStmt = $conn->prepare($addRoleSql);
            $addRoleStmt->bind_param("ii", $userId, $roleId);

            if ($addRoleStmt->execute()) {
                header("Location: ../../views/afterLogin/academic_staff_dashboard.php?message=Thêm sinh viên thành công");
                exit();
            } else {
                echo "Lỗi khi thêm vai trò: " . $addRoleStmt->error;
            }
        }
    } else {
        echo "Lỗi: " . $stmt->error;
    }

}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Thêm Sinh viên</title>
</head>

<body>
    <h2>Thêm Sinh viên</h2>
    <form method="POST" action="">
        <label for="username">Tên đăng nhập:</label>
        <input type="text" name="username" required><br>
        <label for="password">Mật khẩu:</label>
        <input type="password" name="password" required><br>
        <label for="email">Email:</label>
        <input type="email" name="email" required><br>
        <label for="fullname">Họ và tên:</label>
        <input type="text" name="fullname" required><br>
        <label for="gender">Giới tính:</label>
        <select name="gender" required>
            <option value="male">Nam</option>
            <option value="female">Nữ</option>
            <option value="other">Khác</option>
        </select><br>
        <label for="birthyear">Năm sinh:</label>
        <input type="number" name="birthyear" min="1900" max="2024" required><br>
        <label for="classid">Mã lớp:</label>
        <input type="number" name="classid"><br>
        <input type="submit" value="Thêm">
    </form>
    <p><a href="academic_staff_dashboard.php">Quay lại Dashboard</a></p>
</body>

</html>