/**
 * 관리자 -> 사용자 글 열람 권한 변경
 */

function submitBoardAuthority(newPermission) {
    var formData = new FormData(document.getElementById('authorityForm'));
    formData.append('change_permission', newPermission);

    fetch('/action/board/authorityChangeBoard.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            // 서버에서 반환한 데이터를 처리
            console.log(data.status + " " + data.content);
            if (data.status) {
                alert(data.content);
                window.location.href = '/pages/home.php';
            } else {
                alert(data.content);
            }
        })
        .catch(error => {
            alert("Error: " + error);
            console.log('Error:', error);
        });
}
