<?php

if (!function_exists('isAllowed')) {
    function isAllowed()
    {
        if (!isset($_SESSION['user_id'])) {
            die("ACCESS DENIED");
        }
    }
}


if (!function_exists('flashMessages')) {
    function flashMessages()
    {
        if (isset($_SESSION['error'])) {
            echo '<p style="color:red">'. $_SESSION['error'] ."</p>\n";
            unset($_SESSION['error']);
        }
        
        if (isset($_SESSION['success'])) {
            echo('<p style="color: green;">' . $_SESSION['success'] . "</p>\n");
            unset($_SESSION['success']);
        }
    }
}


if (!function_exists('validateProfile')) {
    function validateProfile()
    {
        if (
            strlen($_POST['first_name'])<1
            ||
            strlen($_POST['last_name'])<1
            ||
            strlen($_POST['email'])<1
            ||
            strlen($_POST['headline'])<1
            ||
            strlen($_POST['summary'])<1
            ) {
            return "All fields are required";
            ;
        }
    
        if (strpos($_POST['email'], '@')===false) {
            return "Email address must contain @";
        }

        return true;
    }
}

if (!function_exists('validatePosition')) {
    function validatePosition()
    {
        for ($i=1; $i<=9; $i++) {
            if (!isset($_POST['year'.$i])) {
                continue;
            }
            if (!isset($_POST['description'.$i])) {
                continue;
            }
            $year=$_POST['year'.$i];
            $description= $_POST['description'.$i];

            if (strlen($year)==0||strlen($description)==0) {
                return "All fields are required";
            }
    
            if (! is_numeric($year)) {
                return "Year must be numeric";
            }
        }
       
        return true;
    }
}


if (!function_exists('loadPositions')) {
    function loadPositions($pdo, $profileId)
    {
        try {
            $stmt = $pdo->prepare("SELECT * FROM Position where profile_id = :profile_id order by rank");
            $stmt->execute(array(':profile_id'=>$profileId));
            $positions=array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $positions[]=$row;
            }
            return $positions;
        } catch (Exception $ex) {
            echo("Internal error, please contact support");
            error_log("SQL error=" . $ex->getMessage());
            return;
        }
    }
}
