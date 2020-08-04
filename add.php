<?php

session_start();

require_once "helpers.php";
isAllowed();
if (isset($_POST['cancel'])) {
    header('Location: index.php');
    return;
}
require_once "pdo.php";

if ($_POST) {
    $firstName=$_POST['first_name'];
    $lastName=$_POST['last_name'];
    $email=$_POST['email'];
    $headline=$_POST['headline'];
    $summary=$_POST['summary'];
    if (
        strlen($firstName)<1
        ||
        strlen($lastName)<1
        ||
        strlen($email)<1
        ||
        strlen($headline)<1
        ||
        strlen($summary)<1
        ) {
        $_SESSION['error'] = "All fields are required";
        header("Location: add.php");
        return;
    }

    if (strpos($_POST['email'], '@')===false) {
        $_SESSION['error'] = "Email address must contain @";
        header("Location: add.php");
        return;
    }
    
    try {
        $stmt = $pdo->prepare('INSERT INTO Profile(first_name, last_name, email, headline, summary, user_id) VALUES ( :fn, :ln, :em, :he, :su, :uid)');
        $stmt->execute(
            array(
                ':fn' => $firstName,
                ':ln' => $lastName,
                ':em' => $email,
                ':he' => $headline,
                ':su' => $summary,
                ':uid'=> $_SESSION['user_id']
                )
        );
        $_SESSION['success'] = "Profile added";
        header("Location: index.php");
        return;
    } catch (Exception $ex) {
        echo("Internal error, please contact support");
        error_log("SQL error=".$ex->getMessage());
        return;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ahmed Helal Ahmed's Profile Add</title>
</head>
<body>
    <div>
        <h1>Adding Profile for <?= $_SESSION['name'] ?></h1>
        <?php
            if (isset($_SESSION['error'])) {
                echo('<p style="color: red;">'.$_SESSION['error']."</p>\n");
                unset($_SESSION['error']);
            }
        ?>       
        <form method="post">
        <p>First Name:
        <input type="text" name="first_name" size="60"></p>
        <p>Last Name:
        <input type="text" name="last_name" size="60"></p>
        <p>Email:
        <input type="text" name="email" size="30"></p>
        <p>Headline:<br>
        <input type="text" name="headline" size="80"></p>
        <p>Summary:<br>
        <textarea name="summary" rows="8" cols="80"></textarea>
        </p>
        <p>
        <input type="submit" value="Add">
        <input type="submit" name="cancel" value="Cancel">
        </p>
        </form>
    </div>
</body>
</html>
