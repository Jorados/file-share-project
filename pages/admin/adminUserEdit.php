<?php
/**
 *  관리자 -> 사용자 계정 수정 페이지
 */

session_start();
include '/var/www/html/lib/config.php';

use repository\UserRepository;
$userRepository = new UserRepository();

$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
$user = $userRepository -> getUserById($user_id);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <title>사용자 정보 변경</title>
    <link rel="stylesheet" href="/assets/css/index.css">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '/var/www/html/includes/header.php'?>

<div class="container mt-5">
    <!-- 게시글 상세 정보 -->
    <div class="card mx-auto mb-5" style="max-width: 700px;">
        <div class="card-header custom-header" style="max-height: 90px;">
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
                        <input type="email" class="form-control" id="email" name="email" value="<?=$user->getEmail(); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="password">비밀번호:</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="변경할 비밀번호를 입력하세요." required>
                    </div>

                    <div class="form-group">
                        <label for="username">사용자명:</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?=$user->getUsername(); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">전화번호:</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?=$user->getPhone(); ?>">
                    </div>

                    <br>
                    <input type="hidden" name="user_id" value="<?=$user->getUserId(); ?>">
                    <button type="button" class="btn btn-warning btn-block" onclick="submitForm()">정보 수정</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="/assets/js/user/editUser.js"></script>

</body>
<footer>
    <?php include '/var/www/html/includes/footer.php'?>
</footer>
</html>