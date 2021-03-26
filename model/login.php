<?php
include('../controller/PCom.php');
$app = new PCom();
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $pass = trim($_POST['pass']);
    $email = $_POST['email'];
    echo $app->loginUser($email, $pass);
}

?>