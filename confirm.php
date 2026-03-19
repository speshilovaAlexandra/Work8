<?php
    session_start();
    include("./settings/connect_datebase.php");

    if (isset($_SESSION['user']) && $_SESSION['user'] != -1) {
        $user_query = $mysqli->query("SELECT * FROM `users` WHERE `id` = ".$_SESSION['user']);
        if($user_read = $user_query->fetch_row()) {
            if($user_read[3] == 0) header("Location: user.php");
            else if($user_read[3] == 1) header("Location: admin.php");
            exit;
        }
    } 
    else if (!isset($_SESSION['mail'])) {
        header("Location: login.php");
        exit;
    } 
    else {
        if (!isset($_SESSION['code'])) {
            $code = rand(100000, 999999);
            $_SESSION['code'] = $code;
            
            mail($_SESSION['mail'], "Код подтверждения", "Ваш код: ".$code);
        }
    }
?>
<html>
    <head> 
        <meta charset="utf-8">
        <title> Подтверждение авторизации </title>
        <script src="https://code.jquery.com/jquery-1.8.3.js"></script>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <div class="top-menu">
            <a href=#><img src = "img/logo1.png"/></a>
            <div class="name">
                <a href="index.php">
                    <div class="subname">БЕЗОПАСНОСТЬ  ВЕБ-ПРИЛОЖЕНИЙ</div>
                    Пермский авиационный техникум им. А. Д. Швецова
                </a>
            </div>
        </div>
        <div class="space"> </div>
        <div class="main">
            <div class="content">
                <div class = "login">
                    <div class="name"> Подтверждение авторизации </div>
                
                    <div class = "sub-name">Код отправленный на почту:</div>
                    <div style="color: gray; font-size: 12px; margin-bottom: 10px;">
                        <?php echo $_SESSION['mail']; ?>
                    </div>
                    
                    <input name="_code" type="text" placeholder="000000" onkeypress="return PressToEnter(event)"/>
                    
                    <input type="button" style="margin-top: 10px" class="button" value="Войти" onclick="LogIn()"/>
                    <img src = "img/loading.gif" class="loading" style="display:none;"/>
                </div>
                
                <div class="footer">
                    © КГАПОУ "Авиатехникум", 2020
                    <a href=#>Конфиденциальность</a>
                    <a href=#>Условия</a>
                </div>
            </div>
        </div>
        
        <script>
            function LogIn() {
                var loading = document.getElementsByClassName("loading")[0];
                var button = document.getElementsByClassName("button")[0];
                var _code = document.getElementsByName("_code")[0].value;
                
                if(_code == "") return;

                loading.style.display = "block";
                button.className = "button_diactive";
                
                var data = new FormData();
                data.append("code", _code);
                
                // AJAX запрос
                $.ajax({
                    url         : 'ajax/check_code.php',
                    type        : 'POST', 
                    data        : data,
                    cache       : false,
                    dataType    : 'html',
                    processData : false,
                    contentType : false, 
                    success: function (_data) {
                        _data = _data.trim();
                        if(_data === "success") {
                            // Перезагружаем страницу. PHP сам увидит сессию и перекинет в user.php
                            location.reload(); 
                        } else {
                            alert("Неверный код!");
                            loading.style.display = "none";
                            button.className = "button";
                        }
                    },
                    error: function() {
                        alert('Системная ошибка!');
                        loading.style.display = "none";
                        button.className = "button";
                    }
                });
            }
            
            function PressToEnter(e) {
                if (e.keyCode == 13) {
                    var _code = document.getElementsByName("_code")[0].value;
                    if(_code != "") {
                        LogIn();
                    }
                }
            }
        </script>
    </body>
</html>