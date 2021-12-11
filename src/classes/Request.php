<?php
/**
 * Validates and returns id and table name from GET and POST array
 */
class Request
{
    /**
     * Returns the id from a GET or POST request as an integer
     * Returns FALSE if no valid id is found
     * An id must be an integer string value larger than 0
     */
    public function getID(): int|bool  
    {

        if ($_SERVER['REQUEST_METHOD'] === "GET" && $this->isValidID($_GET['id'] ?? FALSE)) {

            return (int)$_GET['id'];
        } else if ($_SERVER['REQUEST_METHOD'] === "POST" && $this->isValidID($_POST['id'] ?? 0)) {

            return (int)$_POST['id'];
        } else {

            return FALSE;
        }
    }

    /**
     * Helper function for getID
     */
    private function isValidID(string|bool $value): bool
    {

        //mind that id from $_GET is a string, not an int.
        if ((ctype_digit($value)) && (int)$value > 0) {

            return TRUE;
        }

        return FALSE;
    }

    /**
     * Returns the table name from a GET or POST request as a string
     * Returns FALSE if no valid table name is found
     * A table name must be lowercase alphanumeric and may include underscores
     * It can't start with a number and can't end with an underscore.
     */
    public function getTable(): string|bool
    {

        //NB: $this->isValidTable($_GET['t'] ?? FALSE is a shortcut for a check on isset()
        if ($_SERVER['REQUEST_METHOD'] === "GET" && $this->isValidTable($_GET['t'] ?? FALSE)) {

            return $_GET['t'];
        } else if ($_SERVER['REQUEST_METHOD'] === "POST" && $this->isValidTable($_POST['t'] ?? FALSE)) {

            return $_POST['t'];
        }

        return FALSE;
    }

    /**
     * Helper function for getTable
     */
    private function isValidTable(string|bool $table): bool
    {
        //only accept lowercase characters, numbers and underscore
        //tablename can't start with a number or end with an underscore
        if (preg_match("/^[a-z_][A-Za-z0-9_]+[a-z0-9]$/", $table) && $table !== FALSE) {

            return TRUE;
        }

        return FALSE;
    }
}
