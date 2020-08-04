<?php

if (!function_exists('isAllowed')) {
    function isAllowed()
    {
        if (!isset($_SESSION['user_id'])) {
            die("Not logged in");
        }
    }
}
