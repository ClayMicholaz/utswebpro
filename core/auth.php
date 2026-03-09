<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . "/../config/database.php";

class auth extends database
{
    public function login($username, $password)
    {
        $stmt = $this->conn->prepare("SELECT * FROM tbl_users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user && password_verify($password, $user["password"])) {
            $_SESSION["username"] = $user["username"];
            $_SESSION["role"] = $user["role"];
            return true;
        }
        return false;
    }
    public function change_password($username, $new_password)
    {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("UPDATE tbl_users SET password = ? WHERE username = ?");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("ss", $hashed_password, $username);
        if (!$stmt->execute()) {
            return false;
        }
        $affected = $stmt->affected_rows;
        $stmt->close();
        return $affected > 0;
    }
    public static function login_check()
    {
        if (!isset($_SESSION["username"])) {
            header("Location: /utswebpro/auth/login.php");
            exit();
        }
    }
}