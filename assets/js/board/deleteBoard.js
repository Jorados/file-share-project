/**
 * 관리자 -> 글 삭제
 */
function submitDeleteForm() {
    var formData = new FormData(document.getElementById('deleteForm'));

    var confirmDelete = window.confirm("정말 해당 게시글을 삭제하시겠습니까?");
    if (!confirmDelete) {
        return;
    }

    fetch('/action/board/deletePost.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            // 서버에서 반환한 데이터를 처리
            if (data.status) {
                alert(data.content);
                window.location.href = '/pages/home.php';
            } else {
                alert(data.content);
            }
        })
        .catch(data => {
            alert(data.content);
            console.log('Error:', data.content);
        });
}