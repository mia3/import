<?php
namespace MIA3\Import\Adapter;

use Box\Spout\Common\Type;
use Box\Spout\Reader\ReaderFactory;

class ExcelAdapter
{
    /**
     * @var object
     */
    protected $source;

    /**
     * @param $configuration
     */
    public function __construct($configuration = [])
    {
    }

    /**
     * @param $configuration
     * @return array
     */
    public function getRows($configuration)
    {
        $reader = ReaderFactory::create(Type::XLSX);
        $reader->open($configuration['file']);

        $headerAt = isset($configuration['headerRow']) ? $configuration['headerRow'] : 0;
        $startAt = isset($configuration['startAt']) ? $configuration['startAt'] : 1;

        $rows = [];
        $this->repeatingColumns = isset($configuration['repeatingColumns']) ? array_flip($configuration['repeatingColumns']) : [];
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $index => $row) {
                if ($index == $headerAt) {
                    $headers = $row;
                    $headers = array_filter($headers, function ($item) {
                        return !empty($item);
                    });
                }
                if ($index < $startAt || $index <= $headerAt) {
                    continue;
                }
                if ($headers !== null) {
                    $row = $this->mapHeaders($row, $headers);
                }
                foreach ($this->repeatingColumns as $repeatingColumn => $value) {
                    if (empty($row[$repeatingColumn])) {
                        $row[$repeatingColumn] = $value;
                    } else {
                        $this->repeatingColumns[$repeatingColumn] = $row[$repeatingColumn];
                    }
                }
                $rows[] = $row;
            }
        }

        if (isset($configuration['filter'])) {
            $rows = array_values(array_filter($rows, $configuration['filter']));
        }

        return $rows;
    }

    public function mapHeaders($sourceRow, $headers)
    {
        $row = [];
        foreach ($headers as $index => $name) {
            $row[$name] = isset($sourceRow[$index]) ? $sourceRow[$index] : null;
        }

        return $row;
    }

    /**
     * @param $row
     * @param $configuration
     * @return mixed
     */
    public function saveRow($row, $configuration)
    {
//        array_walk($row, function(&$item){
//           $item = '"' . $this->link->real_escape_string($item) . '"';
//        });
//        $query = sprintf(
//            'INSERT INTO %s (%s) VALUES (%s)',
//            $configuration['table'],
//            implode(', ', array_keys($row)),
//            implode(', ', $row)
//        );
//        $this->link->query($query);
//        return $this->link->insert_id;
    }

    /**
     * @param $configuration
     */
    public function truncate($configuration)
    {

    }
}
