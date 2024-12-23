<?php
session_start();
session_unset();
session_destroy();
header("Location: index.php"); // 메인 페이지로 리다이렉트
exit();
?>