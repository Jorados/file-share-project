<?php

class User
{
    private string $user_id;
    private string $email;

    public function getUser_id(): string
    {
        return $this->user_id;
    }

    public function setUser_id(string $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function setEmail(string $email): self
    {
        $this->$email = $email;

        return $this;
    }
}
