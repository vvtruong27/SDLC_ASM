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

// Lấy thông tin môn học cần sửa
if (isset($_GET['id'])) {
    $courseId = $_GET['id'];
    $result = $conn->query("SELECT * FROM Courses WHERE Id = $courseId");

    if ($result->num_rows > 0) {
        $course = $result->fetch_assoc();
    } else {
        echo "Không tìm thấy môn học";
        exit();
    }
}

// Cập nhật thông tin môn học
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $credits = $_POST['credits'];
    $description = $_POST['description'];
    $classId = $_POST['class_id']; // Lớp học liên quan

    // Cập nhật bảng Courses
    $sql = "UPDATE Courses SET Name = ?, Credits = ?, Description = ? WHERE Id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisi", $name, $credits, $description, $courseId);

    if ($stmt->execute()) {
        // Cập nhật bảng ClassesToCourses
        $sqlLink = "UPDATE ClassesToCourses SET ClassId = ? WHERE CourseId = ?";
        $stmtLink = $conn->prepare($sqlLink);
        $stmtLink->bind_param("ii", $classId, $courseId);

        if ($stmtLink->execute()) {
            header("Location: ../../views/afterLogin/academic_staff_dashboard.php?message=Sửa môn học thành công");
            exit();
        } else {
            echo "Lỗi khi cập nhật ClassesToCourses: " . $stmtLink->error;
        }
    } else {
        echo "Lỗi khi cập nhật môn học: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sửa Môn học</title>
</head>
<body>
    <h2>Sửa Môn học</h2>
    <form method="POST" action="">
        <label for="name">Tên môn học:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($course['Name']); ?>" required><br>
        <label for="credits">Số tín chỉ:</label>
        <input type="number" name="credits" value="<?php echo htmlspecialchars($course['Credits']); ?>" min="1" required><br>
        <label for="description">Mô tả:</label>
        <textarea name="description" required><?php echo htmlspecialchars($course['Description']); ?></textarea><br>
        <label for="class_id">Mã lớp:</label>
        <input type="number" name="class_id" required><br>
        <input type="submit" value="Sửa Môn học">
    </form>
</body>
</html>
