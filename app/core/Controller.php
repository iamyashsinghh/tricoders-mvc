<?php
class controller
{
    public function model($model)
    {
        require_once __DIR__ . '/../../app/models/' . $model . '.php';
        return new $model();
    }
    public function view($view, $data = [])
    {
        require_once __DIR__ . '/../../app/views/' . $view . '.php';
    }
    public function loadConfig($data)
    {
        return [
            'api_key' => 'apikey',
        ];
    }
    public function yash_mailer($to, $subject, $msg)
    {
        $mail = new PHPMailer();
        $mail->SMTPDebug = 0;
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls';
        $mail->Host = "smtp.gmail.com";
        $mail->Port = 587;
        $mail->IsHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Username = "yashphpproject@gmail.com";
        $mail->Password = "mplbxowbvltvymwy";
        $mail->SetFrom("yashphpproject@gmail.com");
        $mail->Subject = $subject;
        $mail->Body = $msg;
        $mail->AddAddress($to);
        $mail->SMTPOptions = array('ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => false,
        ));
        if (!$mail->Send()) {
            echo $mail->ErrorInfo;
        } else {
            return 'Sent';
        }
    }

}
