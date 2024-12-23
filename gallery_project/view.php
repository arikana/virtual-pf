<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
$servername = "localhost";
$username = "root"; // 데이터베이스 사용자 이름
$password = ""; // 데이터베이스 비밀번호
$dbname = "gallery"; // 데이터베이스 이름

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("연결 오류: " . $conn->connect_error);
}

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $sql = "SELECT file_name, upload_date FROM uploads WHERE user_id = '$user_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // 업로드된 파일 목록 출력
        echo "<h2>당신의 포트폴리오:</h2>";
        while ($row = $result->fetch_assoc()) {
            echo "<div>";
            echo "<p>파일명: " . htmlspecialchars($row["file_name"]) . "</p>";
            echo "<p>업로드 날짜: " . htmlspecialchars($row["upload_date"]) . "</p>";
            echo "<a href='uploads/" . htmlspecialchars($row["file_name"]) . "' target='_blank'>파일 보기</a>";
            echo "</div><hr>";
        }
    } else {
        echo "<p>업로드된 파일이 없습니다.</p>";
    }
} else {
    echo "<p>로그인 후에 파일을 확인할 수 있습니다.</p>";
}

$conn->close();
?>