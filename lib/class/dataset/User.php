<?php
/**
 * 유저 - 데이터 모델
 */
namespace dataset;

use dataset\BaseModel;

class User extends BaseModel{
    protected $user_id;
    protected $email;
    protected $password;
    protected $username;
    protected $phone;
    protected $role;
    protected $authority;
    protected $available;

    public function __construct($data = null){ //$data=null --> 매개변수 없으면 기본값으로 null을 사용
        parent::__construct($data); // '::' --> 1. 정적 메소드 참조, 2. 부모 클래스 생성자 호출 .
    }

    public function getUserId(){
        return $this->user_id;
    }

    public function setUserId($user_id): self{
        $this->user_id = $user_id;
        return $this;
    }

    public function getEmail(){
        return $this->email;
    }

    public function setEmail($email): self{
        $this->email = $email;
        return $this;
    }

    public function getPassword(){
        return $this->password;
    }

    public function setPassword($password): self{
        $this->password = $password;
        return $this;
    }

    public function getUsername(){
        return $this->username;
    }

    public function setUsername($username): self{
        $this->username = $username;
        return $this;
    }

    public function getPhone(){
        return $this->phone;
    }

    public function setPhone($phone): self{
        $this->phone = $phone;
        return $this;
    }

    public function getRole(){
        return $this->role;
    }

    public function setRole($role): self{
        $this->role = $role;
        return $this;
    }

    public function getAuthority(){
        return $this->authority;
    }

    public function setAuthority($authority): self{
        $this->authority = $authority;
        return $this;
    }

    public function getAvailable(){
        return $this->available;
    }

    public function setAvailable($available): self{
        $this->available = $available;
        return $this;
    }
}
