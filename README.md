# chamelion
PHP Helpers and Libraries | Security

/**
 * Disable Unnecessary Methods
 */
RewriteEngine On
RewriteCond %{REQUEST_METHOD} !^(GET|POST|PUT)
RewriteRule .* - [R=405,L]
RewriteCond %{REQUEST_METHOD} ^(TRACE|OPTIONS|HEAD) 
RewriteRule .* - [F]



/**
 * Sensitive HTTP Header Info
 */
# On php.ini
expose_php = Off

# As global declaration
header_remove("X-Powered-By"); //--> on php main index file
header("X-XSS-Protection: 0");  //--> on php main index file

# Server Info in Header
# On apache httpd.conf
ServerTokens ProductOnly
ServerSignature Off



/**
 * Laravel Rewrite Route URL
 */
Options +FollowSymLinks
RewriteEngine On

RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [L]


/**
 * CodeIgniter URL Rewrite
 */
RewriteEngine on

RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule .* index.php/$0 [PT,L]



/**
 * References
 * 
 * Multicurl
 * http://wolf-et.ru/php/curl-vs-sockets-vs-file_get_contents-vs-multicurl/
 * 
 * 
 */
