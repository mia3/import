<?php
namespace MIA3\Import;

class Mapping
{
    /*
     * container for created columns
     * @var array
     */
    protected $columns = array();

    /*
     * configuration array
     *
     * @var array
     */
    protected $configuration = array(
        'source' => array(
            'adapter' => 'source'
        ),
        'target' => array(
            'adapter' => 'target'
        )
    );

    /*
     * parent object
     *
     * @var AbstractImport
     */
    protected $parent;

    /*
     * toggle, if the target should be truncated before improt
     */
    protected $truncate = false;

    /*
     * container for a map of old to new primary keys
     */
    protected $primaryKeyMap = array();

    /*
     *
     */
    public function __construct($parent, $source, $target) {
        $this->parent = $parent;
        $this->configuration['source'] = array_replace($this->configuration['source'], $source);
        $this->configuration['target'] = array_replace($this->configuration['target'], $target);
    }

    /*
     * set the primary key field
     *
     * @param string $field
     */
    public function primaryKey($field) {
        $this->configuration['primaryKey'] = $field;
    }

    /*
     * get the primary key field
     */
    public function getPrimaryKey() {
        return $this->configuration['primaryKey'];
    }

    /*
     * add a primaryKey value with olf and new key
     *
     * @param mixed $oldKey
     * @param mixed $newKey
     */
    public function addPrimaryKeyValue($oldKey, $newKey) {
        $this->primaryKeyMap[$oldKey] = $newKey;
    }

    /*
     * get a new primaryKey value based on an old one
     *
     * @param mixed $oldKey
     * @return mixed $newKey
     */
    public function getPrimaryKeyValue($oldKey) {
        if (isset($this->primaryKeyMap[$oldKey])) {
            return $this->primaryKeyMap[$oldKey];
        }
    }

    /*
     * create and store a new column
     *
     * @param string $source
     * @param string target
     * @return Column
     */
    public function createColumn($source, $target = NULL) {
        $column = new Column($source, $target);
        $this->columns[] = $column;
        return $column;
    }

    /*
     * get rows from source dapter
     *
     * @return array
     */
    public function getRows() {
        $adapter = $this->parent->getAdapter($this->configuration['source']['adapter']);
        return $adapter->getRows($this->configuration['source']);
    }

    /*
     * save a row using the target adapter
     *
     * @return mixed $newKey
     */
    public function saveRow($row) {
        $adapter = $this->parent->getAdapter($this->configuration['target']['adapter']);
        return $adapter->saveRow($row, $this->configuration['target']);
    }

    /*
     * get all configured columns
     *
     * @return array<Column>
     */
    public function getColumns() {
        return $this->columns;
    }

    /*
     * enable truncate beefore import
     *
     * @param boolean $truncate
     */
    public function truncate($truncate = TRUE) {
        $this->truncate = $truncate;
    }

    /*
     * execute truncation on target adapter
     */
    public function executeTruncation() {
        $adapter = $this->parent->getAdapter($this->configuration['target']['adapter']);
        return $adapter->truncate($this->configuration['target']);
    }

    /*
     * should the target be truncated before import?
     *
     * @return boolean
     */
    public function shouldTruncate() {
        return $this->truncate;
    }
}