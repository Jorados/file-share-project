<?php

namespace database;

use dataset\BaseModel;

class DatabaseController {

    public static function arrayMapObjects(BaseModel $bindInstance, $data) {
        return array_map(
            function ($item) use ($bindInstance) {
                return $bindInstance::createNewInstance($item);
            },
            $data
        );
    }

}
?>
