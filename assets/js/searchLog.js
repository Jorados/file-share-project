var originalContent = null;  // originalContent 초기화

function searchLog() {

    // 검색어 가져오기
    var searchKeyword = document.getElementById("search").value;
    // 로그 파일 내용 가져오기
    var logContent = document.getElementById("logContent").innerHTML;

    // 원본 내용이 없으면 현재 내용 저장
    if (originalContent === null) {
        originalContent = logContent;
    }

    // 정규 표현식을 사용하여 검색
    var regex = new RegExp(searchKeyword, 'gi'); //-> 전역적으로 대소문자구분 x
    var highlightedContent = logContent.replace(regex, function(match) {
        // return '<span class="text-danger">' + match + '</span>';
        return '<span style="font-weight: bold; color: #ff0000;">' + match + '</span>';
    });

    // 화면에 검색 결과 표시
    document.getElementById("logContent").innerHTML = highlightedContent;
}

function resetSearch() {
    // 저장된 원본 내용이 있으면 화면에 복원
    if (originalContent !== null) {
        document.getElementById("logContent").innerHTML = originalContent;
        originalContent = null;  // 원본 내용 초기화
    }
}