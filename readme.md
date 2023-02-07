# Database

> really cliche name for something any php user would use

## Create an instance
```php
$host = "localhost";
$username = "root";
$password = "";
$database_name = "";

$db = new Database($host, $username, $password, $database_name);  

```

## Make queries
```php
$query = "SELECT * FROM $some_table WHERE $some_row = $some_col";
$db->run_sql($query);
```

## Check the connection at any point
```php
$some_db = "database1";
$db->check_connection($some_db);
// uses $db->database_name by default
```

## Check existence of a database
```php
$some_db = "database1";
$db->database_exists($some_db);
// uses $db->database_name by default
```

## Check existence of a table
```php
$some_db = "table1";
$db->table_exists($some_db);
// uses $db->table_name by default
```

thats all for now