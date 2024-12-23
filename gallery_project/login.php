Copy
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
    $password = $_POST['password'];

    // 사용자 확인 SQL
    $sql = "SELECT id, password FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // 사용자 존재
        $row = $result->fetch_assoc();
        // 비밀번호 확인
        if (password_verify($password, $row['password'])) {
            // 세션 시작 및 사용자 ID 저장
            session_start();
            $_SESSION['user_id'] = $row['id'];
            header("Location: my_page.php"); // 내 페이지로 이동
            exit();
        } else {
            $error_message = "비밀번호가 일치하지 않습니다.";
        }
    } else {
        $error_message = "사용자를 찾을 수 없습니다.";
    }
}

$conn->close();
?>

<?php include 'header.php'; ?>

<h1>로그인</h1>
<form action="login.php" method="post">
    <input type="text" name="username" placeholder="별명" required>
    <input type="password" name="password" placeholder="비밀번호" required>
    <button type="submit">로그인</button>
</form>

<!-- 로그인 오류 메시지 표시 -->
<p id="error-message" style="color: red;">
    <?php
    if (!empty($error_message)) {
        echo $error_message;
    }
    ?>
</p>

<p><a href="register.php">회원가입</a></p>
<p><a href="index.php">메인 페이지로 돌아가기</a></p>

<?php include 'footer.php'; ?>