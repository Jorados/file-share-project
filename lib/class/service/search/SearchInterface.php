<?php
/**
 * 검색 관련 메서드를 정의한 인터페이스
 */
namespace service\search;

use repository\BoardRepository;

interface SearchInterface {

    //admin
    public function getTotalItems(BoardRepository $boardRepository, $permission, $searchType, $searchQuery);
    public function getBoards(BoardRepository $boardRepository, $offset, $itemsPerPage, $order, $permission, $searchType, $searchQuery);

    //user
    public function getTotalItemsByUserId(BoardRepository $boardRepository, $permission, $searchType, $searchQuery, $userId);
    public function getBoardsByPageAndUser(BoardRepository $boardRepository, $offset, $itemsPerPage, $order, $permission, $searchType, $searchQuery, $userId);
}


?>