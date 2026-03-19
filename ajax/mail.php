<?php
    function SendMail($to, $subject, $message) {
        $to = filter_var(trim($to), FILTER_VALIDATE_EMAIL);
        if (!$to) {
            error_log("Invalid email address: $to");
            return false;
        }

        $headers  = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type: text/html; charset=utf-8" . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        $encoded_subject = "=?utf-8?B?" . base64_encode($subject) . "?=";

        return mail($to, $encoded_subject, $message, $headers);
    }

?>