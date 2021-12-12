<?php




class Custom
{

    public function __construct(private Database $database)
    {
    }

    public function execute(string $table, int $id): bool|array
    {

        if ($table === "werk" && !$id) {

            return $this->getShowcases();
        }   
    }

    private function getShowcases()
    {
        $result[CAT_KEY_NEW] = $this->getSelection(CAT_ID_NEW);
        $result[CAT_KEY_AUTONOMOUS] = $this->getSelection(CAT_ID_AUTONOMOUS);
        $result[CAT_KEY_APPLIED] = $this->getSelection(CAT_ID_APPLIED);
        $result[CAT_KEY_ARCHIVE] = $this->getSelection(CAT_ID_ARCHIVE);
        return $result;
    }

    private function getSelection(int $category, int $n = 3): bool|array
    {

        $n = $n <= 0 ? 3 : $n;

        $sql = "SELECT * FROM `werk` WHERE categorie_werk_id=:werk_id ORDER BY RAND() LIMIT " . $n;

        $params = [':werk_id' => $category];

        return $this->database->read($sql, $params);
    }
}
