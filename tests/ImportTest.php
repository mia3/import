<?php

require __DIR__ . '/../vendor/autoload.php';

class ImportTest extends \MIA3\Import\AbstractImport {

    public function __construct()
    {
        $sourceAdapter = new \MIA3\Import\Adapter\MySQLAdapter(array(
            'host' => 'localhost',
            'username' => 'root',
            'password' => '',
            'database' => 'import-test'
        ));
        $this->addAdapter('source', $sourceAdapter);
        $this->addAdapter('target', $sourceAdapter);

        $companyMapping = $this->createMapping('company', array('table' => 'foo_company'), array('table' => 'bar_company'));
        $companyMapping->primaryKey('id');
        $companyMapping->truncate();
        $companyMapping->createColumn('name')->into('name');

        $fooMapping = $this->createMapping('foo', array('table' => 'foo'), array('table' => 'bar'));
        $fooMapping->primaryKey('id');
        $fooMapping->truncate();
        $fooMapping->createColumn('full_name')->split(' ')->into('first_name, last_name');
        $fooMapping->createColumn('last_name')->uppercase()->into('last_name');
        $fooMapping->createColumn('first_name, last_name')->combine(' ')->into('full_name');
        $fooMapping->createColumn('company')->updateForeignKey('company')->into('company');
        $fooMapping->createColumn('email')->into('email');
        $fooMapping->createColumn('gender')->into('gender');
        $fooMapping->createColumn('ip_address')->into('ip_address');
        $fooMapping->createColumn('someDate')->date('Y-m-d', 'U')->into('timestamp');
        $fooMapping->createColumn('currency')->valueMap(array('EUR' => '€', 'USD' => '$'))->into('currency');
        $fooMapping->createColumn('timestamp')->date('U', 'd.m.Y')->into('date');
        $fooMapping->createColumn('datestring')->date('d.m.Y', 'Y-m-d H:i:s')->into('datetime');
    }
}

$importTest = new ImportTest();
$importTest->test();