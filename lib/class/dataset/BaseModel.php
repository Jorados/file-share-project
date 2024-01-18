<?php

namespace dataset;

// 추상 클래스 --> 직접 new 인스턴스 xx , 다른 클래스에서 상속해서 사용하는 목적.
abstract class BaseModel{

    public function __construct($data = null){
        $this->bindData($data);
    }

    public function getObjectVars(){
        return get_object_vars($this);
    }

    protected function bindData($data){
        if (empty($data)) return;

        foreach ($data as $key => $value) {
            // 객체에 존재하지 않는 속성이면 무시
            if (!array_key_exists($key, $this->getObjectVars())) continue;  // $this->getObjectVars() : 해당 객체의 속성($this)을 배열(key,value)로 가져오는것.
            $this->{$key} = $value; // 값 할당
        }
    }

    public static function createNewInstance($data){
        return new static($data); //--> new static() 해주면 해당 부모클래스를 호출한 BaseModel 내의 자식클래스가 return 된다.
    }

    // ----- 개념 -----
    // get_object_vars
    // 객체의 속성을 배열로 반환하는 내장 함수 / ( key,value ) 형태로 데이터 보관
    // 각 배열 키는 속성의 이름이 되고, 값은 해당 속성의 현재 값이 된다.
}
