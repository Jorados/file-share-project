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
            // 여기에 추가된 파일에 대한 초기화 로직을 추가할 수 있습니다.
            // 예: 파일 추가 시 사용자에게 취소 옵션을 제공하는 버튼을 생성
            var cancelButton = Dropzone.createElement("<button class='btn btn-danger btn-sm mt-1'>취소</button>");
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
                window.location.href = "../../../pages/user/userHome.php";
            }
        });

        document.querySelector("input[type=submit]").addEventListener("click", function(e) {
            e.preventDefault(); // 이벤트로 인한 이동 중지
            e.stopPropagation(); // 이벤트 전파 중지

            var formData = {
                title: $('#title').val(),
                content: $('#content').val()
            };

            $.ajax({
                url: '/action/board/boardCreate_user.php',
                type: 'POST',
                data: formData,
                success: function(response) {
                    console.log("Ajax Success:", response);
                    if (myDropzone.getQueuedFiles().length === 0) {
                        window.location.href = "../../../pages/user/userHome.php";
                    }
                    myDropzone.processQueue();
                    console.log("asdasdadasd");
                },
                error: function(error) {
                    console.error("Ajax Error:", error);
                }
            });
        });
    }
};