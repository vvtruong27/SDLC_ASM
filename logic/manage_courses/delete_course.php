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

// Xóa môn học
if (isset($_GET['id'])) {
    $courseId = $_GET['id'];

    // Xóa liên kết trong ClassesToCourses trước
    $sqlLink = "DELETE FROM ClassesToCourses WHERE CourseId = ?";
    $stmtLink = $conn->prepare($sqlLink);
    $stmtLink->bind_param("i", $courseId);

    if ($stmtLink->execute()) {
        // Sau đó xóa môn học
        $sql = "DELETE FROM Courses WHERE Id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $courseId);

        if ($stmt->execute()) {
            header("Location: ../../views/afterLogin/academic_staff_dashboard.php?message=Xoá môn học thành công");
            exit();
        } else {
            echo "Lỗi khi xoá môn học: " . $stmt->error;
        }
    } else {
        echo "Lỗi khi xoá liên kết trong ClassesToCourses: " . $stmtLink->error;
    }
}
?>
