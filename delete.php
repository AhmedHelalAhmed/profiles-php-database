<?php
session_start();
require_once "pdo.php";
require_once "util.php";
isAllowed();

if (isset($_POST['cancel'])) {
    header('Location: index.php');
    return;
}



if (isset($_POST['delete']) && isset($_POST['profile_id'])) {
    $sql = "DELETE FROM Profile WHERE profile_id = :profile_id and user_id = :uid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':profile_id' => $_POST['profile_id'],
        ':uid'=>$_SESSION['user_id']
    ));
    $_SESSION['success'] = 'Profile deleted';
    header('Location: index.php') ;
    return;
}

if (! isset($_GET['profile_id'])) {
    $_SESSION['error'] = "Missing profile_id";
    header('Location: index.php');
    return;
}

$stmt = $pdo->prepare("SELECT first_name, last_name FROM Profile where profile_id = :profile_id");
$stmt->execute(array(":profile_id" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row === false) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header('Location: index.php') ;
    return;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ahmed Helal Ahmed's Confirmation of delete</title>
    <?php include 'head.php'; ?>

</head>
<body>
<div class="container">
    <h1>Deleteing Profile</h1>
    <p>First Name: <?= htmlentities($row['first_name']) ?></p>
    <p>Last Name: <?= htmlentities($row['last_name']) ?></p>
    <form method="post">
        <input type="hidden" name="profile_id" value="<?= $_GET['profile_id'] ?>">
        <input type="submit" value="Delete" name="delete">
        <input type="submit" name="cancel" value="Cancel">
    </form>
</div>
</body>
</html>





