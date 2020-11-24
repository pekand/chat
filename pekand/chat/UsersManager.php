<?php

namespace pekand\Chat;

class UsersManager
{

    public function __construct($usersStorage) {
        $this->usersStorage = $usersStorage;
    }
    
}
