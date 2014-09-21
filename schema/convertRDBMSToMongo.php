<?php

use ZE\RDBMSToMongo\Adapter as Adapter;
use Spyc;

class RDBMSToMongoConverter
{
    protected $fixturePath;
    public function run()
    {
        $data = Spyc::YAMLLoad($this->fixturePath . DIRECTORY_SEPARATOR . $table . '.yml');
        foreach ($fixtureData as $row)
        {
            if (isset($row['id']))
            {
                unset ($row['id']);
            }

            $this->db->$table->insert($row);
        }
    }
}

$con = new RDBMSToMongoConverter();