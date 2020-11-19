<?php

namespace Logic;

class UsersManager
{

    public function __construct($usersStorage) {
        $this->usersStorage = $usersStorage;
    }
    
}
