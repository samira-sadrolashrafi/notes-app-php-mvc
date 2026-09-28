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


    public function login($data){

        $this->db->query("SELECT * FROM users WHERE username = :username");

        $this->db->bind(':username' , $data['username']);

        $row = $this->db->singleResult();

        if(!$row){
            return false;
        }

        $hashedPassword = $row->password;

        if(password_verify($data['password'] , $hashedPassword)){
            return $row;
        }else{
            return false;
        }

    }

    public function storeRememberToken($userId, $tokenHash, $expiresAt){
        $this->db->query("INSERT INTO remember_tokens (user_id, token_hash, expires_at) VALUES (:user_id, :token_hash, :expires_at)");
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':token_hash', $tokenHash);
        $this->db->bind(':expires_at', $expiresAt);

        return $this->db->execute();
    }

    public function findUserByRememberToken($tokenHash){
        $this->db->query("SELECT users.id, users.username, users.email FROM remember_tokens INNER JOIN users ON users.id = remember_tokens.user_id WHERE remember_tokens.token_hash = :token_hash AND remember_tokens.expires_at > NOW() LIMIT 1");
        $this->db->bind(':token_hash', $tokenHash);

        return $this->db->singleResult();
    }

    public function deleteRememberToken($tokenHash){
        $this->db->query("DELETE FROM remember_tokens WHERE token_hash = :token_hash");
        $this->db->bind(':token_hash', $tokenHash);

        return $this->db->execute();
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


