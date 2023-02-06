<?php

class Database
{
    public string $host = "localhost";
    public string $username = "root";
    public string $password = "";
    public string $database_name = "";
    public string $table_name = "";
    public $connection;

    function __construct($host, $username, $password, $database_name, $table_name)
    {

        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->database_name = $database_name;
        $this->table_name = $table_name;

        $this->connect();

        // If database doesnt exist, create it
        if (!$this->database_exists($this->database_name)) {
            $this->run_sql("CREATE DATABASE $this->database_name");
        } else {
            // database is already created
            $this->connection = new mysqli($this->host, $this->username, $this->password, $this->database_name);
        }
    }

    function console_log($error_message)
    {
        echo '<script>console.log("' . $error_message . '")</script>';
    }

    function connect()
    {
        $this->connection = new mysqli($this->host, $this->username, $this->password);
        $this->check_connection();
    }

    function check_connection()
    {
        $connection = $this->connection;
        if ($connection->connect_error) {
            $this->console_log("Connection failed");
        } else {
            $this->console_log("Connected to mysql");
        }
    }

    function run_sql(string $sql)
    {
        $connection = $this->connection;
        if ($connection->query($sql) === TRUE) {
            $this->console_log("SQL ran successfully");
        } else {
            $this->console_log("Error running SQL");
            echo $connection->error;
        }
    }

    function database_exists($db_name)
    {
        $connection = $this->connection;
        $result = $connection->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$db_name'");

        if ($result->num_rows) {
            $this->console_log("Database exists");
            return true;
        } else {
            $this->console_log("No database");
            return false;
        }
    }

    function table_exists($database, $table)
    {
        $connection = $this->connection;
        $table_exists = $connection->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '$database' AND TABLE_NAME = '$table'");

        if ($table_exists->num_rows) {
            $this->console_log("Table exists");
            return true;
        } else {
            // attempt to create a the table
            $this->console_log("No Table... creating it");
            $create_table = "CREATE TABLE $this->table_name (
                ID VARCHAR(30) NOT NULL,
                USERNAME VARCHAR(30) NOT NULL,
                TIME TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )";

            $this->run_sql($create_table);
        }
    }
}
