<?php

namespace newXMLAPI;

class emailAccount{
    private $domain;
    private $username;
    private $password;
    
    function __construct($domain=null,$user=null,$pass=null){
        $this->domain = $domain;
        $this->username = $user;
        $this->password = $pass;
    }
    public function setUser($user){
        $this->username = $user;
    }
    public function setPassword($pass){
        $this->password = $pass;
    }
    public function setDomain($domain){
        $this->domain = $domain;
    }
    public function getUser(){
        return $this->username;
    }
    public function getPassword(){
        return $this->password;
    }
    public function getDomain(){
        return $this->domain;
    }
    public function set($domain,$user,$pass){
        $this->domain = $domain;
        $this->username = $user;
        $this->password = $pass;
    }
}
    
