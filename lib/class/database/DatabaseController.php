<?php

namespace database;

use dataset\BaseModel;

class DatabaseController {

    /**
     * 쿼리 결과 데이터를 데이터 모델에 바인딩하는 메소드
     * @param BaseModel $bindInstance
     * @param array $data
     * @return array|BaseModel[]
     */
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
