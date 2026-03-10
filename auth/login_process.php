<?php
require_once "../core/auth.php";

if($_SERVER["REQUEST_METHOD"] !== "POST"){
    header("Location: login.php");
    exit();
}

$auth = new auth();
if(isset($_POST["login"])){

    $names = $_POST["email"];
    $password = $_POST["password"];

    if($auth->Login($names, $password)){
        header("location: /utswebpro/index.php");
    } else{
        echo "Login Gagal";
    }
var_dump($_POST);
}