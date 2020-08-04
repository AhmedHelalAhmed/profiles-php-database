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
        header("Location: edit.php?profile_id=".$_POST['profile_id']);
        return;
    }

    if (strpos($_POST['email'], '@')===false) {
        $_SESSION['error'] = "Email address must contain @";
        header("Location: edit.php?profile_id=".$_POST['profile_id']);
        return;
    }


    
    try {
        $sql = "UPDATE Profile SET first_name = :first_name, last_name = :last_name,
             email = :email,
             headline = :headline,
             summary = :summary
             WHERE profile_id = :profile_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(
            array(
                    ':first_name' => $firstName,
                    ':last_name' => $lastName,
                    ':email' => $email,
                    ':headline' => $headline,
                    ':summary' => $summary,
                    ':profile_id' => $_POST['profile_id']
                    )
        );
   
        $_SESSION['success'] = "Profile updated";
        header("Location: index.php");
        return;
    } catch (Exception $ex) {
        echo("Internal error, please contact support");
        error_log("SQL error=".$ex->getMessage());
        return;
    }
}
  
if (! isset($_GET['profile_id'])) {
    $_SESSION['error'] = "Missing profile_id";
    header('Location: index.php');
    return;
}
  
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
$profile_id = $row['profile_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ahmed Helal Ahmed's Profile Edit</title>
</head>
<body>
    <div>
        <h1>Editing Profile for <?= $_SESSION['name'] ?></h1>
        <?php
            if (isset($_SESSION['error'])) {
                echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
                unset($_SESSION['error']);
            }
        ?>       
        <form method="post">
            <p>First Name:
            <input type="text" name="first_name" size="60" value="<?= $firstName ?>"></p>
            <p>Last Name:
            <input type="text" name="last_name" size="60" value="<?= $lastName ?>"></p>
            <p>Email:
            <input type="text" name="email" size="30" value="<?= $email ?>"></p>
            <p>Headline:<br>
            <input type="text" name="headline" size="80" value="<?= $headline ?>"></p>
            <p>Summary:<br>
            <textarea name="summary" rows="8" cols="80"><?= $summary ?></textarea>
            </p>
            <input type="hidden" name="profile_id" value="<?= $profile_id ?>">
            <input type="submit" value="Save">
            <input type="submit" name="cancel" value="Cancel">
        </form>
    </div>
</body>
</html>
