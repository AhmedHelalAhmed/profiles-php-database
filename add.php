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
        header("Location: add.php");
        return;
    }


    $message=validatePosition();

    if (is_string($message)) {
        $_SESSION['error']=$message;
        header("Location: add.php");
        return;
    }

    try {
        $stmt = $pdo->prepare('INSERT INTO Profile
        (first_name, last_name, email, headline, summary, user_id)
         VALUES ( :fn, :ln, :em, :he, :su, :uid)');
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
        $profile_id = $pdo->lastInsertId();
        $rank = 1;
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
                ':pid' => $profile_id,
                ':rank' => $rank,
                ':year' => $year,
                ':desc' => $description,
            
                )
            );
            $rank++;
        }
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
    <?php include 'head.php'; ?>
</head>
<body>
    <div class="container">
        <h1>Adding Profile for <?= $_SESSION['name'] ?></h1>
        <?php
           flashMessages();
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
            Position: <input type="submit" id="addPos" value="+">
        </p>
        <div id="position_fields">
        </div>
        <p>
        <input type="submit" value="Add">
        <input type="submit" name="cancel" value="Cancel">
        </p>
        </form>
    </div>
    <script>
    countPos = 0;
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
