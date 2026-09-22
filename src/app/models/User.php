<?php


class User{

    private $db;

    public function __construct(){
        $this->db = new Database();
    }

    public function findUserByUsername($username){

        $this->db->query("SELECT * FROM users WHERE username = :username");

        $this->db->bind(':username' , $username);

        return $this->db->singleResult();

    }

    public function findUserByEmail($email){

        $this->db->query("SELECT * FROM users WHERE email = :email");

        $this->db->bind(':email', $email);

        return $this->db->singleResult();
    }

    public function register($data){

        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        $this->db->query("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");

        $this->db->bind(':username' , $data['username']);
        $this->db->bind(':email' , $data['email']);
        $this->db->bind(':password' , $hashedPassword);

        return $this->db->execute();

    }

    
}


