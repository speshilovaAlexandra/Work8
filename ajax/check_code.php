<?php
    session_start();
    include("../settings/connect_datebase.php");

    $user_code = isset($_POST['code']) ? trim($_POST['code']) : '';

    if (!isset($_SESSION['mail'])) {
        die("session_expired");
    }

    $login = $_SESSION['mail'];

    $query = "SELECT id, auth_code FROM `users` WHERE `login` = '$login'";
    $result = $mysqli->query($query);
    $user_data = $result->fetch_assoc();

    if ($user_data) {
        $db_code = $user_data['auth_code'];
        $user_id = $user_data['id'];

        if ($user_code != "" && $user_code == $db_code) {
            $_SESSION['user'] = $_SESSION['preuser'];
            $token = bin2hex(random_bytes(32));
            $mysqli->query("UPDATE `users` SET `token` = '$token' WHERE `id` = ". $_SESSION['user']);
            $_SESSION['token'] = $token;

            session_regenerate_id();
            $current_session_id = session_id();

            $update_query = "UPDATE `users` SET 
                             `session_id` = '$current_session_id', 
                             `auth_code` = NULL 
                             WHERE `id` = '$user_id'";
            
            if ($mysqli->query($update_query)) {
                $_SESSION['user'] = $user_id;
                
                unset($_SESSION['mail']);
                unset($_SESSION['code']);
                unset($_SESSION['preuser']);

                echo "success";
            } else {
                echo "db_error";
            }

        } else {
            echo "wrong_code";
        }
    } else {
        echo "user_not_found";
    }

    $mysqli->close();
?>
