<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>게시글 생성</title>
    <!-- Bootstrap CSS 추가 -->
    <link href="https://cdn.jsdelivr.net/npm/dropzone@5.9.3/dist/dropzone.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<?php include '/var/www/html/includes/header.php'?>
<?php include '/var/www/html/includes/adminNavibar.php'?>
<div class="container mt-5">
    <div class="card mx-auto mb-5" style="max-width: 1000px;">
        <div class="card-header bg-dark text-white" style="max-height: 90px;">
            <h2 class="text-center">게시글 생성</h2>
        </div>

        <!-- 게시글 유형 선택 -->
        <div class="btn-group mt-3 mx-auto" role="group" aria-label="게시글 유형 선택">
            <label for="normalPost" class="ml-6 mr-4 mt-3">일반 :
                <input type="radio" class="btn-check" name="postType" id="normalPost" value="normal" onclick="setPostType('normal')" autocomplete="off" checked>
            </label>
            <label for="noticePost" class="ml-6 mr-3 mt-3">공지 :
                <input type="radio" class="btn-check" name="postType" id="noticePost" value="notification" onclick="setPostType('notification')" autocomplete="off">
            </label>
        </div>

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
<script src="https://cdn.jsdelivr.net/npm/dropzone@5.9.3/dist/min/dropzone.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function setPostType(type) {
        // 여기에있는 이 데이터를 boardCreate-admin 으로 넘겨야 한다.
        document.getElementById('postStatus').value = type;
    }

    Dropzone.options.myDropzone = {
        url: '/file/upload.php', // 파일 업로드 처리 스크립트의 URL
        autoProcessQueue: false, // 수동으로 파일을 처리하기 위해 false로 설정
        parallelUploads: 99,
        maxFiles: 5, // 최대 5개
        maxFilesize: 100,  // 100MB
        currentFileNum : 0,

        init: function() {
            var myDropzone = this;

            // 파일이 추가될 때 호출될 콜백 함수
            this.on("addedfile", function(file) {
                var cancelButton = Dropzone.createElement("<button class='btn btn-danger btn-sm mt-2'>취소</button>");
                var _this = this;

                cancelButton.addEventListener("click", function() {
                    _this.removeFile(file);  // 파일 제거
                });
                file.previewElement.appendChild(cancelButton);  // 취소 버튼을 파일 미리보기 요소에 추가
            });

            // 데이터 전송
            this.on("sending", function(file, xhr, formData) {
                formData.append('title', document.getElementById('title').value);
                formData.append('content', document.getElementById('content').value);
                formData.append('totalCount', document.querySelectorAll('.dz-preview').length);
            });

            // 파일 처리 완료 후
            // let currentFileNum=0;
            this.on("success", function(file, response) {
                this.options.currentFileNum++;
                const result = JSON.parse(response); // json 데이터 받기.
                if(this.options.currentFileNum == result.totalCount){
                    window.location.href = "/pages/admin/adminHome.php";
                }
            });

            document.querySelector("input[type=submit]").addEventListener("click", function(e) {
                e.preventDefault(); // 이벤트로 인한 이동 중지
                e.stopPropagation(); // 이벤트 전파 중지

                var formData = {
                    title: $('#title').val(),
                    content: $('#content').val(),
                    status: $('#postStatus').val()
                };

                $.ajax({
                    url: '/action/board/boardCreate_admin.php',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        console.log("Ajax Success:", response);
                        if (myDropzone.getQueuedFiles().length === 0) {
                            window.location.href = "adminHome.php";
                        }
                        myDropzone.processQueue();
                    },
                    error: function(error) {
                        console.error("Ajax Error:", error);
                    }
                });
            });
        }
    };
</script>
</body>
<footer>
    <?php include '/var/www/html/includes/footer.php'?>
</footer>
</html>
<style>
    .custom-mx-10 {
        margin-left: 8rem !important;
        margin-right: 8rem !important;
    }
</style>