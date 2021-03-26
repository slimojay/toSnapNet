<?php
include('../controller/PCom.php');
$app = new PCom();
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $fname = $_POST['fname'];
    $sname = $_POST['sname'];
    $pass = $_POST['pass'];
    $email = $_POST['email'];
    $name = $fname . ' ' . $sname;
    $url = '../views/login.php';
    $urlf = '../views/create_user.php';
    echo $app->createUser($name, $email, $pass, $url, $urlf);
}

?>