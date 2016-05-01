<?php
namespace MIA3\Import\Adapter;

class MySQLAdapter
{
    /**
     * @var \mysqli
     */
    protected $link;

    /**
     * MySQLAdapter constructor.
     * @param $configuration
     */
    public function __construct($configuration)
    {
        $host = isset($configuration['host']) ? $configuration['host'] : 'localhost';
        $username = isset($configuration['username']) ? $configuration['username'] : null;
        $password = isset($configuration['password']) ? $configuration['password'] : null;
        $database = $configuration['database'];
        $this->link = new \mysqli($host, $username, $password, $database);
    }

    /**
     * @param $configuration
     * @return array
     */
    public function getRows($configuration)
    {
        $query = "SELECT * FROM " . $configuration['table'];
        $result = $this->link->query($query);

        $rows = array();
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * @param $row
     * @param $configuration
     * @return mixed
     */
    public function saveRow($row, $configuration)
    {
        array_walk($row, function(&$item){
           $item = '"' . $this->link->real_escape_string($item) . '"';
        });
        $query = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $configuration['table'],
            implode(', ', array_keys($row)),
            implode(', ', $row)
        );
        $this->link->query($query);
        return $this->link->insert_id;
    }

    /**
     * @param $configuration
     */
    public function truncate($configuration) {
        $query = sprintf('TRUNCATE TABLE %s', $configuration['table']);
        $this->link->query($query);
    }
}