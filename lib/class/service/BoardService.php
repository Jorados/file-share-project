<?php

namespace service;

use repository\BoardRepository;
use service\search\DefaultSearch;
use service\search\OriginSearch;
use service\search\PermissionSearch;
use service\search\SearchTypeSearch;

class BoardService{

    // 전략 패턴
    public function getBoardByPage($items_per_page, $order, $offset, $permission = null, $searchType = null, $searchQuery = null) {
        $boardRepository = new BoardRepository();
        $strategy = $this->getSearchStrategy($permission, $searchType, $searchQuery);

        // 검색 query
        $total_items = $strategy->getTotalItems($boardRepository, $permission, $searchType, $searchQuery);
        $boards = $strategy->getBoards($boardRepository, $offset, $items_per_page, $order, $permission, $searchType, $searchQuery);

        $total_pages = ceil($total_items / $items_per_page);

        return [
            'total_pages' => $total_pages,
            'boards' => $boards,
        ];
    }

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