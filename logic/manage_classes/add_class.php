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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $className = $_POST['name'];

    // Thêm lớp vào bảng Classes
    $sql = "INSERT INTO Classes (Name) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $className);

    if ($stmt->execute()) {
        header("Location: ../../views/afterLogin/academic_staff_dashboard.php?message=Thêm lớp học thành công");
        exit();
    } else {
        echo "Lỗi khi thêm lớp học: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Thêm Lớp học</title>
</head>
<body>
    <h2>Thêm Lớp học</h2>
    <form method="POST" action="">
        <label for="name">Tên lớp học:</label>
        <input type="text" name="name" required><br>
        <input type="submit" value="Thêm Lớp học">
    </form>
</body>
</html>
