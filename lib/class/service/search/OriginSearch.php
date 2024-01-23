<?php
/**
 * SearchInterface 인터페이스의 메서드를 구현하는 구현체 클래스
 * 모든 검색 조건이 존재하는 경우를 처리하는 클래스
 */
namespace service\search;

use repository\BoardRepository;
use service\search\SearchInterface;

class OriginSearch implements SearchInterface {

    // admin
    public function getTotalItems(BoardRepository $boardRepository, $permission, $searchType, $searchQuery) {
        return $boardRepository->getTotalBoardCount($permission, $searchType, $searchQuery,null);
    }

    public function getBoards(BoardRepository $boardRepository, $offset, $itemsPerPage, $order, $permission, $searchType, $searchQuery) {
        return $boardRepository->getBoardsByPage($offset, $itemsPerPage, $order, $permission, $searchType, $searchQuery,null);
    }

    // user
    public function getTotalItemsByUserId(BoardRepository $boardRepository, $permission, $searchType, $searchQuery, $userId){
        return $boardRepository->getTotalBoardCount($permission, $searchType, $searchQuery, $userId);
    }

    public function getBoardsByPageAndUser(BoardRepository $boardRepository, $offset, $itemsPerPage, $order, $permission, $searchType, $searchQuery, $userId){
        return $boardRepository->getBoardsByPage($offset, $itemsPerPage, $order, $permission, $searchType, $searchQuery, $userId);
    }
}

?>