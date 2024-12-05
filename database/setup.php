<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "StudentManagement";

// Tạo kết nối đến MySQL
$conn = new mysqli($servername, $username, $password);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Tạo database nếu chưa tồn tại
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "Database đã được tạo thành công.<br>";
} else {
    echo "Lỗi khi tạo database: " . $conn->error . "<br>";
}

// Sử dụng database
$conn->select_db($dbname);

// Tạo bảng Classes
$sql = "CREATE TABLE IF NOT EXISTS Classes (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(255) NOT NULL
)";
if ($conn->query($sql) === TRUE) {
    echo "Bảng Classes đã được tạo thành công.<br>";
} else {
    echo "Lỗi khi tạo bảng Classes: " . $conn->error . "<br>";
}

// Tạo bảng Courses
$sql = "CREATE TABLE IF NOT EXISTS Courses (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(255) NOT NULL,
    Credits INT NOT NULL,
    Description TEXT NOT NULL
)";
if ($conn->query($sql) === TRUE) {
    echo "Bảng Courses đã được tạo thành công.<br>";
} else {
    echo "Lỗi khi tạo bảng Courses: " . $conn->error . "<br>";
}

// Tạo bảng Users
$sql = "CREATE TABLE IF NOT EXISTS Users (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(255) UNIQUE NOT NULL,
    Password VARCHAR(255) NOT NULL,
    Email VARCHAR(255) UNIQUE NOT NULL,
    FullName VARCHAR(255) NOT NULL,
    Gender VARCHAR(255) NOT NULL,
    BirthYear INT NOT NULL,
    ClassId INT,
    FOREIGN KEY (ClassId) REFERENCES Classes(Id)
)";
if ($conn->query($sql) === TRUE) {
    echo "Bảng Users đã được tạo thành công.<br>";
} else {
    echo "Lỗi khi tạo bảng Users: " . $conn->error . "<br>";
}

// Tạo bảng Roles
$sql = "CREATE TABLE IF NOT EXISTS Roles (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(255) NOT NULL
)";
if ($conn->query($sql) === TRUE) {
    echo "Bảng Roles đã được tạo thành công.<br>";
} else {
    echo "Lỗi khi tạo bảng Roles: " . $conn->error . "<br>";
}

// Tạo bảng ClassesToCourses
$sql = "CREATE TABLE IF NOT EXISTS ClassesToCourses (
    ClassId INT NOT NULL,
    CourseId INT NOT NULL,
    PRIMARY KEY (ClassId, CourseId),
    FOREIGN KEY (ClassId) REFERENCES Classes(Id),
    FOREIGN KEY (CourseId) REFERENCES Courses(Id)
)";
if ($conn->query($sql) === TRUE) {
    echo "Bảng ClassesToCourses đã được tạo thành công.<br>";
} else {
    echo "Lỗi khi tạo bảng ClassesToCourses: " . $conn->error . "<br>";
}

// Tạo bảng Grades
$sql = "CREATE TABLE IF NOT EXISTS Grades (
    UserId INT NOT NULL,
    CourseId INT NOT NULL,
    PRIMARY KEY (UserId, CourseId),
    GradeValue FLOAT,
    FOREIGN KEY (UserId) REFERENCES Users(Id),
    FOREIGN KEY (CourseId) REFERENCES Courses(Id)
)";
if ($conn->query($sql) === TRUE) {
    echo "Bảng Grades đã được tạo thành công.<br>";
} else {
    echo "Lỗi khi tạo bảng Grades: " . $conn->error . "<br>";
}

// Tạo bảng UsersToRole
$sql = "CREATE TABLE IF NOT EXISTS UsersToRoles (
    UserId INT NOT NULL,
    RoleId INT NOT NULL,
    PRIMARY KEY (UserId, RoleId),
    FOREIGN KEY (UserId) REFERENCES Users(Id),
    FOREIGN KEY (RoleId) REFERENCES Roles(Id)
)";
if ($conn->query($sql) === TRUE) {
    echo "Bảng UsersToRole đã được tạo thành công.<br>";
} else {
    echo "Lỗi khi tạo bảng UsersToRole: " . $conn->error . "<br>";
}

// Đóng kết nối
$conn->close();
?>
