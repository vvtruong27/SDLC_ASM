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

// Lấy thông tin sinh viên hiện tại
$sql = "SELECT * FROM Users WHERE Id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $fullname = $_POST['fullname'];
    $gender = $_POST['gender'];
    $birthyear = $_POST['birthyear'];
    $classid = $_POST['classid'];

    $updateSql = "UPDATE Users SET Email = ?, FullName = ?, Gender = ?, BirthYear = ?, ClassId = ? WHERE Id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("sssiii", $email, $fullname, $gender, $birthyear, $classid, $id);

    if ($updateStmt->execute()) {
        header("Location: ../../views/afterLogin/academic_staff_dashboard.php?message=Cập nhật sinh viên thành công");
        exit();
    } else {
        echo "Lỗi: " . $updateStmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Chỉnh sửa Sinh viên</title>
</head>

<body>
    <h2>Chỉnh sửa Sinh viên</h2>
    <form method="POST" action="">
        <label for="email">Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($student['Email']); ?>" required><br>
        <label for="fullname">Họ và tên:</label>
        <input type="text" name="fullname" value="<?php echo htmlspecialchars($student['FullName']); ?>" required><br>
        <label for="gender">Giới tính:</label>
        <select name="gender" required>
            <option value="male" <?php echo $student['Gender'] == 'male' ? 'selected' : ''; ?>>Nam</option>
            <option value="female" <?php echo $student['Gender'] == 'female' ? 'selected' : ''; ?>>Nữ</option>
            <option value="other" <?php echo $student['Gender'] == 'other' ? 'selected' : ''; ?>>Khác</option>
        </select><br>
        <label for="birthyear">Năm sinh:</label>
        <input type="number" name="birthyear" value="<?php echo htmlspecialchars($student['BirthYear']); ?>" min="1900"
            max="2024" required><br>
        <label for="classid">Mã lớp:</label>
        <input type="number" name="classid" value="<?php echo htmlspecialchars($student['ClassId']); ?>"><br>
        <input type="submit" value="Cập nhật">
    </form>
    <p><a href="academic_staff_dashboard.php">Quay lại Dashboard</a></p>
</body>

</html>