<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);

// DB parameters
define("DB_HOST" , '127.0.0.1');
define('DB_USER' , 'root');
define('DB_PASSWORD' , '');
define('DB_DB' , '500komerce');
define('DB_PORT', '');

//define('BASE_URL', str_replace('/index.php', '', strtok($_SERVER["REQUEST_URI"], '?')));
define('BASE_URL', '');

// Authentication (components/user)
// Public url of the site, used in the password reset emails, ex: 'https://www.example.com'. Empty = current host
define('APP_URL', '');
// Sender of the emails, ex: 'no-reply@example.com'
define('MAIL_FROM', '');
// DEVELOPMENT ONLY: display the reset link on the page instead of relying on the email. Set to false in production!
define('AUTH_SHOW_RESET_LINK', true);

// Sign in with Google, Facebook, X. A provider is displayed on the login page when its client_id and client_secret are filled.
// Redirect / callback url to declare at each provider: {APP_URL}/index.php?component=user&task=oauthcallback
define('OAUTH_PROVIDERS', array(
    // https://console.cloud.google.com/apis/credentials  (OAuth client ID, type "Web application")
    'google'   => array('client_id' => '1234', 'client_secret' => '1234'),
    // https://developers.facebook.com/apps  (product "Facebook Login", App ID / App secret)
    'facebook' => array('client_id' => '1234', 'client_secret' => '1234'),
    // https://developer.x.com  (User authentication settings: OAuth 2.0, "Web App", "Request email from users" enabled)
    'x'        => array('client_id' => '1234', 'client_secret' => '1234'),
));

// API (api.php)
// Secret token of the programs calling the API: header "Authorization: Bearer <token>".
// Empty = no access by token, only the session of a logged user. Use a long random value, ex: bin2hex(random_bytes(32))
define('API_TOKEN', '');
