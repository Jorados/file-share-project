<?php
/**
 * Board 관련 비즈니스 로직 처리 클래스
 */

namespace service;

use repository\BoardRepository;

class BoardService{

    /**
     * 모든 글 조회 또는 검색을 활용한 글 조회
     * 조회되는 글 개수와 글 리턴
     * @param $items_per_page
     * @param $order
     * @param $offset
     * @param null $permission
     * @param null $searchType
     * @param null $searchQuery
     * @param null $user_id
     * @return array
     */
    public function getBoardByPage($items_per_page, $order, $offset, $permission = null, $searchType = null, $searchQuery = null, $user_id = null) {
        $boardRepository = new BoardRepository();

        if($permission=='-권한-') $permission=null;
        if($searchType=='-선택-') $searchType=null;
        $total_items = $boardRepository->getTotalBoardCount($permission, $searchType, $searchQuery, $user_id);
        $boards = $boardRepository->getBoardsByPage($offset, $items_per_page, $order, $permission, $searchType, $searchQuery, $user_id);

        $total_pages = ceil($total_items / $items_per_page);

        return [
            'total_pages' => $total_pages,
            'boards' => $boards,
        ];
    }
}

?>