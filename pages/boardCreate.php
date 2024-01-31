<?php
/**
 * 관리자 -> 글 작성 페이지
 */
session_start();

include_once  '/var/www/html/lib/config.php';
use util\Util;
if($_SESSION['authority']==0) Util::serverRedirect("/pages/home.php");
?>
<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>게시글 생성</title>
    <link href="https://cdn.jsdelivr.net/npm/dropzone@5.9.3/dist/dropzone.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/index.css">
    <style>
        .custom-mx-10 { margin-left: 8rem !important; margin-right: 8rem !important; }
    </style>
</head>

<body>
<?php include '/var/www/html/includes/header.php'?>
<div class="container mt-5">
    <div class="card mx-auto mb-5" style="max-width: 1000px;">
        <div class="card-header custom-header" style="max-height: 90px;">
            <h3 class="text-center">게시글 생성</h3>
        </div>

        <!-- 게시글 유형 선택 -->
        <?php if($_SESSION['role']=='admin'):?>
            <div class="btn-group mt-3 mx-auto" role="group" aria-label="게시글 유형 선택">
                <label for="normalPost" class="ml-6 mr-4 mt-3">일반 :
                    <input type="radio" class="btn-check" name="postType" id="normalPost" value="normal" onclick="setPostType('normal')" autocomplete="off" checked>
                </label>
                <label for="noticePost" class="ml-6 mr-3 mt-3">공지 :
                    <input type="radio" class="btn-check" name="postType" id="noticePost" value="notification" onclick="setPostType('notification')" autocomplete="off">
                </label>
            </div>
        <?php endif; ?>

        <div class="card-body col-md-9 custom-mx-10">
            <h5 class="card-title text-center">파일 업로드</h5>
            <form class="dropzone" action="upload.php" id="myDropzone"></form>
        </div>

        <div class="card-body">
            <div class="d-flex justify-content-center">
                <form action="" method="post" class="col-md-9">
                    <div class="form-group">
                        <label for="title">제목</label>
                        <input type="text" id="title" name="title" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="content">내용</label>
                        <textarea id="content" name="content" rows="5" class="form-control" required></textarea>
                    </div>

                    <input type="hidden" id="postStatus" name="status" value="normal">

                    <div class="text-center">
                        <input type="submit" value="게시글 등록" class="btn btn-primary">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $dropzoneScriptPath = ($_SESSION['role'] == 'user') ? '/assets/js/dropzone/dropzoneUser.js' : '/assets/js/dropzone/dropzoneAdmin.js'; ?>
<script src="https://cdn.jsdelivr.net/npm/dropzone@5.9.3/dist/min/dropzone.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="<?php echo $dropzoneScriptPath; ?>"></script>
</body>
<footer>
    <?php include '/var/www/html/includes/footer.php'?>
</footer>
</html>
