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

$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

if ($conn->connect_error) {
    die("연결 오류: " . $conn->connect_error);
}

$upload_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['files']['name'])) { // 'files' 배열이 있는지 확인
        $user_id = $_SESSION['user_id'];
        $target_dir = "uploads/";
        $uploadOk = 1;

        // 첫 번째 이미지의 파일명을 저장할 변수
        $first_image = '';

        // 각 파일에 대해 반복
        foreach ($_FILES['files']['name'] as $key => $name) {
            $target_file = $target_dir . basename($name);
            $caption = isset($_POST['captions'][$key]) ? trim($_POST['captions'][$key]) : ''; // 각 이미지에 대해 캡션 받기
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // 파일 크기 제한 (예: 500MB)
            if ($_FILES['files']['size'][$key] > 500000000) {
                $upload_message = "죄송합니다. 파일 크기가 너무 큽니다.";
                $uploadOk = 0;
                break;
            }

            // 파일 형식 확인
            if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                $upload_message = "죄송합니다. JPG, JPEG, PNG, GIF 파일만 허용됩니다.";
                $uploadOk = 0;
                break;
            }

            // 파일 업로드
            if ($uploadOk == 1) {
                if (move_uploaded_file($_FILES['files']['tmp_name'][$key], $target_file)) {
                    // 대표 이미지는 첫 번째 이미지로 설정
                    if (empty($first_image)) {
                        $first_image = $name; // 첫 번째 이미지의 파일명을 저장
                    }

                    // 업로드된 파일 정보를 DB에 저장
                    if ($caption !== '') { // 캡션이 비어있지 않은 경우에만 저장
                        $file_name = basename($name); // Store basename in a variable
                        $stmt = $conn->prepare("INSERT INTO uploads (user_id, file_name, caption) VALUES (?, ?, ?)");
                        $stmt->bind_param("iss", $user_id, $file_name, $caption); // Use variable here
                        $stmt->execute();
                        $stmt->close();
                    } else {
                        $upload_message = "캡션이 비어있어서 저장하지 않았습니다.";
                    }
                } else {
                    $upload_message = "파일 업로드 중 오류가 발생했습니다.";
                }
            }
        }

        // 첫 번째 이미지가 성공적으로 업로드된 경우 대표 이미지로 업데이트
        if (!empty($first_image)) {
            $stmt = $conn->prepare("UPDATE uploads SET is_featured = 1 WHERE file_name = ? AND user_id = ?");
            $stmt->bind_param("si", $first_image, $user_id);
            $stmt->execute();
            $stmt->close();
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
    <title>포트폴리오 업로드</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>포트폴리오 업로드</h1>
    <form action="upload.php" method="post" enctype="multipart/form-data">
        파일 선택:
        <input type="file" name="files[]" id="file" multiple required>
        <br>
        <!-- 각 파일에 대해 캡션 입력 필드 추가 -->
        <textarea name="captions[]" placeholder="파일에 대한 캡션을 입력하세요." required></textarea>
        <button type="submit" name="submit">업로드</button>
    </form>

    <p style="color: red;">
        <?php
        if (!empty($upload_message)) {
            echo $upload_message;
        }
        ?>
    </p>

<p><a href="my_page.php">마이 페이지로 돌아가기</a></p>
</body>
</html>