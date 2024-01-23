<?php

namespace service\search;

use repository\BoardRepository;

interface SearchInterface {
    public function getTotalItems(BoardRepository $boardRepository, $permission, $searchType, $searchQuery);
    public function getBoards(BoardRepository $boardRepository, $offset, $itemsPerPage, $order, $permission, $searchType, $searchQuery);
}


?>