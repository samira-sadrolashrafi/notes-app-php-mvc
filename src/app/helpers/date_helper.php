<?php

function formatDateTime($datetime)
{
    if (!$datetime) {
        return '';
    }

    $date = new DateTime($datetime, new DateTimeZone('UTC'));

    $date->setTimezone(new DateTimeZone('Asia/Tehran'));

    return $date->format('Y-m-d H:i');
}

function formatTime($datetime)
{
    if (!$datetime) {
        return '';
    }

    $date = new DateTime($datetime, new DateTimeZone('UTC'));
    $date->setTimezone(new DateTimeZone('Asia/Tehran'));

    return $date->format('H:i');
}
