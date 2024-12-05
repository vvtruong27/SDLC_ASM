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

// Lấy thông tin lớp học cần sửa
if (isset($_GET['id'])) {
    $classId = $_GET['id'];
    $result = $conn->query("SELECT * FROM Classes WHERE Id = $classId");

    if ($result->num_rows > 0) {
        $class = $result->fetch_assoc();
    } else {
        echo "Không tìm thấy lớp học";
        exit();
    }
}

// Cập nhật thông tin lớp học
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $className = $_POST['name'];

    $sql = "UPDATE Classes SET Name = ? WHERE Id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $className, $classId);

    if ($stmt->execute()) {
        header("Location: ../../views/afterLogin/academic_staff_dashboard.php?message=Sửa lớp học thành công");
        exit();
    } else {
        echo "Lỗi khi cập nhật lớp học: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sửa Lớp học</title>
</head>
<body>
    <h2>Sửa Lớp học</h2>
    <form method="POST" action="">
        <label for="name">Tên lớp học:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($class['Name']); ?>" required><br>
        <input type="submit" value="Sửa Lớp học">
    </form>
</body>
</html>
