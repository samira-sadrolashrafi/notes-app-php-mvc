<?php

require_once '../bootstrap.php';


// اگر فایل Seeder خودش autoload نمی‌شود:
require_once '../app/database/seeders/NoteSeeder.php';

$seeder = new NoteSeeder();

$result = $seeder->insertFakeNotes(1,100);

if($result){
    echo "Fake notes inserted successfully";
 
}else {

    echo "Insert failed";

}

