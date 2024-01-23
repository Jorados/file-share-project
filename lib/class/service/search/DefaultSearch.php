<?php

namespace service\search;

use repository\BoardRepository;
use service\search\SearchInterface;

class DefaultSearch implements SearchInterface {
    public function getTotalItems(BoardRepository $boardRepository, $permission, $searchType, $searchQuery) {
        return $boardRepository->getTotalBoardCount();
    }

    public function getBoards(BoardRepository $boardRepository, $offset, $itemsPerPage, $order, $permission, $searchType, $searchQuery) {
        return $boardRepository->getBoardsByPage($offset, $itemsPerPage, $order);
    }

}

?>