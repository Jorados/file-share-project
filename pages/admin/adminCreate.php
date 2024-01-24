<?php
///**
// * xxxx
// */
//
//include '/var/www/html/repository/userRepository.php';
//
//$userRepository = new UserRepository();
//try {
//    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//        $email = $_POST['email'];
//        $password = $_POST['password'];
//        $username = $_POST['username'];
//        $phone = $_POST['phone'];
//        $role = 'admin';
//
//        // 비밀번호 해싱 -> 나중에 로그인할때 verify 함수때문에
//        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
//
//        // $stmt -> DB 관련 작업에서 사용하는 변수
//        $userRepository->adminCreateUser($email, $hashed_password, $username, $phone, $role);
//        echo "게시글이 성공적으로 추가되었습니다.";
//
//        header("Location: /index.php");  // boardList.php로 리다이렉션
//        exit;  // 리다이렉션 후 스크립트 종료
//    }
//} catch (PDOException $e) {
//    echo "Error: " . $e->getMessage();
//}
//?>
<!---->
<!---->
<!--<!DOCTYPE html>-->
<!--<html lang="ko">-->
<!--<head>-->
<!--    <meta charset="UTF-8">-->
<!--    <meta name="viewport" content="width=device-width, initial-scale=1.0">-->
<!--    <title>회원 생성</title>-->
<!--</head>-->
<!--<body>-->
<!--<h2 align="center">관리자 생성</h2>-->
<!---->
<!--<!-- 예외 메세지 생기면 상단에 출력 -->-->
<?php //if (!empty($message)) echo "<p>$message</p>"; ?>
<!--<div align="center">-->
<!--    <form  action="" method="post">-->
<!--        <label for="email">이메일</label><br>-->
<!--        <input type="text" id="email" name="email" required><br><br>-->
<!---->
<!--        <label for="content">비밀번호</label><br>-->
<!--        <input type="password" id="password" name="password"><br><br>-->
<!---->
<!--        <label for="username">이름</label><br>-->
<!--        <input type="text" id="username" name="username" required><br><br>-->
<!---->
<!--        <label for="phone">전화번호</label><br>-->
<!--        <input type="text" id="phone" name="phone"><br><br>-->
<!---->
<!--        <input type="submit" value="회원 가입">-->
<!--    </form>-->
<!--</div>-->
<!--</body>-->
<!--<footer>-->
<!--    --><?php //include '/var/www/html/includes/footer.php'?>
<!--</footer>-->
<!--</html>-->
