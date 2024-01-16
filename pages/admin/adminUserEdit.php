<?php
session_start();
include '/var/www/html/lib/config.php';

use repository\UserRepository;

$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
$userRepository = new UserRepository();

if (!$user_id) die("사용자 ID가 제공되지 않았습니다.");

try {
    $user = $userRepository -> getUserById($user_id);
    if (!$user) die("해당 사용자를 찾을 수 없습니다.");
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// 수정 버튼 클릭
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $username = $_POST['username'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    $message = ""; // 초기 메시지 설정

    // 비밀번호 입력 여부 확인
    if (empty($password)) {
        $message = "비밀번호를 입력해주세요.";
    } else if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/", $password)) {
        // 비밀번호 유효성 검사 (영어와 숫자, 최소 8자)
        $message = "비밀번호는 영어와 숫자를 포함하여 8자 이상이어야 합니다.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // 비밀번호 암호화
        $updateStmt = $userRepository->updateUserDetails($user_id, $email, $hashedPassword, $username, $phone);
        header("Location: adminAuthority.php"); // 예시 리다이렉트
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <title>사용자 정보 변경</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '/var/www/html/includes/header.php'?>

<div class="container mt-5">
    <!-- 게시글 상세 정보 -->
    <div class="card mx-auto mb-5" style="max-width: 600px;">
        <div class="card-header bg-dark text-white" style="max-height: 90px;">
            <h3 class="text-center">사용자 정보 변경</h3>
        </div>

        <div class="card-body">

            <?php if (!empty($message)): ?>
                <div class="alert alert-info">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <div class="d-flex justify-content-center">
                <form action="/action/editUser.php" method="post" class="col-md-9" id="editForm">
                    <div class="form-group">
                        <label for="email">이메일:</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['email']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="password">비밀번호:</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="변경할 비밀번호를 입력하세요." required>
                    </div>

                    <div class="form-group">
                        <label for="username">사용자명:</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo $user['username']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">전화번호:</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $user['phone']; ?>">
                    </div>

                    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                    <button type="button" class="btn btn-warning" onclick="submitForm()">정보 수정</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function submitForm() {
        var formData = new FormData(document.getElementById('editForm'));

        // 비동기적으로 createUser.php에 POST 요청을 보냄
        fetch('/action/user/editUser.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                // 서버에서 반환한 데이터를 처리
                if (data.status) {
                    alert(data.content);
                    window.location.href = '/pages/admin/adminAuthority.php';
                } else {
                    alert(data.content);
                }
            })
            .catch(data => {
                alert(data.content);
                console.log('Error:', data.message);
            });
    }
</script>

</body>
<footer>
    <?php include '/var/www/html/includes/footer.php'?>
</footer>
</html>