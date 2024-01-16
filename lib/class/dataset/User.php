<?php

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

    public function __construct($data = null)
    {
        parent::__construct($data);
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function setUserId($user_id): self
    {
        $this->user_id = $user_id;
        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username): self
    {
        $this->username = $username;
        return $this;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function setPhone($phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setRole($role): self
    {
        $this->role = $role;
        return $this;
    }

    public function getAuthority()
    {
        return $this->authority;
    }

    public function setAuthority($authority): self
    {
        $this->authority = $authority;
        return $this;
    }

    public function getAvailable()
    {
        return $this->available;
    }

    public function setAvailable($available): self
    {
        $this->available = $available;
        return $this;
    }
}
