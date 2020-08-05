<?php

session_start();

require_once "pdo.php";
require_once "util.php";


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
    <?php include 'head.php'; ?>

</head>
<body>
    <div class="container">
        <h1>Ahmed Helal Ahmed's Resume Registry</h1>

        <?php

        flashMessages();

        if (!isset($_SESSION['name'])) {
            echo ' <p><a href="login.php">Please log in</a></p>';
        } else {
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
    </div>
</body>
</html>