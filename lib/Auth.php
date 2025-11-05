<?php
class Auth {
    public static function loginWithDiscord() {
        // ตัวอย่าง mock login
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['user'] = ['id' => 1, 'username' => 'Admin']; 
    }

    public static function getUser() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        return $_SESSION['user'] ?? null;
    }

    public static function requireAdmin() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        global $admin_ids;

        if (!isset($_SESSION['user'])) {
            header("Location: ../login.php");
            exit;
        }

        $u = $_SESSION['user'];
        if (!in_array($u['id'], $admin_ids)) {
            die('<h2 style="color:white;text-align:center;margin-top:20%">⛔ ไม่มีสิทธิ์เข้าถึงหน้านี้</h2>');
        }
    }
}
