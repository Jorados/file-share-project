<?php
/**
 * Board 관련 비즈니스 로직 처리 클래스
 */

namespace service;

use repository\BoardRepository;
use service\search\DefaultSearch;
use service\search\OriginSearch;
use service\search\PermissionSearch;
use service\search\SearchTypeSearch;

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
        $strategy = $this->getSearchStrategy($permission, $searchType, $searchQuery);

        // admin
        if($user_id===null){
            $total_items = $strategy->getTotalItems($boardRepository, $permission, $searchType, $searchQuery);
            $boards = $strategy->getBoards($boardRepository, $offset, $items_per_page, $order, $permission, $searchType, $searchQuery);
        }
        // user
        else{
            $total_items = $strategy->getTotalItemsByUserId($boardRepository, $permission, $searchType, $searchQuery, $user_id);
            $boards = $strategy->getBoardsByPageAndUser($boardRepository, $offset, $items_per_page, $order, $permission, $searchType, $searchQuery, $user_id);
        }

        $total_pages = ceil($total_items / $items_per_page);

        return [
            'total_pages' => $total_pages,
            'boards' => $boards,
        ];
    }

    /**
     * 각 매개변수 조건에 맞는 클래스(구현체) return
     * @param null $permission
     * @param null $searchType
     * @param null $searchQuery
     * @return DefaultSearch|OriginSearch|PermissionSearch|SearchTypeSearch
     */
    private function getSearchStrategy($permission=null, $searchType=null, $searchQuery=null) {
        if($permission !== '-권한-'  && $searchType !== '-선택-' && $searchQuery !== '') {
            return new OriginSearch();
        }
        else if($permission === '-권한-'  && $searchType !== '-선택-' && $searchQuery !== '') {
            return new SearchTypeSearch();
        }
        else if(($permission !== '-권한-' && $searchType !== '-선택-') || ($permission !== '-권한-' && $searchType === '-선택-')) {
            return  new PermissionSearch();
        }
        else return new DefaultSearch();
    }

}

?>