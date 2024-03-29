function submitDeleteFormComment() {
    var formData = new FormData(document.getElementById('deleteFormComment'));

    var confirmDelete = window.confirm("댓글을 삭제하시겠습니까?");
    if (!confirmDelete) {
        return;
    }

    fetch('/action/comment/deleteComment.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            // 서버에서 반환한 데이터를 처리
            if (data.status) {
                alert(data.content);
                window.location.reload();
            } else {
                alert(data.content);
            }
        })
        .catch(data => {
            alert(data.content);
            console.log('Error:', data.content);
        });
}