<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "StudentManagement";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Helper functions
function getClasses($conn)
{
    return $conn->query("SELECT * FROM Classes");
}

function getCourses($conn, $classId = null)
{
    $sql = "SELECT Courses.* 
            FROM Courses 
            JOIN ClassesToCourses ON Courses.Id = ClassesToCourses.CourseId";
    if ($classId) {
        $sql .= " WHERE ClassesToCourses.ClassId = $classId";
    }
    return $conn->query($sql);
}

function getStudentsWithGrades($conn, $classId, $courseId)
{
    $sql = "SELECT Users.Id AS UserId, Users.FullName, Grades.GradeValue
            FROM Users
            LEFT JOIN Grades ON Users.Id = Grades.UserId AND Grades.CourseId = $courseId
            WHERE Users.ClassId = $classId AND EXISTS (
                SELECT 1 FROM UsersToRoles ur JOIN Roles r ON ur.RoleId = r.Id 
                WHERE ur.UserId = Users.Id AND r.Name = 'student')";
    return $conn->query($sql);
}

// Handle grade actions (add, edit, delete)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_POST['userId'];
    $courseId = $_POST['courseId'];
    $action = $_POST['action'];

    if ($action === "edit") {
        // Handle the edit action
        $gradeValue = $_POST['gradeValue']; // gradeValue is required for editing
        $sql = "INSERT INTO Grades (UserId, CourseId, GradeValue) 
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE GradeValue = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iidi", $userId, $courseId, $gradeValue, $gradeValue);
        if ($stmt->execute()) {
            echo "<p style='color: green;'>Grade updated successfully!</p>";
        } else {
            echo "<p style='color: red;'>Error updating grade: " . $conn->error . "</p>";
        }
    } elseif ($action === "delete") {
        // Handle the delete action
        $sql = "DELETE FROM Grades WHERE UserId = ? AND CourseId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $userId, $courseId);
        if ($stmt->execute()) {
            echo "<p style='color: green;'>Grade deleted successfully!</p>";
        } else {
            echo "<p style='color: red;'>Error deleting grade: " . $conn->error . "</p>";
        }
    }
}

// Retrieve selected class and course
$selectedClassId = $_GET['classId'] ?? null;
$selectedCourseId = $_GET['courseId'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Teacher Dashboard</title>
    <style>
        /* General Styling */
        body {
            font-family: Arial, sans-serif;
            margin: 90px;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
        }

        h1,
        h2 {
            text-align: center;
            color: #0056b3;
        }

        /* Container */
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Form Controls */
        .form-control {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .form-control label {
            font-weight: bold;
            margin-right: 10px;
        }

        .form-control select {
            width: 70%;
            padding: 8px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .form-control select:focus {
            outline: none;
            border-color: #0056b3;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th,
        table td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #007bff;
            color: #fff;
            font-weight: bold;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        /* Buttons */
        .btn {
            padding: 8px 12px;
            text-decoration: none;
            color: #fff;
            font-size: 14px;
            border-radius: 4px;
            display: inline-block;
            margin: 0 5px;
        }

        .btn-save {
            background-color: #28a745;
        }

        .btn-save:hover {
            background-color: #218838;
        }

        .btn-delete {
            background-color: #dc3545;
        }

        .btn-delete:hover {
            background-color: #c82333;
        }

        .btn-submit {
            background-color: #0056b3;
        }

        .btn-submit:hover {
            background-color: #004085;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .form-control {
                flex-direction: column;
                align-items: flex-start;
            }

            .form-control label {
                margin-bottom: 5px;
            }

            .form-control select {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <h1>Teacher Dashboard</h1>

    <!-- Filter Section -->
    <form method="GET">
        <div class="form-control">
            <label for="classId">Select Class:</label>
            <select name="classId" id="classId" onchange="this.form.submit()">
                <option value="">-- Choose a Class --</option>
                <?php
                $classes = getClasses($conn);
                while ($class = $classes->fetch_assoc()) {
                    $selected = ($class['Id'] == $selectedClassId) ? "selected" : "";
                    echo "<option value='{$class['Id']}' $selected>{$class['Name']}</option>";
                }
                ?>
            </select>
        </div>
        <?php if ($selectedClassId): ?>
            <div class="form-control">
                <label for="courseId">Select Course:</label>
                <select name="courseId" id="courseId" onchange="this.form.submit()">
                    <option value="">-- Choose a Course --</option>
                    <?php
                    $courses = getCourses($conn, $selectedClassId);
                    while ($course = $courses->fetch_assoc()) {
                        $selected = ($course['Id'] == $selectedCourseId) ? "selected" : "";
                        echo "<option value='{$course['Id']}' $selected>{$course['Name']}</option>";
                    }
                    ?>
                </select>
            </div>
        <?php endif; ?>
    </form>

    <!-- Grades Table -->
    <?php if ($selectedClassId && $selectedCourseId): ?>
        <h2>Grades for Class and Course</h2>
        <table>
            <tr>
                <th>Student Name</th>
                <th>Grade</th>
                <th>Actions</th>
            </tr>
            <?php
            $students = getStudentsWithGrades($conn, $selectedClassId, $selectedCourseId);
            while ($student = $students->fetch_assoc()) {
                $grade = $student['GradeValue'] ?? "N/A";
                echo "<tr>
                    <td>{$student['FullName']}</td>
                    <td>{$grade}</td>
                    <td>
                        <form style='display:inline;' method='POST'>
                            <input type='hidden' name='userId' value='{$student['UserId']}'>
                            <label for='gradeValue'>Enter grade:</label>
                            <input type='hidden' name='courseId' value='{$selectedCourseId}'>
                            <input type='number' name='gradeValue' step='0.1' value='{$grade}' required>
                            <button type='submit' name='action' value='edit' class='btn btn-save'>Save</button>
                        </form>
                        <form style='display:inline;' method='POST'>
                            <input type='hidden' name='userId' value='{$student['UserId']}'>
                            <input type='hidden' name='courseId' value='{$selectedCourseId}'>
                            <button type='submit' name='action' value='delete' class='btn btn-delete'>Delete</button>
                        </form>
                    </td>
                </tr>";
            }
            ?>
        </table>
    <?php endif; ?>

    <?php $conn->close(); ?>
</body>

</html>