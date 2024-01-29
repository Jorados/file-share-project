/**
 * 관리자, 사용자 비밀번호 변경
 */
function submitForm() {
    // 비밀번호 및 확인 비밀번호 값 가져오기
    var password = document.getElementById('password').value;
    var confirmPassword = document.getElementById('confirmPassword').value;

    // 비밀번호 확인
    if (password !== confirmPassword) {
        alert("비밀번호를 다시 확인해주세요.");
        return;
    }

    // 비밀번호 유효성 검사
    if (!isValidPassword(password)) {
        alert("비밀번호는 영문자와 숫자를 포함한 8자 이상이어야 합니다.");
        return;
    }

    // 비밀번호 변경 여부 확인
    var confirmChange = window.confirm("정말 비밀번호를 변경하시겠습니까?");

    if (!confirmChange) {
        // 사용자가 취소한 경우
        return;
    }

    // 비밀번호가 일치하고 유효성 검사를 통과하면 계속 진행
    var formData = new FormData(document.getElementById('passwordForm'));

    // 비동기적으로 changePassword.php에 POST 요청을 보냄
    fetch('/action/user/changePassword.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            // 서버에서 반환한 데이터를 처리
            if (data.status) {
                alert(data.content);
                window.location.href = '/index.php';
            } else {
                alert(data.content);
            }
        })
        .catch(data => {
            alert(data.content);
            console.log('Error:', data.content);
        });
}

// 비밀번호 유효성 검사 함수
function isValidPassword(password) {
    // 영문자와 숫자를 포함하고 8자 이상인지 확인
    var regex = /^(?=.*[A-Za-z])(?=.*\d).{8,}$/;
    return regex.test(password);
}