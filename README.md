PHP-Reaper
==========
PHP tool to scan ADOdb code for SQL Injections

Why
===
Running PHP-Reaper is far less time consuming than running full fledged automated security scanner at your application. The web security scanner might not locate all possible SQL Injections vulnerabilities, because of hard to reach code from the UI (or needed the right conditions). PHP-Reaper is fast and pinpoints the exact line where the problem lies, scanning all your source code.

You'll get the most out of PHP-Reaper if you run it on every commit. It's made to be CI friendly. The exit code is 0, if all is OK, or -1 if a problem is found.

Examples
========

Because of laziness, pressure or just ignorance, php developers using ADOdb are making such mistakes.

Vulnerabe SQL query:

`$dbConn->GetRow(“SELECT * FROM users WHERE id = $user_id”)`

Correct SQL query:

`$dbConn->GetRow(“SELECT * FROM users WHERE id = ?”, array(‘$user_id’))`

Usage
=====
Recursively scan directory with php files:

`php-reaper -d directory_with_php_files`

or scan a single file:

`php-reaper -f single_file.php`
