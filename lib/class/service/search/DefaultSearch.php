<?php
/**
 * SearchInterface 인터페이스의 메서드를 구현하는 구현체 클래스
 * 검색 조건이 없는 경우를 처리하는 클래스
 */
namespace service\search;

use repository\BoardRepository;

class DefaultSearch implements SearchInterface {

    // admin
    public function getTotalItems(BoardRepository $boardRepository, $permission, $searchType, $searchQuery) {
        return $boardRepository->getTotalBoardCount(null,null,null,null);
    }

    public function getBoards(BoardRepository $boardRepository, $offset, $itemsPerPage, $order, $permission, $searchType, $searchQuery) {
        return $boardRepository->getBoardsByPage($offset, $itemsPerPage, $order, null,null,null,null);
    }


    // user
    public function getTotalItemsByUserId(BoardRepository $boardRepository, $permission, $searchType, $searchQuery, $userId){
        return $boardRepository->getTotalBoardCount(null,null,null, $userId);
    }

    public function getBoardsByPageAndUser(BoardRepository $boardRepository, $offset, $itemsPerPage, $order, $permission, $searchType, $searchQuery, $userId){
        return $boardRepository->getBoardsByPage($offset, $itemsPerPage, $order, null,null,null, $userId);
    }
}

?>