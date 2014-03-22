<?php

/**
 * Provides all database connection related functionality.
 */
class Db {

    /**
     * Returns a standard mysql database connection for queries.
     */
  public static function getDbConnection() {
    $database['host'] = "localhost";
    $database['username'] = "pycdorg_app";
    $database['password'] = "pycd049t4qjj8ht";
    $database['dbname'] = "pycdorg_competitions_test";

    $con = new PDO(
              'mysql:host='.$database['host'].';'.
              'dbname='.$database['dbname'].';'.
              'charset=utf8',
              $database['username'],
              $database['password']);

    if (!$con) {
        die('Could not connect: ' . mysql_error());
    }

    return $con;
  }//close getDbConnection

}// close Db
