<?php

function startAppSession()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}
