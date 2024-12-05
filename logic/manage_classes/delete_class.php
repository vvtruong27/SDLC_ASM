<?php
// Kết nối đến cơ sở dữ liệu
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "StudentManagement";

$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Xóa lớp học
if (isset($_GET['id'])) {
    $classId = $_GET['id'];

    // Xóa tất cả các bản ghi trong ClassesToCourses liên quan đến lớp học này
    $sqlLink = "DELETE FROM ClassesToCourses WHERE ClassId = ?";
    $stmtLink = $conn->prepare($sqlLink);
    $stmtLink->bind_param("i", $classId);

    if ($stmtLink->execute()) {
        // Cập nhật các user có ClassId liên quan đến lớp bị xoá
        $sqlUpdateUsers = "UPDATE Users SET ClassId = NULL WHERE ClassId = ?";
        $stmtUpdateUsers = $conn->prepare($sqlUpdateUsers);
        $stmtUpdateUsers->bind_param("i", $classId);

        if ($stmtUpdateUsers->execute()) {
            // Xóa lớp học khỏi bảng Classes
            $sql = "DELETE FROM Classes WHERE Id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $classId);

            if ($stmt->execute()) {
                header("Location: ../../views/afterLogin/academic_staff_dashboard.php?message=Xoá lớp học thành công");
                exit();
            } else {
                echo "Lỗi khi xoá lớp học: " . $stmt->error;
            }
        } else {
            echo "Lỗi khi cập nhật các user liên quan: " . $stmtUpdateUsers->error;
        }
    } else {
        echo "Lỗi khi xoá liên kết trong ClassesToCourses: " . $stmtLink->error;
    }
}
?>
