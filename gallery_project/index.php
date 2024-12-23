<?php
session_start();
?>

<?php include 'header.php'; ?>

<h1>최근 포트폴리오</h1>

<!-- Correct buttons for 회원가입, 로그인, 포트폴리오 업로드, 마이페이지 -->
<div class="user-buttons">
    <?php if (!isset($_SESSION['user_id'])): ?>
        <a href="register.php"><button>회원가입</button></a>
        <a href="login.php"><button>로그인</button></a>
    <?php endif; ?>
    <a href="upload.php"><button>포트폴리오 업로드</button></a>
    <a href="my_page.php"><button>마이페이지</button></a>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="logout.php"><button>로그아웃</button></a>
    <?php endif; ?>
</div>

<div id="grid" class="portfolio-grid">
    <?php
    // 데이터베이스 연결
    $servername = "localhost";
    $dbusername = "root";
    $dbpassword = "";
    $dbname = "gallery";

    $conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

    if ($conn->connect_error) {
        die("연결 오류: " . $conn->connect_error);
    }

    // 최근 업로드된 포트폴리오 가져오기
    $sql = "SELECT uploads.*, users.username FROM uploads JOIN users ON uploads.user_id = users.id ORDER BY upload_date DESC LIMIT 9"; // 최근 9개 업로드
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $file_path = 'uploads/' . htmlspecialchars($row['file_name']);
            if (!empty($row['file_name']) && file_exists($file_path)) {
                echo '<div class="portfolio-item">
                        <a href="viewer.php?id=' . $row['id'] . '">
                            <img src="' . $file_path . '" alt="Portfolio Item">
                            <div class="caption" style="display: none;">
                                <strong><a href="user_portfolio.php?username=' . urlencode($row['username']) . '">' . htmlspecialchars($row['username']) . '</a></strong>: ' . htmlspecialchars($row['caption']) . '
                            </div>
                        </a>
                      </div>';
            }
        }
    } else {
        echo "<p>업로드된 포트폴리오가 없습니다.</p>";
    }

    $conn->close();
    ?>
</div>

<?php include 'footer.php'; ?>