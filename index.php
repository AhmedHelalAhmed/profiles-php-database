<?php

session_start();

require_once "pdo.php";

try {
    $stmt = $pdo->query("SELECT * FROM Profile");
    $profiles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $ex) {
    echo("Internal error, please contact support");
    error_log("SQL error=" . $ex->getMessage());
    return;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ahmed Helal Ahmed</title>
</head>
<body>
<h1>Ahmed Helal Ahmed's Resume Registry</h1>

<?php

if (isset($_SESSION['error'])) {
    echo '<p style="color:red">'. $_SESSION['error'] ."</p>\n";
    unset($_SESSION['error']);
}

if (isset($_SESSION['success'])) {
    echo('<p style="color: green;">' . $_SESSION['success'] . "</p>\n");
    unset($_SESSION['success']);
}

if (!isset($_SESSION['name'])) {
    echo ' <p><a href="login.php">Please log in</a></p>';
}

if (isset($_SESSION['name'])) {
    echo '<p><a href="logout.php">Logout</a></p>';
}

if (count($profiles)) {
    echo('<table border="1">' . "\n");
    echo('
    <thead>
    <tr>
    
    ');


    echo('
    <th>Name</th>
    <th>Headline</th>

    ');
    if (isset($_SESSION['user_id'])) {
        echo('
    <th>Action</th>
    ');
    }
    echo('
    </tr>
    </thead>
    <tbody>
    ');
    foreach ($profiles as $profile) {
        echo('<tr>');
        echo('<td>');
        echo('<a href="view.php?profile_id=' . $profile['profile_id'] . '">');
        echo(htmlentities($profile['first_name']).' '.htmlentities($profile['last_name']));
        echo('</a>');
        echo('</td>');
        echo('<td>' . htmlentities($profile['headline']) . '</td>');
        if (isset($_SESSION['user_id'])) {
            echo('<td>');
            echo('<a href="edit.php?profile_id=' . $profile['profile_id'] . '">Edit</a>');
            echo(' / ');
            echo('<a href="delete.php?profile_id=' . $profile['profile_id'] . '">Delete</a>');
            echo('</td>');
        }
        echo('</tr>');
    }
    echo('</tbody>');
    echo('</table>');
} else {
    echo("No rows found");
}


if (isset($_SESSION['name'])) {
    echo '<p><a href="add.php">Add New Entry</a></p>';
}

?>
</body>
</html>