<?php

/**
 * This script creates two constants that all other scripts can use.
 * Both of them define absolute paths for the application in the current environment.
 *
 * - ROOT_PATH. The root path of the application. It is a file system path
 *   (e.g., "C:/xampp/htdocs/php_company_employees", "var/www/html/php_company_employees")
 * 
 * - BASE_URL. The base URL of the application. It is a web URL
 *   (e.g., "php_company_employees")
 */

// In case it is a Windows OS, the backslash must be replaced by a slash.
// Specifying the backslash twice is necessary, as the backslash is the escape character
define('ROOT_PATH', str_replace('\\', '/', __DIR__));

// Web server's document root ("C:\xampp\htdocs", "var/www/html" or similar)
$documentRoot = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']));

// The base URL is the present script path minus the document root
$baseUrl = str_replace($documentRoot, '', ROOT_PATH);

// As it is an absolute path, it must start with a slash.
// If it already starts with a slash, ltrim removes it before it gets added again
define('BASE_URL', '/' . ltrim($baseUrl, '/'));