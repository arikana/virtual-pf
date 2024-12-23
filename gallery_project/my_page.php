<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html"); // 로그인하지 않은 사용자 접근 제한
    exit();
}

$servername = "localhost";
$username = "root"; // DB 사용자 이름
$password = ""; // DB 비밀번호
$dbname = "gallery"; // DB 이름

// 데이터베이스 연결
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("연결 오류: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT username FROM users WHERE id = '$user_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>내 페이지</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1><?php echo htmlspecialchars($user['username']); ?>님의 페이지</h1>
    <p><a href="upload.php">포트폴리오 업로드</a></p>
    <p><a href="view.php">업로드한 파일 보기</a></p>
    <p><a href="logout.php">로그아웃</a></p>
    <p><a href="index.php">메인 페이지로 돌아가기</a></p>

    <?php
        // 추가적인 정보나 기능을 여기에 삽입할 수 있습니다.
    ?>

</body>
</html>

<?php
$conn->close();
?>

