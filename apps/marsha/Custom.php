<?php




class Custom
{

    public function __construct(private Database $database)
    {
    }

    public function execute(string $table, int $id): bool|array
    {

        $category = $this->getCategory();

        if ($table === "werk" && $category) {

            return $this->getSelection($category);
        } else if ($table === "werk" && !$id) {

            return $this->getShowcases();
        } else if ($table === "werk" && $id > 0) {

            return $this->getItem($id);
        } else if ($table === "tekst" && !$id) {

            return $this->getText();
        } else if ($table === "agenda" && !$id) {

            return $this->getAgenda();
        } else if ($table === "agenda" && $id) {

            return $this->getAgendaItem($id);
        }
        else{
            return false;
        }
    }


    private function getAgendaItem(int $id): bool|array
    {
        $sql = "SELECT * FROM agenda WHERE id=:id LIMIT 1";

        $params = [':id' => $id];

        return $this->database->read($sql, $params);
    }


    private function getAgenda(): bool|array
    {
        $sql = "SELECT *,id FROM agenda WHERE NOW() > in_agenda && NOW() < uit_agenda";

        return $this->database->read($sql, [], PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);
    }


    private function getText(): bool|array
    {
        $sql = "SELECT tag,tekst,id,tag FROM tekst";

        $array[] = $this->database->read($sql, [], PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);
        return $array;
    }

    private function getItem(int $id): bool|array
    {
        $sql = "SELECT * FROM `werk` WHERE id=:id LIMIT 1";

        $params = [':id' => $id];

        return $this->database->read($sql, $params);
    }

    private function getShowcases(): array
    {
        $result[CAT_KEY_NEW] = $this->getSelection(CAT_KEY_NEW, 3);
        $result[CAT_KEY_AUTONOMOUS] = $this->getSelection(CAT_KEY_AUTONOMOUS, 3);
        $result[CAT_KEY_APPLIED] = $this->getSelection(CAT_KEY_APPLIED, 3);
        $result[CAT_KEY_ARCHIVE] = $this->getSelection(CAT_KEY_ARCHIVE, 3);
        return $result;
    }

    private function getSelection(string $category, int|bool $n = false): bool|array
    {

        $limit = $n === false ? " ORDER BY titel" : " AND in_overzicht=1 ORDER BY RAND() LIMIT " . $n;

        $sql = "SELECT * FROM `werk` WHERE categorie=:category" . $limit;

        $params = [':category' => $category];

        return $this->database->read($sql, $params);
    }


    public function getCategory(): bool|string
    {
        if ($_SERVER['REQUEST_METHOD'] === "GET" && $this->isValidCategory($_GET['c'] ?? false)) {


            return $_GET['c'];
        } else if ($_SERVER['REQUEST_METHOD'] === "POST" && $this->isValidCategory($_POST['c'] ?? false)) {

            return $_POST['c'];
        } else {

            return false;
        }
    }

    private function isValidCategory(string|bool $value): bool
    {

        //mind that id from $_GET is a string, not an int.
        $result = match ($value) {

            CAT_KEY_NEW => true,
            CAT_KEY_APPLIED => true,
            CAT_KEY_ARCHIVE => true,
            CAT_KEY_AUTONOMOUS => true,
            default => false
        };

        return $result;
    }
}
