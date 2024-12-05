<?php
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
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$email = $_POST['email'];
$fullname = $_POST['fullname'];
$gender = $_POST['gender'];
$birthyear = $_POST['birthyear'];
$classid = !empty($_POST['classid']) ? $_POST['classid'] : null;
$role = $_POST['role'];
error_log($role);

// Bắt đầu giao dịch để đảm bảo dữ liệu được thêm đúng vào cả hai bảng
$conn->begin_transaction();

try {
    // Chèn dữ liệu vào bảng Users
    $sql = "INSERT INTO Users (Username, Password, Email, FullName, Gender, BirthYear, ClassId)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    // Sử dụng 'i' hoặc 's' phụ thuộc vào kiểu dữ liệu
    if ($classid === null) {
        $stmt->bind_param("ssssssi", $username, $password, $email, $fullname, $gender, $birthyear, $classid);
    } else {
        $stmt->bind_param("ssssssi", $username, $password, $email, $fullname, $gender, $birthyear, $classid);
    }

    if (!$stmt->execute()) {
        throw new Exception("Lỗi khi đăng ký: " . $stmt->error);
    }

    // Lấy UserId mới tạo để thêm vai trò
    $userId = $conn->insert_id;

    // Chèn vai trò vào bảng UsersToRoles
    $sql = "INSERT INTO UsersToRoles (UserId, RoleId)
            SELECT ?, Id FROM Roles WHERE Name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $userId, $role);

    if (!$stmt->execute()) {
        throw new Exception("Lỗi khi thêm vai trò: " . $stmt->error);
    }

    // Hoàn tất giao dịch
    $conn->commit();
    header("Location: ../../views/beforeLogin/login.php?message=Đăng ký thành công!");
    exit(); // Dừng script sau khi chuyển hướng
} catch (Exception $e) {
    // Rollback nếu có lỗi
    $conn->rollback();
    echo $e->getMessage();
}

$stmt->close();
$conn->close();
?>
