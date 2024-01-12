<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>게시판</title>
    <link href="https://cdn.jsdelivr.net/npm/dropzone@5.9.3/dist/dropzone.css" rel="stylesheet">
</head>
<body>

<h2>파일 업로드</h2>
<form  class="dropzone" action="upload.php" id="myDropzone">
</form>

<h2>파일 목록</h2>
<ul>
    <?php
    foreach (glob('/var/www/html/file/uploads/*') as $file) {
        if (is_file($file)) {
            echo '<li><a href="download.php?file=' . urlencode(basename($file)) . '">' . basename($file) . '</a></li>';
        }
    }
    ?>
</ul>

<script src="https://cdn.jsdelivr.net/npm/dropzone@5.9.3/dist/min/dropzone.min.js"></script>
<script>
    // Dropzone.js 설정
    Dropzone.options.myDropzone = {
        // 기본 설정
        autoProcessQueue: true, // 파일을 자동으로 업로드 큐에 추가
        // url: "http://192.168.137.128/file/upload.php",     // 업로드를 처리하는 서버의 URL
        // method: "post",         // HTTP 메서드
        addRemoveLinks: true,   // 파일 업로드를 제거할 수 있는 링크 표시

        // 파일 타입 및 크기 제한
        maxFilesize: 100,  // 최대 100MB까지 허용
        maxFiles: 5,      // 최대 5개 파일까지 업로드 허용

        // // 이벤트 리스너
        // init: function() {
        //     this.on("addedfile", function(file) {
        //         console.log("Added file: " + file.name);
        //     });
        //     this.on("complete", function(file) {
        //         console.log("Completed uploading: " + file.name);
        //     });
        //     this.on("error", function(file, errorMessage) {
        //         console.log("Error uploading file: " + file.name + " - " + errorMessage);
        //     });
        // },

        // 추가적인 설정
        // headers: {
        //     "Authorization": "Bearer YOUR_TOKEN",   // 필요한 경우 헤더에 토큰 추가
        //     "My-Custom-Header": "Header-Value"      // 추가적인 사용자 정의 헤더
        // },
        //
        // // 파일 업로드 전 처리
        // preprocess: function(file, done) {
        //     console.log("Preprocessing file: " + file.name);
        //     done();  // 파일 전처리 완료
        // }
    };

    // document.addEventListener("DOMContentLoaded", function() {
    //     var inputs = document.querySelectorAll('form#myDropzone input[type="file"]');
    //
    //     inputs.forEach(function(input) {
    //         console.log(input); // 입력 요소 자체
    //         console.log(input.type); // 입력 요소의 유형 (예: "file")
    //     });
    // });
</script>


</body>
</html>

