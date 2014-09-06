<?php
namespace ZE\Bandaid\Tests;

use Spyc;

abstract class Abstract_TestCase extends \PHPUnit_Framework_TestCase
{
    protected $fixturePath = 'fixtures';
    /**
     * @var \PDO
     */
    protected $pdo = null;

    /**
     * @var Array fixtures
     */
    protected $fixtures;

    /**
     * @return Array
     */
    public function getFixtures()
    {
        return $this->fixtures;
    }

    /**
     * @param Array $fixtures
     */
    public function setFixtures($fixtures)
    {
        $this->fixtures = $fixtures;
    }

    public function truncateTables($tables)
    {
        foreach($tables as $table){
            $stmt = $this->pdo->prepare("TRUNCATE TABLE $table");
            $stmt->execute();
        }
    }
    public function loadFixtures($truncate = false)
    {
        if (!empty($this->fixtures)) {
            foreach ($this->fixtures as $table) {
                $data = Spyc::YAMLLoad($this->fixturePath . DIRECTORY_SEPARATOR . $table . '.yml');
                if ($truncate) {
                    $this->truncateTables(array_keys($data));
                }
                foreach ($data as $table => $fixtureData) {

                    $columns = array();
                    $columns = array_keys(reset($fixtureData));
                    $values = $columns;
                    array_walk($values, function (&$item) {
                            $item = ':' . $item;
                        });

                    $query = 'INSERT INTO `' . $table . '`(' . implode(',', $columns) . ' ) VALUES ( ' . implode(',', $values) .' )';
                    foreach ($fixtureData as $row) {
                        $stmt = $this->pdo->prepare($query);
                        foreach ($row as $key => &$value) {
                            $stmt->bindParam(':' . $key, $value, \PDO::PARAM_STR);
                        }
                        try {
                            $stmt->execute();
                        } catch(\Exception $e){
                            var_dump($e);
                        }
                    }

                }

            }
        }
    }

    /**
     * @return string
     */
    public function getFixturePath()
    {
        return $this->fixturePath;
    }

    /**
     * @param string $fixturePath
     */
    public function setFixturePath($fixturePath)
    {
        $this->fixturePath = $fixturePath;
    }

    /**
     * @return \PDO
     */
    public function getPdo()
    {
        return $this->pdo;
    }

    /**
     * @param \PDO $pdo
     */
    public function setPdo($pdo)
    {
        $this->pdo = $pdo;
    }


    public function __construct()
    {
        if ($this->pdo === null) {
            $options = array(
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'",
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC
            );
            $this->pdo = new \PDO($GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD'], $options);
        }
    }


}

?>