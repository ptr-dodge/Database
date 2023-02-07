<?php

class Database
{
    // define public variables that can be changed
    public string $host = "localhost";
    public string $username = "root";
    public string $password = "";
    public string $database_name = "";
    public string $table_name = "";
    public bool $verbose = false;
    public bool $use_echo_for_errors = false;
    public $connection;

    function __construct($host, $username, $password, $database_name, $table_name)
    {
        // take in arguments and set them to the public variables
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->database_name = $database_name;
        $this->table_name = $table_name;

        // connect to mySQL server
        $this->connect();

        // Check if the given database doesnt exist
        if (!$this->database_exists($this->database_name)) {
            // if it doesnt exist, create it
            $this->run_sql("CREATE DATABASE $this->database_name");

            // reset connection to include the database
            $this->connection = new mysqli($this->host, $this->username, $this->password, $this->database_name);
        } else {
            // database is already created, connect to it
            $this->connection = new mysqli($this->host, $this->username, $this->password, $this->database_name);
        }
    }

    private function console_log($error_message)
    {
        // just a convenience function, $this->verbose is false by default
        if ($this->verbose) {
            // if you cant understand an if statement, im not explaining
            if ($this->use_echo_for_errors) {
                echo $error_message;
            } else {
                echo '<script>console.log("' . $error_message . '")</script>';
            }
        }
    }

    function connect()
    {
        $this->connection = new mysqli($this->host, $this->username, $this->password);
        $this->check_connection();
    }

    function check_connection()
    {
        if ($this->connection->connect_error) {
            $this->console_log("Connection failed");
        } else {
            $this->console_log("Connected to mysql");
        }
    }

    function run_sql(string $sql)
    {
        if ($this->connection->query($sql) === TRUE) {
            $this->console_log("SQL ran successfully");
        } else {
            $this->console_log("Error running SQL");
        }
    }

    function database_exists($db_name)
    {
        if (!isset($db_name)) {
            $db_name = $this->database_name;
        }

        $check_for_database = $this->connection->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$db_name'");

        if ($check_for_database->num_rows) {
            $this->console_log("Database exists");
            return true;
        } else {
            $this->console_log("No database");
            return false;
        }
    }

    function table_exists($database, $table)
    {
        if (!isset($database)) {
            $database = $this->database_name;
        }

        if (!isset($table)) {
            $table = $this->table_name;
        }

        $table_exists = $this->connection->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '$database' AND TABLE_NAME = '$table'");

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
