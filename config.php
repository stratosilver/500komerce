<?php
// Display of the PHP errors on the pages (classes/Security.php). false in production: the messages give the
// paths of the files and the queries to the visitors. The errors always go to the log of PHP.
// On your own computer (WampServer) you can set it to true while developing.
define('APP_DEBUG', false);

// DB parameters: SQLite database file (schema: _dev/500komerce_sqlite.sql)
// The web server must be able to write in the file AND in its folder (SQLite writes its journal next to the file)
define('DB_FILE', __DIR__.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'500komerce.sqlite');

//define('BASE_URL', str_replace('/index.php', '', strtok($_SERVER["REQUEST_URI"], '?')));
define('BASE_URL', '');

// Authentication (components/user)
// Public url of the site, used in the links of the emails (password reset, access code) and as return address
// of Google, Facebook, X, ex: 'https://www.example.com'. REQUIRED IN PRODUCTION: when it is empty, the links are
// only made on a development host (localhost, *.test, *.local), because the Host header of the request can be
// changed by anybody (an attacker would receive the password reset links).
define('APP_URL', '');
// Sender of the emails, ex: 'no-reply@example.com'
define('MAIL_FROM', '');
// DEVELOPMENT ONLY: display the links and the codes sent by email on the page (password reset, access code).
// Even when true, they are only displayed to the requests made from the server itself (127.0.0.1, ::1):
// leave it false outside of development.
define('AUTH_SHOW_RESET_LINK', false);

// Sign in with Google, Facebook, X. A provider is displayed on the login page when its client_id and client_secret are filled.
// Redirect / callback url to declare at each provider: {APP_URL}/login/callback
define('OAUTH_PROVIDERS', array(
    // https://console.cloud.google.com/apis/credentials  (OAuth client ID, type "Web application")
    'google'   => array('client_id' => '1234', 'client_secret' => '1234'),
    // https://developers.facebook.com/apps  (product "Facebook Login", App ID / App secret)
    'facebook' => array('client_id' => '1234', 'client_secret' => '1234'),
    // https://developer.x.com  (User authentication settings: OAuth 2.0, "Web App", "Request email from users" enabled)
    'x'        => array('client_id' => '1234', 'client_secret' => '1234'),
));

// Permissions (classes/Auth.php). Each user has a level from 0 to 100, 0 by default: consultation pages and own profile only
// Minimum level to add, edit and delete in all the components, except the users
define('PERMISSION_EDIT', 50);
// Minimum level to manage the users and their permissions
define('PERMISSION_ADMIN', 100);

// Shop (components/shop). The prices of the products are with tax (TVA comprise), in cents
// Rate of the VAT, in percent: edited in the back office (Paramètres, table config). This value is only used
// when the table config has no rate
define('TAX_RATE', 21);
// Symbol displayed after the amounts
define('CURRENCY', '&euro;');

// Illustration displayed for a product that has no image (component productmedia): %1$d = id of the product,
// %2$d = width, %3$d = height. The same product always gets the same picture. Empty = a grey tile.
// Empty: no more random pictures from picsum.photos, a product without image shows the grey tile with an icon.
// Former value: 'https://picsum.photos/seed/product-%1$d/%2$d/%3$d'
define('PRODUCT_ILLUSTRATION_URL', '');

// API (api.php)
// Secret token of the programs calling the API: header "Authorization: Bearer <token>".
// Empty = no access by token, only the session of a logged user. Use a long random value, ex: bin2hex(random_bytes(32))
define('API_TOKEN', '');
