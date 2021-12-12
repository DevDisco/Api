<?php

/**
 * Creates a connection to the database when instanciated.
 * Use run() to select one or more records from a table.
 */
class Database
{

    //do this with constructor property propagation in php 8.1
    public PDO $pdo;

    // This api is so small that I am 100% sure this is only called once
    // In a larger project I need to check if a PDO connection has been made already
    public function __construct(private Config $config, public SimpleError $error)
    {
        $dsn = $config->DB_DSN ?? '';
        $user = $config->DB_USER ?? '';
        $pwd = $config->DB_PWD ?? '';

        $this->pdo = new PDO($dsn, $user, $pwd);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
    }

    /**
     * Creates a SELECT statement and returns the results.
     * If $id is set to false the whole table will be returned,
     * otherwise the matching record.
     * It handles sql errors and tables you're not allowed to use.
     * Return values
     * - an array containing the requested record(s)
     * - false on error or empty result set
     */
    public function run(string|bool $table, int|bool $id = false): array|bool|string
    {
        if (!in_array($table, $this->config->ALLOWED_TABLES)) {

            $this->error->setError("You are not allowed to use this table.", 406);
            return false;
        }

        $result = $this->queryDb($table, $id);

        return $result;
    }

    /**
     * Helper function for run()
     */
    private function queryDb(string|bool $table, int|bool $id = false): array|bool
    {

        if ($id === false) {

            $sql = "SELECT * FROM $table";
            $stmt = $this->pdo->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {

            $sql = "SELECT * FROM $table WHERE id=:id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        $this->catchErrorsOrEmpty($result, $stmt);

        return $result;
    }

    public function read(string $sql, array $params = [],
        int $fetchMode = PDO::FETCH_ASSOC
    ): bool|array {

        $stmt = $this->prepareExecute($sql, $params);

        try {
            return $stmt->fetchAll($fetchMode);
        } catch (\PDOException $th) {
            Logger::toLog($th->getMessage());
            $this->error->setError("Database: fetch query failed.", $th->getCode());
            $this->error->showAndAbort();
        }
    }

    private function prepareExecute(string $sql, array $params = []): PDOStatement|bool
    {

        // /Logger::toLog($sql, "prepareExecute");

        try {
            $stmt = $this->pdo->prepare($sql);
        } catch (\PDOException $th) {
            Logger::toLog($th->getMessage());
            $this->error->setError("Database: prepare query failed.", $th->getCode());
            $this->error->showAndAbort();
        }

        try {
            $stmt->execute($params);
        } catch (\PDOException $th) {
            Logger::toLog($th->getMessage());
            $this->error->setError("Database: execute query failed.", $th->getCode());
            $this->error->showAndAbort();
        }

        return $stmt;
    }

    /**
     * Helper function for queryDb()
     */
    private function catchErrorsOrEmpty(array|bool $result, PDOStatement $stmt): void
    {

        if ($result === false) {

            $sql_error = $stmt->errorInfo();

            if (isset($sql_error[1])) {

                //something went wrong with the database and it's not the clients fault
                $this->error->setError($sql_error[1] . " - " . $sql_error[2], 500);
            } else {

                $this->error->setError("Database returned an empty result set.", 406);
            }
        }
    }
}
