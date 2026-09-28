<?php

function redirect($page)
{

    header("Location: " . URLROOT . "/public/?page=" . $page);

    exit;

}