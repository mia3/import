<?php
namespace MIA3\Import;

use MIA3\Import\Modifiers\DateModifier;
use MIA3\Import\Modifiers\RelationModifier;
use MIA3\Import\Modifiers\TextModifier;

class AbstractImport
{
    use TextModifier;
    use RelationModifier;
    use DateModifier;

    /**
     * @var array
     */
    protected $adapters = array();

    /**
     * @var array
     */
    protected $mappings = array();

    /**
     * @var array
     */
    protected $idMap = array();

    /**
     * @param $name
     * @param $adapter
     */
    protected function addAdapter($name, $adapter) {
        $this->adapters[$name] = $adapter;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getAdapter($name) {
        return $this->adapters[$name];
    }

    /**
     * @param $name
     * @param $source
     * @param null $target
     * @return Mapping
     */
    public function createMapping($name, $source, $target = NULL) {
        $mapping = new Mapping($this, $source, $target);
        $this->mappings[$name] = $mapping;
        return $mapping;
    }

    /**
     *
     */
    public function test() {
        foreach($this->mappings as $mapping) {
            $rows = $mapping->getRows();
            if ($mapping->shouldTruncate() !== FALSE) {
                $mapping->executeTruncation();
            }
            foreach ($rows as $row) {
                $primaryKey = $row[$mapping->getPrimaryKey()];
                $row = $this->convertRow($row, $mapping);
                $newPrimaryKey = $mapping->saveRow($row);
                $mapping->addPrimaryKeyValue($primaryKey, $newPrimaryKey);
            }
        }
    }

    /**
     * @param $row
     * @param $mapping
     * @return array|mixed
     * @throws \Exception
     */
    public function convertRow($row, $mapping) {
        $convertedRow = array();
        foreach ($mapping->getColumns() as $column) {
            $value = $this->getValue($row, $column);
            $value = $this->modify($value, $column);
            $convertedRow = $this->setValue($convertedRow, $column, $value);
        }
        return $convertedRow;
    }

    /**
     * @param $row
     * @param $column
     * @return array
     */
    public function getValue($row, $column) {
        $source = $column->getSource();
        if (stristr($source, ',') === FALSE) {
            return $row[$source];
        }

        $columnNames = $this->trimExplode(',', $source);
        $value[] = array();
        foreach ($columnNames as $columnName) {
            $values[] = $row[$columnName];
        }
        return $values;
    }

    /**
     * @param $row
     * @param $column
     * @param $value
     * @return mixed
     */
    public function setValue($row, $column, $value) {
        $target = $column->getTarget();
        if (stristr($target, ',') === FALSE) {
            $row[$target] = $value;
            return $row;
        }

        $columnNames = $this->trimExplode(',', $target);
        foreach ($columnNames as $key =>$columnName) {
            $row[$columnName] = $value[$key];
        }
        return $row;
    }

    /**
     * @param $value
     * @param $column
     * @return mixed
     * @throws \Exception
     */
    public function modify($value, $column) {
        $modifiers = $column->getModifiers();
        foreach ($modifiers as $modifier) {
            array_unshift($modifier['arguments'], $value);
            if (method_exists($this, $modifier['method'])) {
                $value = call_user_func_array(
                    array($this, $modifier['method']),
                    $modifier['arguments']
                );
            } elseif(function_exists($modifier['method'])) {
                $value = call_user_func_array(
                    $modifier['method'],
                    $modifier['arguments']
                );
            } else {
                throw new \Exception('Unknown modifier method: ' . $modifier['method']);
            }
        }
        return $value;
    }

    /**
     * @param $delimiter
     * @param $string
     * @return array
     */
    public function trimExplode($delimiter, $string) {
        $parts = explode($delimiter, $string);
        array_walk($parts, function(&$part) {
            $part = trim($part);
        });
        return $parts;
    }
}