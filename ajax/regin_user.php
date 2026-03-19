<?php
    session_start();
    include("../settings/connect_datebase.php");
    
    $login = $mysqli->real_escape_string($_POST['login']);
    $password = $_POST['password'];

    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/', $password)) {
        die("weak_password");
    }
    
    $check_user = $mysqli->query("SELECT `id` FROM `users` WHERE `login`='$login'");
    
    if($check_user->num_rows > 0) {
        echo "-1";
    } else {

        $hash = password_hash($password, PASSWORD_BCRYPT);
        
        $insert = $mysqli->query("INSERT INTO `users`(`login`, `password`, `roll`) VALUES ('$login', '$hash', 0)");
        
        if ($insert) {
            $new_id = $mysqli->insert_id; 
            $_SESSION['user'] = $new_id;  
            echo $new_id;                
        } else {
            echo "db_error";
        }
    }
?>