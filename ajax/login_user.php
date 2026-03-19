<?php
    session_start();
    include("../settings/connect_datebase.php");
    include("mail.php"); 

    $login = $mysqli->real_escape_string($_POST['login']);
    $password = $_POST['password'];

    if (empty($login) || empty($password)) {
        die("empty_fields");
    }

    $query = "SELECT * FROM `users` WHERE `login` = '$login'";
    $result = $mysqli->query($query);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            
            $auth_code = rand(100000, 999999);
            
            $update_query = "UPDATE `users` SET `auth_code` = '$auth_code' WHERE `id` = " . $user['id'];
            $mysqli->query($update_query);

			$_SESSION['preuser'] = $user['id']; 
            $_SESSION['temp_login'] = $login; 
            $_SESSION['mail'] = $login; 

            $subject = "Подтверждение входа";
            $message = "
                <html>
                <head><title>Код авторизации</title></head>
                <body>
                    <h2>Ваш код для входа в систему: <span style='color: #2c3e50;'>$auth_code</span></h2>
                    <p>Если вы не запрашивали этот код, просто проигнорируйте письмо.</p>
                </body>
                </html>
            ";

            if (SendMail($login, $subject, $message)) {
                echo "need_code";
            } else {
                echo "mail_error";
            }

        } else {
            echo "error_auth";
        }
    } else {
        echo "error_auth";
    }

    $mysqli->close();
?>