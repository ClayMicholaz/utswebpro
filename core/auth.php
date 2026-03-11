<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . "/../config/database.php";

class auth extends database
{
    public function Login($email, $password)
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user && password_verify($password, $user["password"])) {
            $_SESSION["email"] = $user["email"];
            $_SESSION["role"] = $user["role"] ?? "user";
            return true;
        }
        return false;
    }
    public function change_password($email, $new_password)
    {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("ss", $hashed_password, $email);
        if (!$stmt->execute()) {
            return false;
        }
        $affected = $stmt->affected_rows;
        $stmt->close();
        return $affected > 0;
    }
    public static function login_check()
    {
        if (!isset($_SESSION["email"])) {
            header("Location: /utswebpro/auth/login.php");
            exit();
        }
    }
}