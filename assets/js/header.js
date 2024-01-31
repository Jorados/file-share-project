// 로그아웃 버튼이 클릭되면 페이지를 리다이렉트
function logoutAndRedirect() {
    // 사용자에게 한 번 더 확인
    var confirmLogout = window.confirm("로그아웃 하시겠습니까?");
    if (!confirmLogout) {
        return false; // 취소되면 아무 동작도 하지 않음
    }

    fetch('/action/user/logoutUser.php', {
        method: 'POST',
    })
        .then(response => response.json())
        .then(data => {
            // 서버에서 반환한 데이터를 처리
            if (data.status) {
                alert(data.content); // 성공 또는 실패 메시지 표시
                // 페이지 리다이렉션
                //window.location.href = "/index.php";
            } else {
                alert(data.content);
            }
        })

    return false; // 폼이 서브밋되지 않도록 합니다.
}

// 남은 시간 업데이트 함수
function updateSessionTimer() {
    if (remainingSeconds > 0) {
        remainingSeconds--;
        const minutes = Math.floor(remainingSeconds / 60);
        const seconds = remainingSeconds % 60;
        document.getElementById('session_timer').innerText = `${minutes}분 ${seconds}초`;
    } else {
        document.getElementById('session_timer').innerText = '세션 종료';
    }
}

// 1초 마다 업데이트
window.onload = function () {
    updateSessionTimer();
    setInterval(updateSessionTimer, 1000);
};