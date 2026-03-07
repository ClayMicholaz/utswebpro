<?php
require_once "../core/auth.php";

$auth = new auth();
if(isset($_POST["login"])){
    if($auth->Login($_POST["username"], $_POST["password"])){
        header("location: /utswebpro/dashboard.php");
    } else{
        echo "Login Gagal";
    }
}