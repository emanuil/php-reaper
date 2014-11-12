php-reaper
==========

PHP tool to scan ADOdb code for SQL Injections

Why
===

Running PHP-Reaper is far less time consuming than running full fledged automated security scanner at your application. The web security scanner might not locate all possible SQL Injections vulnerabilities, because of hard to reach code from the UI (or needed the right conditions). PHP-Reaper is fast and pinpoints the exact line where the problem lies, scannin all your source code.

Examples
========

Vulnerabe SQL query: `$dbConn->GetRow(“SELECT * FROM users WHERE id = $user_id”)`

Correct SQL query: `$dbConn->GetRow(“SELECT * FROM users WHERE id = ?”, array(‘$user_id’))`
