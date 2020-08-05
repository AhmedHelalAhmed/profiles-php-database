<?php
session_start();
require_once "pdo.php";
require_once "util.php";
isAllowed();
if (isset($_POST['cancel'])) {
    header('Location: index.php');
    return;
}

if ($_POST) {
    $firstName=$_POST['first_name'];
    $lastName=$_POST['last_name'];
    $email=$_POST['email'];
    $headline=$_POST['headline'];
    $summary=$_POST['summary'];

    $message=validateProfile();

    if (is_string($message)) {
        $_SESSION['error']=$message;
        header("Location: edit.php?profile_id=".$_POST['profile_id']);
        return;
    }
    $message=validatePosition();

    if (is_string($message)) {
        $_SESSION['error']=$message;
        header("Location: add.php");
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

        $stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id = :profile_id');
        $stmt->execute(array(':profile_id' =>$_REQUEST['profile_id']));
        $rank=1;
        for ($i=1; $i<=9; $i++) {
            if (!isset($_POST['year'.$i])) {
                continue;
            }
            if (!isset($_POST['description'.$i])) {
                continue;
            }
            $year=$_POST['year'.$i];
            $description= $_POST['description'.$i];

            $stmt = $pdo->prepare('INSERT INTO Position
        (profile_id, rank, year, description)
         VALUES ( :pid, :rank, :year, :desc)');
            $stmt->execute(
                array(
                ':pid' => $_REQUEST['profile_id'],
                ':rank' => $rank,
                ':year' => $year,
                ':desc' => $description,
            
                )
            );
            $rank++;
        }


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

$positions= loadPositions($pdo, $_REQUEST['profile_id']);
$pos =count($positions);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ahmed Helal Ahmed's Profile Edit</title>
    <?php include 'head.php'; ?>

</head>
<body>
    <div class="container">
        <h1>Editing Profile for <?= $_SESSION['name'] ?></h1>
        <?php
          flashMessages();

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
            <p>
            Position: <input type="submit" id="addPos" value="+">
            </p>
            <div id="position_fields">

            <?php

            foreach ($positions as $key => $position) {
                echo('<div id="'.'position'.($key+1).'">');
                echo('<p>');
                echo('Year: <input type="text" name="year'.($key+1).'" value="'.$position['year'].'">');
                echo('<input type="button" value="-" onclick="$(\'#position'.($key+1).'\').remove();return false;">');
                echo('</p>');
                echo('<textarea name="description'.($key+1).'" rows="8" cols="80">');
                echo(htmlentities($position['description']));
                echo('</textarea>');

                echo('</div>');
            }
                ?>
            </div>
        
            <input type="hidden" name="profile_id" value="<?= $profile_id ?>">
            <input type="submit" value="Save">
            <input type="submit" name="cancel" value="Cancel">
        </form>
    </div>



<script>
countPos = <?= $pos ?>;

// http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
$(document).ready(function(){
    window.console && console.log('Document ready called');
    $('#addPos').click(function(event){
        // http://api.jquery.com/event.preventdefault/
        event.preventDefault();
        if ( countPos >= 9 ) {
            alert("Maximum of nine position entries exceeded");
            return;
        }
        countPos++;
        window.console && console.log("Adding position "+countPos);
        $('#position_fields').append(
            '<div id="position'+countPos+'"> \
            <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
            <input type="button" value="-" \
            onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
            <textarea name="description'+countPos+'" rows="8" cols="80"></textarea>\
            </div>');
    });
});
</script>
</body>
</html>
