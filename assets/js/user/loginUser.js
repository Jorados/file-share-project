function submitForm() {
    var formData = new FormData(document.getElementById('loginForm'));

    // 비동기적으로 createUser.php에 POST 요청을 보냄
    fetch('/action/user/loginUser.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            // 서버에서 반환한 데이터를 처리
            if (data.status) {
                alert(data.content);
                if(data.available == 0) {
                    alert("최초 로그인 사용자는 비밀번호를 변경해주시기 바랍니다.");
                    window.location.href = '/pages/changePassword.php';
                }
                else window.location.href = '/pages/home.php';
            } else {
                alert(data.content);
            }
        })
        .catch(data => {
            alert(data.content);
            console.log('Error:', data.content);
        });
}