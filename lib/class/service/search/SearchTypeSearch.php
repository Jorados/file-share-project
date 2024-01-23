<?php
/**
 * SearchInterface 인터페이스의 메서드를 구현하는 구현체 클래스
 * 열람권한(permission) 검색 조건만 존재하지 않는 경우를 처리하는 클래스
 */
namespace service\search;

use repository\BoardRepository;
use service\search\SearchInterface;

class SearchTypeSearch implements SearchInterface {

    // admin
    public function getTotalItems(BoardRepository $boardRepository, $permission, $searchType, $searchQuery) {
        return $boardRepository->getTotalBoardCount(null, $searchType, $searchQuery,null);
    }

    public function getBoards(BoardRepository $boardRepository, $offset, $itemsPerPage, $order, $permission, $searchType, $searchQuery) {
        return $boardRepository->getBoardsByPage($offset, $itemsPerPage, $order, null, $searchType, $searchQuery,null);
    }

    // user
    public function getTotalItemsByUserId(BoardRepository $boardRepository, $permission, $searchType, $searchQuery, $userId){
        return $boardRepository->getTotalBoardCount(null, $searchType, $searchQuery, $userId);
    }

    public function getBoardsByPageAndUser(BoardRepository $boardRepository, $offset, $itemsPerPage, $order, $permission, $searchType, $searchQuery, $userId){
        return $boardRepository->getBoardsByPage($offset, $itemsPerPage, $order, null, $searchType, $searchQuery, $userId);
    }
}

?>

