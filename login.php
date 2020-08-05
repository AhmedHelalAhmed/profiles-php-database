<?php
session_start();
require_once "pdo.php";
require_once "util.php";

if (isset($_POST['cancel'])) {
    header("Location: index.php");
    return;
}

$salt = 'XyZzy12*_';


if (isset($_POST['email']) && isset($_POST['pass'])) {
    unset($_SESSION['name']);
    unset($_SESSION['user_id']);
    if (strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1) {
        $_SESSION['error'] = "All fields are required";
        header("Location: login.php");
        return;
    } elseif (strpos($_POST['email'], '@')===false) {
        $_SESSION['error'] = "Email address must contain @";
        header("Location: login.php");
        return;
    } else {
        $check = hash('md5', $salt.$_POST['pass']);
        $stmt = $pdo->prepare('SELECT user_id, name FROM users
        WHERE email = :em AND password = :pw');
        $stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);


        if ($row !== false) {
            error_log("Login success ".$_POST['email']);
            $_SESSION['name'] = $row['name'];
            $_SESSION['user_id'] = $row['user_id'];
            header("Location: index.php");
            return;
        } else {
            error_log("Login fail ".$_POST['email']." $check");
            $_SESSION['error'] = "Incorrect password";
            header("Location: login.php");
            return;
        }
    }
}


?>
<!DOCTYPE html>
<html>
<head>
<title>Ahmed Helal Ahmed's Login Page</title>
<?php include 'head.php'; ?>

</head>
<body>
<div class="container">
<h1>Please Log In</h1>
<?php

flashMessages();

?>
<form method="POST">
<label for="email">Email</label>
<input type="text" name="email" id="email"><br/>
<label for="id_1723">Password</label>
<input type="text" name="pass" id="id_1723"><br/>
<input onclick="return doValidate();" type="submit" value="Log In">
<input type="submit" name="cancel" value="Cancel">
</form>
<p>
For a password hint, view source and find a password hint
in the HTML comments.
<!-- Pw is php123 -->
</p>
</div>
<script>

function doValidate() {
    console.log('Validating...');
    try {
        email = document.getElementById('email').value;
        password = document.getElementById('id_1723').value;
        if (email == null || email == "" || password == null || password == "") {
            alert("Both fields must be filled out");
            return false;
        }
        if ( email.indexOf('@') == -1 ) {
            alert("Invalid email address");
            return false;
        }
        return true;
    } catch(e) {
        return false;
    }
    return false;
}

</script>
</body>
