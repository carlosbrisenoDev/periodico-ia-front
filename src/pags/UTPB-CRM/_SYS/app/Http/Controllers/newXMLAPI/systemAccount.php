<?php

namespace newXMLAPI;

class systemAccount{
    private $username;
    private $password;
    private $host;
    
    function __construct($username,$password,$host="127.0.0.1"){
        $this->username = $username;
        $this->password = $password;
        $this->host = $host;
    }
    
    public function getUser(){
        return $this->username;
    }
    public function getPassword(){
        return $this->password;
    }
    public function getHost(){
        return $this->host;
    }
    public function setUser($user){
        $this->username = $user;
    }
    public function setPassword($password){
        $this->password = $password;
    }
    public function setHost($host){
        $this->host = $host;
    }
    public function set($username,$password,$host){
        $this->username = $username;
        $this->password = $password;
        $this->host = $host;
    }
}