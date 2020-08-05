<?php

session_start();

if (! isset($_GET['profile_id'])) {
    $_SESSION['error'] = "Missing profile_id";
    header('Location: index.php');
    return;
}

require_once "pdo.php";
require_once "util.php";

$stmt = $pdo->prepare("SELECT * FROM Profile where profile_id = :profile_id");
$stmt->execute(array(":profile_id" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);


if ($row === false) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header('Location: index.php') ;
    return;
}


$firstName=htmlentities($row['first_name']);
$lastName=htmlentities($row['last_name']);
$email=htmlentities($row['email']);
$headline=htmlentities($row['headline']);
$summary=htmlentities($row['summary']);
$positions= loadPositions($pdo, $_REQUEST['profile_id']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ahmed Helal Ahmed's Profile View</title>
    <?php include 'head.php'; ?>

</head>
<body>
<div class="container">
<h1>Profile information</h1>
    <p>First Name: <?= $firstName ?></p>
    <p>Last Name: <?= $lastName ?></p>
    <p>Email: <?= $email ?></p>
    <div>
    <p>Headline:</p>
    <?= $headline ?>
    </div>
    <div>
    <p>Summary:</p>
    <?= $summary ?>
    </div>
    <?php
        if (count($positions)>0) {
            ?>
    <div>
    <p>Position</p>
    <ul>
        <?php
            foreach ($positions as $position) {
                echo('<li>');
                echo($position['year'].': '.htmlentities($position['description']));
                echo('</li>');
            } ?>
    </ul>
    </div>
    <?php
        } ?>
<a href="index.php">Done</a>
</div>
</body>
</html>
