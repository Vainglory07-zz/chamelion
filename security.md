/**
 * Hide Sensitive HTTP Header Info
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
