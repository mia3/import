<?php
namespace MIA3\Import;

class Column
{
    /*
     * column configuration
     * @var array
     */
    protected $configuration = array(
        'modifiers' => array()
    );

    /*
     * @param string
     * @param string
     */
    public function __construct($target, $source = NULL) {
        $this->configuration['target'] = $target;
        $this->configuration['source'] = $source;
    }

    /**
     * set the source of this column
     *
     * @param $target
     */
    public function from($source) {
        $this->configuration['source'] = $source;
    }

    /*
     * magic function to catch modifiers
     */
    public function __call($name, $arguments)
    {
        $this->configuration['modifiers'][] = array(
            'method' => $name,
            'arguments' => $arguments
        );
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSource() {
        return $this->configuration['source'];
    }

    /**
     * @return mixed
     */
    public function getTarget() {
        return $this->configuration['target'];
    }

    /**
     * @return mixed
     */
    public function getModifiers() {
        return $this->configuration['modifiers'];
    }
}