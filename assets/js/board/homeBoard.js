document.addEventListener('DOMContentLoaded', function() {
    // DOM이 로드된 후 실행되는 코드
    var tabs = document.querySelectorAll('.nav-tabs .nav-link');

    tabs.forEach(function(tab) {
        tab.addEventListener('click', function(event) {
            // 탭 클릭 시 실행되는 코드
            event.preventDefault();

            // 현재 페이지 URL을 기반으로한 새로운 URL을 생성
            var url = new URL(window.location.href);
            url.searchParams.set('order', this.getAttribute('href').includes('newest') ? 'newest' : 'oldest');

            // 페이지를 리로드
            window.location.href = url.toString();
        });
    });

    var pageLinks = document.querySelectorAll('.pagination a.page-link');
    pageLinks.forEach(function (link) {
        link.addEventListener('click', function (event) {
            event.preventDefault();

            // 현재 페이지 URL을 기반으로한 새로운 URL을 생성
            var url = new URL(window.location.href);

            // 기존의 모든 파라미터를 유지하고 page 파라미터를 업데이트
            var params = new URLSearchParams(url.search);
            params.set('page', this.getAttribute('href').split('=')[1]);

            // URL에 업데이트된 파라미터를 설정
            url.search = params.toString();

            // 페이지를 리로드
            window.location.href = url.toString();
        });
    });
});

function resetSearchParams() {
    // 현재 페이지 URL을 기반으로한 새로운 URL을 생성
    var url = new URL(window.location.href);

    // 모든 파라미터 제거
    url.search = '';

    // 페이지를 리로드
    window.location.href = url.toString();
}