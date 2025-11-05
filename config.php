<?php
if (!defined('CONFIG_LOADED')) {
    define('CONFIG_LOADED', true);

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // ✅ ค่า Database จาก InfinityFree
    define('DB_HOST', 'sql108.infinityfree.com'); // แก้จาก extrastore.kesug.com
    define('DB_USER', 'if0_39230885');
    define('DB_PASS', 'C6YaX16i9NEyY'); // รหัสผ่านในหน้าหลัก InfinityFree
    define('DB_NAME', 'if0_39230885_betashop');

    // ✅ เชื่อมต่อฐานข้อมูล
    $mysqli = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($mysqli->connect_errno) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status' => 'error',
            'message' => 'ไม่สามารถเชื่อมต่อฐานข้อมูลได้: ' . $mysqli->connect_error
        ]);
        exit;
    }

    // ✅ Discord Config
    define('DISCORD_CLIENT_ID', '1435534409045639188');
    define('DISCORD_CLIENT_SECRET', 'k3ekgVzIDA2-0bnP6UrEL9jtOzIGUF8_');
    define('DISCORD_REDIRECT_URI', 'https://extrastore.kesug.com/callback.php');
    define('DISCORD_WEBHOOK_TOPUP', 'https://discord.com/api/webhooks/1435716720768581713/DcJgf9da4FJaMAz3HItDO1T7dhBhR91Xyfv42Z6yfTqvBu7TTGMGxEO8cFNwoVnWorrA');

    $admin_ids = ['1244562809182752823'];
}
?>
