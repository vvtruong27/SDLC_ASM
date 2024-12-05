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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $credits = $_POST['credits'];
    $description = $_POST['description'];
    $classIds = $_POST['class_ids']; // Mảng ID của các lớp sẽ liên kết

    // Thêm khóa học mới vào bảng Courses
    $sql = "INSERT INTO Courses (Name, Credits, Description) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sis", $name, $credits, $description);

    if ($stmt->execute()) {
        $courseId = $conn->insert_id;

        // Thêm dữ liệu vào bảng ClassesToCourses
        foreach ($classIds as $classId) {
            $sqlClassCourse = "INSERT INTO ClassesToCourses (ClassId, CourseId) VALUES (?, ?)";
            $stmtClassCourse = $conn->prepare($sqlClassCourse);
            $stmtClassCourse->bind_param("ii", $classId, $courseId);
            $stmtClassCourse->execute();
        }

        header("Location: ../../views/afterLogin/academic_staff_dashboard.php?message=Thêm khóa học thành công");
        exit();
    } else {
        echo "Lỗi: " . $stmt->error;
    }
}
?>

<!-- Form thêm khóa học -->
<form action="add_course.php" method="POST">
    <label for="name">Tên khóa học:</label>
    <input type="text" name="name" required><br>
    <label for="credits">Số tín chỉ:</label>
    <input type="number" name="credits" min="1" required><br>
    <label for="description">Mô tả:</label>
    <textarea name="description" required></textarea><br>
    <label for="class_ids">Chọn lớp:</label>
    <select name="class_ids[]" multiple required>
        <?php
        $result = $conn->query("SELECT Id, Name FROM Classes");
        while ($row = $result->fetch_assoc()) {
            echo "<option value='{$row['Id']}'>{$row['Name']}</option>";
        }
        ?>
    </select><br>
    <input type="submit" value="Thêm khóa học">
</form>
