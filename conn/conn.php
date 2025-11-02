<?php


namespace conn;
define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASS", "");
define("DB_NAME", "cassowary_db");

//setting error if not connection

/**
 * Open a connection to the MySQL database
 *
 * @return \mysqli|null
 */

function openDatabaseConnection(): \mysqli|null
{
    
    // $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    try {
        $conn = new \mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        $conn->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        return $conn;
    } catch (\mysqli_sql_exception $e) {
         echo "test on conn odbc()";
        return null;
    }

}
/**
 * close connection
 * @return void 
 * 
 */
function closeDatabaseConnection($conn)
{
    if ($conn) {
        $conn->close();
    }
}


?>
