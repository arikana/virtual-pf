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

// 업로드된 파일 가져오기
$sql = "SELECT uploads.*, users.username FROM uploads JOIN users ON uploads.user_id = users.id";
$result = $conn->query($sql);

$images = [];
while ($row = $result->fetch_assoc()) {
    $file_path = 'uploads/' . htmlspecialchars($row['file_name']);
    if (!empty($row['file_name']) && file_exists($file_path)) {
        $images[] = [
            'file_name' => htmlspecialchars($row['file_name']),
            'username' => htmlspecialchars($row['username']),
            'caption' => htmlspecialchars($row['caption'])
        ];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>포트폴리오 뷰어</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            background-color: #121212;
            color: #E0E0E0;
        }
        h1 {
            font-weight: bold;
            letter-spacing: -0.5px;
        }
        .portfolio-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
        .portfolio-item {
            margin: 10px;
            border: 1px solid #444;
            border-radius: 5px;
            overflow: hidden;
            max-width: 300px; /* 이미지 크기 조정 */
        }
        .portfolio-item img {
            width: 100%;
            height: auto;
        }
        .caption {
            padding: 10px;
            background-color: #1E1E1E;
            color: #ffffff;
            display: none; /* 기본적으로 숨김 */
        }
        .portfolio-item:hover .caption {
            display: block; /* 마우스 오버 시 보이도록 */
        }
    </style>
</head>
<body>
    <h1>포트폴리오 뷰어</h1>

    <div class="portfolio-grid">
        <?php foreach ($images as $image): ?>
            <div class="portfolio-item">
                <img src="uploads/<?php echo $image['file_name']; ?>" alt="Portfolio Item">
                <div class="caption">
                    <strong><a href="user_portfolio.php?username=<?php echo urlencode($image['username']); ?>"><?php echo $image['username']; ?></a></strong>: <?php echo $image['caption']; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>