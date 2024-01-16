<?php
namespace dataset;

class User{

    private $user_id;
    private $email;
    private $password;
    private $username;
    private $phone;
    private $role;
    private $authority;
    private $available;

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
