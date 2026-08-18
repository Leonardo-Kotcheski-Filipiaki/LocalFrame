<?php

namespace Core\Essentials;

use Core\Essentials\Enviroment;
use Exception;
use PDO;

class Database
{
    /**
     * DATABASE TYPE
     */
    private static string $DATABASE = "";
    /**
     * DATABASE NAME
     */
    private static string $DATABASE_NAME = "";
    /**
     * DATABASE HOST
     */
    private static string $HOST = "";
    /**
     * DATABASE HOST PORT
     */
    private static string $PORT = "";
    /**
     * DATABASE USERNAME
     */
    private static string $USERNAME = "";
    /**
     * DATABASE PASSWORD
     */
    private static string $PASSWORD = "";
    /**
     * DATABASE CHARSET
     */
    private static string $CHARSET = "utf8mb4";
    /**
     * PDO OBJECT
     */
    private static PDO|null $conn = null;
    /**
     * Query
     */
    private static string $QUERY = "";
    /**
     * Marks if the connection was used or not
     * @var bool|null
     */
    public static bool|null $USE_DATABASE = null;

    private function __construct() {}

    public static function instance() : PDO|null
    {
        if (self::$conn === null) {
            self::construirConexao();
        }
        return self::$conn;
    }

    public static function getUseDatabase() : bool
    {
        return self::$USE_DATABASE ?? (new Enviroment())->get("USE_DATABASE");
    }

    /**
     * Construir a conexão com o banco de dados e retornar o objeto PDO
     * @return PDO
     */
    private static function construirConexao() : PDO|null
    {
        $env = new Enviroment();
        if ($env->get("USE_DATABASE") == false) {
            self::$USE_DATABASE = false;
            return null;
        }
        self::$USE_DATABASE = true;
        self::$DATABASE = $env->get("DATABASE") ?? "";
        self::$DATABASE_NAME = $env->get("DATABASE_NAME") ?? "";
        self::$HOST = $env->get("HOST") ?? "";
        self::$PORT = $env->get("PORT") ?? "";
        self::$USERNAME = $env->get("USERNAME") ?? "";
        self::$PASSWORD = $env->get("PASSWORD") ?? "";
        self::$CHARSET = $env->get("CHARSET") ?? "utf8mb4";

        try {
            self::$conn = new PDO(
                self::$DATABASE . ":host=" . self::$HOST . ";port=" . self::$PORT . ";dbname=" . self::$DATABASE_NAME . ";charset=" . self::$CHARSET,
                self::$USERNAME,
                self::$PASSWORD
            );
            self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            self::$conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            return self::$conn;
        } catch (Exception $ex) {
            ExceptionHandler::handle($ex);
            return null;
        }
    }

    /**
     * Down here is a list of functions to build a query
     */
    public static function select(string|array $column)
    {
        if (is_array($column)) {
            $column = implode(", ", $column);
        }
        self::$QUERY .= "SELECT $column ";
    }

    public static function from(string $table)
    {
        self::$QUERY .= "FROM $table ";
    }

    public static function where(string $condition)
    {
        self::$QUERY .= "WHERE $condition ";
    }

    public static function orderByDesc(string $column)
    {
        self::$QUERY .= "ORDER BY $column DESC ";
    }

    public static function orderByAsc(string $column)
    {
        self::$QUERY .= "ORDER BY $column ASC ";
    }

    public static function groupBy(string $column)
    {
        self::$QUERY .= "GROUP BY $column ";
    }

    public static function having(string $condition)
    {
        self::$QUERY .= "HAVING $condition ";
    }

    public static function limit(int $limit)
    {
        self::$QUERY .= "LIMIT $limit ";
    }
    
    /**
     * Get all data from database
     * @return array|null
     */
    public static function get() : array|null
    {
        $result = null;
        try {
            $stmt = self::instance()->prepare(trim(self::$QUERY));
            $stmt->execute();
            self::$QUERY = '';
            $result = $stmt->fetchAll();
        } catch (\Throwable $th) {
            ExceptionHandler::handle($th);
            self::$QUERY = '';
        }
        return $result;
    }

    /**
     * Insert data into a table
     * @param string $table The table name
     * @param array $data Associative array of column => value
     * @return bool
     */
    public static function insert(string $table, array $data): bool
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        try {
            $stmt = self::instance()->prepare($sql);
            return $stmt->execute(array_values($data));
        } catch (\Throwable $th) {
            ExceptionHandler::handle($th);
            return false;
        }
    }

    /**
     * Update data in a table
     * @param string $table The table name
     * @param array $data Associative array of column => value
     * @param string $whereCondition The where clause condition
     * @param array $whereBindings Bindings for the where clause
     * @return bool
     */
    public static function update(string $table, array $data, string $whereCondition, array $whereBindings = []): bool
    {
        $setClause = implode(' = ?, ', array_keys($data)) . ' = ?';
        $sql = "UPDATE $table SET $setClause WHERE $whereCondition";
        
        $bindings = array_merge(array_values($data), $whereBindings);
        try {
            $stmt = self::instance()->prepare($sql);
            return $stmt->execute($bindings);
        } catch (\Throwable $th) {
            ExceptionHandler::handle($th);
            return false;
        }
    }

}