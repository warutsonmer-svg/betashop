XY STORE - PHP template with Discord OAuth2 login
-----------------------------------------------
How to use:
1. Import db.sql into your MySQL (or run the SQL to create database + table).
2. Put this folder into your PHP webroot (e.g., htdocs/betashop or /var/www/html/betashop).
3. Edit config.php: set DB credentials and DISCORD_CLIENT_ID, DISCORD_CLIENT_SECRET, DISCORD_REDIRECT_URI.
   - In Discord Developer Portal -> OAuth2 -> add redirect: http://localhost/betashop/callback.php
4. Access http://localhost/betashop/ and click Login with Discord.
5. After login, profile avatar + username + points will appear on the top-right.
