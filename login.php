<?php
require 'config.php';

$client_id = DISCORD_CLIENT_ID;
$redirect = DISCORD_REDIRECT_URI;
$scope = urlencode('identify email');
$prompt = 'consent';

$authorize = "https://discord.com/api/oauth2/authorize?response_type=code&client_id={$client_id}&scope={$scope}&redirect_uri=" . urlencode($redirect) . "&prompt={$prompt}";
header('Location: ' . $authorize);
exit;
?>
