<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // 로그인하지 않은 사용자를 로그인 페이지로 리다이렉트
    exit();
}

$servername = "localhost";
$dbusername = "root"; // DB 사용자 이름
$dbpassword = ""; // DB 비밀번호
$dbname = "gallery"; // DB 이름

// 데이터베이스 연결
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

if ($conn->connect_error) {
    die("연결 오류: " . $conn->connect_error);
}

// 사용자 이름 가져오기
$username = isset($_GET['username']) ? $_GET['username'] : '';

if (empty($username)) {
    echo "사용자를 찾을 수 없습니다.";
    exit();
}

// 해당 사용자의 포트폴리오 항목 가져오기
$sql = "SELECT uploads.*, users.username FROM uploads JOIN users ON uploads.user_id = users.id WHERE users.username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($username); ?>의 포트폴리오</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1><?php echo htmlspecialchars($username); ?>의 포트폴리오</h1>

    <div class="portfolio-grid">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="portfolio-item">
                        <a href="viewer.php?id=' . $row['id'] . '">
                            <img src="uploads/' . htmlspecialchars($row['file_name']) . '" alt="Portfolio Item">
                            <div class="caption">
                                <strong>' . htmlspecialchars($row['caption']) . '</strong>
                            </div>
                        </a>
                      </div>';
            }
        } else {
            echo "<p>업로드된 포트폴리오가 없습니다.</p>";
        }
        ?>
    </div>

    <p><a href="my_page.php">마이 페이지로 돌아가기</a></p>
    <p><a href="index.php">메인 페이지로 돌아가기</a></p>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>