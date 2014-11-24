PHP-Reaper
==========
PHP tool to scan [ADOdb](http://adodb.sourceforge.net) code for SQL Injections

Why
===
The main idea is to be able to detect problems as early as possible, when the code is fresh in your mind. Shift as much checks as possible to the left. Automate as much as possible. 

Running PHP-Reaper is far less time consuming than running full fledged automated security scanner at your application. The web security scanner might not locate all possible SQL Injections vulnerabilities, because of hard to reach code from the UI (or needs to set rare conditions). PHP-Reaper is fast and pinpoints the exact line where the problem lies, scanning all your PHP [ADOdb](http://adodb.sourceforge.net) source code.

You'll get the most out of PHP-Reaper if you run it on every commit. It's made to be CI friendly and fast.


Examples
========

Because of laziness, pressure or just ignorance, php developers using ADOdb are making such mistakes.

Vulnerable SQL query #1:
```php
$dbConn->GetRow("SELECT * FROM users WHERE id = $user_id");
```

Correct SQL query #1:
```php
$dbConn->GetRow("SELECT * FROM users WHERE id = ?", array(‘$user_id’));
```

Vulnerable SQL Query #2:
```php
$ids = join(',', $ids);
$dbConn->GetAll("SELECT * FROM campaigns WHERE id IN ({$ids})");
```

Correct SQL query #2:
```php
$dbConn->GetAll('SELECT * FROM campaigns WHERE FIND_IN_SET (id, ' . $dbConn->Param('') . ')', array(join(',', $ids)));
```

Usage
=====
Recursively scan directory with php files:

```bash
php php-reaper -d directory_with_php_files
```

or scan a single file:

```bash
php php-reaper -f single_file.php
```


Tests
=====
The tests are located in [tests](https://github.com/emanuil/php-reaper/tree/master/tests) directory. To run them, once in tests directory, type:
```bash
phpunit .
```
If you extend this tool, make sure that the tests are passing before submitting pull request. Better yet, add new test files and unit tests. Look at [example files](https://github.com/emanuil/php-reaper/tree/master/tests/SecurityChecks/exampleFiles) directory, what types of SQL Injections are detected.

Continuous Integration
======================
PHP-Reaper is CI friendly. On error it will exit with -1 status, so it's easy to hook it to your CI jobs.


Exclude from warnings
======================
You can ignore the warnings by PHP-Reaper, if you're absolutely sure that the code does not contain SQL Injection. Comment the line above the ADOdb function with:
```php
// safesql
$result_set = $dbConn->getAll('SELECT * FROM ' . Contracts_Contracts::DB_TABLE);
```
You need to be absolutely sure `Contracts_Contracts::DB_TABLE` cannot be controller by an attacker.


Dangerous Methods
=================
The following [ADOdb](http://adodb.sourceforge.net) methods are considered dangerous and are scanned for potential SQL
injections: getone(), getrow(), getall(), getcol(), getassoc(), execute(), replace(). Note that autoexecute() is immune,
because it automatically escapes all the parameters. If you have methods in your code base with the same names e.g.
execute() - non ADOdb method, you may see false positives. The solution is to rename your methods to be with names 
different than the default ADODb methods - e.g. executeTask(). PHP-Reaper is written in such a way because PHP is pretty
dynamic and static analysis cannot reliably tell us the class of the instantiated object. 
