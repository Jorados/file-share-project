function submitDeleteForm() {
    var formData = new FormData(document.getElementById('deleteForm'));

    fetch('/action/board/deletePost.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            // 서버에서 반환한 데이터를 처리
            if (data.status) {
                alert(data.content);
                window.location.href = '/pages/admin/adminHome.php';
            } else {
                alert(data.content);
            }
        })
        .catch(data => {
            alert(data.content);
            console.log('Error:', data.content);
        });
}