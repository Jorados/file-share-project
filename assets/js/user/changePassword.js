function submitForm() {
    var formData = new FormData(document.getElementById('passwordForm'));

    // 비동기적으로 createUser.php에 POST 요청을 보냄
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