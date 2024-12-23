<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$servername = "localhost";
$dbusername = "root"; // DB 사용자 이름
$dbpassword = ""; // DB 비밀번호
$dbname = "gallery"; // DB 이름

// 데이터베이스 연결
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

if ($conn->connect_error) {
    die("연결 오류: " . $conn->connect_error);
}

// 오류 메시지를 저장할 변수 초기화
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // 비밀번호 해싱

    // 중복 사용자 검사 쿼리
    $checkQuery = "SELECT * FROM users WHERE username = '$username'";
    $checkResult = $conn->query($checkQuery);

    if ($checkResult->num_rows > 0) {
        // 이미 사용 중인 별명일 경우
        $error_message = "이미 사용 중인 별명입니다. 다른 별명을 선택해 주세요.";
    } else {
        // 사용자 등록 쿼리
        $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
        
        if ($conn->query($sql) === TRUE) {
            // 회원가입 성공 후 signup_success.html로 리디렉션
            header("Location: signup_success.html");
            exit();
        } else {
            echo "회원가입 오류: " . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>회원가입</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>회원가입</h1>
    <form action="register.php" method="post">
        <input type="text" name="username" placeholder="별명" required>
        <input type="password" name="password" placeholder="비밀번호" required>
        <button type="submit">회원가입</button>
    </form>

    <!-- 중복 별명 경고 메시지 표시 -->
    <p id="error-message" style="color: red;">
        <?php
        if (!empty($error_message)) {
            echo $error_message;
        }
        ?>
    </p>

    <p><a href="index.html">메인 페이지로 돌아가기</a></p>
</body>
</html>