function submitCommentForm() {
    var formData = new FormData(document.getElementById('createCommentForm'));

    // 비동기적으로 createUser.php에 POST 요청을 보냄
    fetch('/action/comment/createComment.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            // 서버에서 반환한 데이터를 처리
            if (data.status) {
                alert(data.content);
                location.reload();
            } else {
                alert(data.content);
            }
        })
        .catch(data => {
            alert(data.content);
            console.log('Error:', data.content);
        });
}