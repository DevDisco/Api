<?php
/**
 * Validates and returns id and table name from GET and POST array
 */
class Request
{
    /**
     * Returns the id from a GET or POST request as an integer
     * Returns false if no valid id is found
     * An id must be an integer string value larger than 0
     */
    public function getID(): int|bool  
    {

        if ($_SERVER['REQUEST_METHOD'] === "GET" && $this->isValidID($_GET['id'] ?? false)) {

            return (int)$_GET['id'];
        } else if ($_SERVER['REQUEST_METHOD'] === "POST" && $this->isValidID($_POST['id'] ?? 0)) {

            return (int)$_POST['id'];
        } else {

            return false;
        }
    }

    /**
     * Helper function for getID
     */
    private function isValidID(string|bool $value): bool
    {

        //mind that id from $_GET is a string, not an int.
        if ((ctype_digit($value)) && (int)$value > 0) {

            return true;
        }

        return false;
    }

    /**
     * Returns the table name from a GET or POST request as a string
     * Returns false if no valid table name is found
     * A table name must be lowercase alphanumeric and may include underscores
     * It can't start with a number and can't end with an underscore.
     */
    public function getTable(): string|bool
    {

        //NB: $this->isValidTable($_GET['t'] ?? false is a shortcut for a check on isset()
        if ($_SERVER['REQUEST_METHOD'] === "GET" && $this->isValidTable($_GET['t'] ?? false)) {

            return $_GET['t'];
        } else if ($_SERVER['REQUEST_METHOD'] === "POST" && $this->isValidTable($_POST['t'] ?? false)) {

            return $_POST['t'];
        }

        return false;
    }

    /**
     * Helper function for getTable
     */
    private function isValidTable(string|bool $table): bool
    {
        //only accept lowercase characters, numbers and underscore
        //tablename can't start with a number or end with an underscore
        if (preg_match("/^[a-z_][A-Za-z0-9_]+[a-z0-9]$/", $table) && $table !== false) {

            return true;
        }

        return false;
    }
}
