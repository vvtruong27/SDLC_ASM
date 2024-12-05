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

// Hàm để tìm kiếm sinh viên với vai trò là 'student'
function getStudents($conn, $keyword = '')
{
    $sql = "SELECT Users.* 
            FROM Users
            JOIN UsersToRoles ON Users.Id = UsersToRoles.UserId
            JOIN Roles ON UsersToRoles.RoleId = Roles.Id
            WHERE Roles.Name = 'student'";

    // Thêm điều kiện tìm kiếm nếu có từ khoá
    if ($keyword) {
        $sql .= " AND Users.FullName LIKE '%$keyword%'";
    }

    $result = $conn->query($sql);
    return $result;
}

function searchRecords($conn, $table, $column, $keyword)
{
    $sql = "SELECT * FROM $table WHERE $column LIKE '%$keyword%'";
    $result = $conn->query($sql);
    return $result;
}

// Kiểm tra nếu form tìm kiếm được gửi
$searchKeyword = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])) {
    $searchKeyword = $_POST['keyword'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard - Academic Management</title>
    <link rel="stylesheet" href="../assets/css/as.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .tabs {
            display: flex;
            cursor: pointer;
            background-color: #f1f1f1;
            padding: 10px;
        }

        .tabs div {
            padding: 10px 20px;
            border: 1px solid #ccc;
            border-bottom: none;
            margin-right: 5px;
            background-color: #fff;
        }

        .tabs div.active {
            background-color: #007bff;
            color: white;
        }

        .tab-content {
            border: 1px solid #ccc;
            padding: 20px;
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .add-btn {
            display: inline-block;
            margin-bottom: 15px;
            padding: 8px 12px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        .add-btn:hover {
            background-color: #0056b3;
        }

        .back-button-container {
            text-align: center;
            margin: 40px;
            margin-top: 0;
        }

        .back-button {
            padding: 10px 20px;
            font-size: 16px;
            color: white;
            background-color: #333;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .back-button:hover {
            background-color: #555;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Dashboard - Academic Management</h2>

        <!-- Tabs Section -->
        <div class="tabs">
            <div class="tab active" onclick="showTab('students')">Manage Students</div>
            <div class="tab" onclick="showTab('courses')">Manage Courses</div>
            <div class="tab" onclick="showTab('classes')">Manage Classes</div>
        </div>

        <!-- Tab Content Section -->
        <div id="students" class="tab-content active">
            <h3>Student List</h3>
            <a href="../../logic/manage_students/add_student.php" class="add-btn">Add Student</a>
            <form method="POST" action="">
                <input type="text" name="keyword" placeholder="Search for students..."
                    value="<?php echo htmlspecialchars($searchKeyword); ?>">
                <input type="submit" name="search" value="Search">
            </form>
            <table border="1">
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Gender</th>
                    <th>Birth Year</th>
                    <th>Class Code</th>
                    <th>Actions</th>
                </tr>
                <?php
                $students = getStudents($conn, $searchKeyword);
                if ($students->num_rows > 0) {
                    while ($row = $students->fetch_assoc()) {
                        echo "<tr>
                            <td>{$row['Id']}</td>
                            <td>{$row['Username']}</td>
                            <td>{$row['FullName']}</td>
                            <td>{$row['Email']}</td>
                            <td>{$row['Gender']}</td>
                            <td>{$row['BirthYear']}</td>
                            <td>{$row['ClassId']}</td>
                            <td>
                                <a href='../../logic/manage_students/edit_student.php?id={$row['Id']}'>Edit</a> |
                                <a href='../../logic/manage_students/delete_student.php?id={$row['Id']}' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>No students found.</td></tr>";
                }
                ?>
            </table>
        </div>

        <div id="courses" class="tab-content">
            <h3>Course List</h3>
            <a href="../../logic/manage_courses/add_course.php" class="add-btn">Add Course</a>
            <table border="1">
                <tr>
                    <th>ID</th>
                    <th>Course Name</th>
                    <th>Credits</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
                <?php
                $courses = $searchKeyword ? searchRecords($conn, 'Courses', 'Name', $searchKeyword) : $conn->query("SELECT * FROM Courses");
                if ($courses->num_rows > 0) {
                    while ($row = $courses->fetch_assoc()) {
                        echo "<tr>
                            <td>{$row['Id']}</td>
                            <td>{$row['Name']}</td>
                            <td>{$row['Credits']}</td>
                            <td>{$row['Description']}</td>
                            <td>
                                <a href='../../logic/manage_courses/edit_course.php?id={$row['Id']}'>Edit</a> |
                                <a href='../../logic/manage_courses/delete_course.php?id={$row['Id']}' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No courses found.</td></tr>";
                }
                ?>
            </table>
        </div>

        <div id="classes" class="tab-content">
            <h3>Class List</h3>
            <a href="../../logic/manage_classes/add_class.php" class="add-btn">Add Class</a>
            <table border="1">
                <tr>
                    <th>ID</th>
                    <th>Class Name</th>
                    <th>Actions</th>
                </tr>
                <?php
                $classes = $searchKeyword ? searchRecords($conn, 'Classes', 'Name', $searchKeyword) : $conn->query("SELECT * FROM Classes");
                if ($classes->num_rows > 0) {
                    while ($row = $classes->fetch_assoc()) {
                        echo "<tr>
                            <td>{$row['Id']}</td>
                            <td>{$row['Name']}</td>
                            <td>
                                <a href='../../logic/manage_classes/edit_class.php?id={$row['Id']}'>Edit</a> |
                                <a href='../../logic/manage_classes/delete_class.php?id={$row['Id']}' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No classes found.</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>

    <!-- Back Button -->
    <div class="back-button-container">
        <button onclick="history.back()" class="back-button">Back</button>
    </div>

    <script>
        function showTab(tabId) {
            const tabs = document.querySelectorAll('.tab-content');
            const tabButtons = document.querySelectorAll('.tab');
            tabs.forEach(tab => tab.classList.remove('active'));
            tabButtons.forEach(btn => btn.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');
            document.querySelector(`.tabs div[onclick="showTab('${tabId}')"]`).classList.add('active');
        }
    </script>
</body>

</html>


<?php
// Đóng kết nối
$conn->close();
?>