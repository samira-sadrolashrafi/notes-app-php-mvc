<?php

use Carbon\Carbon;

class NoteSeeder{

    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function insertFakeNotes($userId, $count = 20){
        $sql = "INSERT INTO notes(user_id , title, body, created_at, updated_at, is_public)
        VALUES ";

        $values = [];

        $params = [];

        for($i = 1 ; $i<= $count ; $i++){
            $values[] = "(:user_id_$i, :title_$i, :body_$i, :created_at_$i, :updated_at_$i, :is_public_$i)";

            $createdAt = Carbon::now()
                ->subDays(rand(1, 180))
                ->subHours(rand(0, 23))
                ->format('Y-m-d H:i:s');

            $params["user_id_$i"] = $userId;
            $params["title_$i"] = "یادداشت تست شماره $i";
            $params["body_$i"] = "این متن آزمایشی برای یادداشت شماره $i است.";
            $params["created_at_$i"] = $createdAt;
            $params["updated_at_$i"] = $createdAt;
            $params["is_public_$i"] = rand(0 , 1);

        }

        $sql .= implode(',' , $values);

        $this->db->query($sql);

        foreach($params as $key => $value){
            $this->db->bind(':'. $key, $value);
        }

        return $this->db->execute();
    }
}
