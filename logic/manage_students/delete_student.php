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

$id = $_GET['id'] ?? null;
if (!$id) {
    die("ID sinh viên không hợp lệ.");
}

// Xóa vai trò sinh viên trong bảng UsersToRoles
$deleteRoleSql = "DELETE FROM UsersToRoles WHERE UserId = ?";
$deleteRoleStmt = $conn->prepare($deleteRoleSql);
$deleteRoleStmt->bind_param("i", $id);

if ($deleteRoleStmt->execute()) {
    // Sau khi xóa vai trò, xóa sinh viên trong bảng Users
    $sql = "DELETE FROM Users WHERE Id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: ../../views/afterLogin/academic_staff_dashboard.php?message=Xoá sinh viên thành công");
        exit();
    } else {
        echo "Lỗi khi xoá sinh viên: " . $stmt->error;
    }
} else {
    echo "Lỗi khi xoá vai trò: " . $deleteRoleStmt->error;
}

?>