<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>게시글 생성</title>

    <link href="https://cdn.jsdelivr.net/npm/dropzone@5.9.3/dist/dropzone.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .custom-mx-10 { margin-left: 8rem !important; margin-right: 8rem !important; }
    </style>
</head>

<body>
<?php include '/var/www/html/includes/header.php'?>

<div class="container mt-5">
    <div class="card mx-auto mb-5" style="max-width: 1000px;">
        <div class="card-header bg-dark text-white mb-3" style="max-height: 90px;">
            <h2 class="text-center">게시글 생성</h2>
        </div>

        <div class="card-body col-md-9 custom-mx-10">
            <h5 class="card-title text-center">파일 업로드</h5>
            <form class="dropzone" action="upload.php" id="myDropzone"></form>
        </div>

        <div class="card-body mt-2">
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

                    <div class="text-center mt-4">
                        <input type="submit" value="게시글 등록" class="btn btn-primary">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/dropzone@5.9.3/dist/min/dropzone.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="/assets/js/dropzone/dropzoneUser.js"></script>
</body>
<footer>
    <?php include '/var/www/html/includes/footer.php'?>
</footer>
</html>
