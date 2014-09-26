<?php
namespace ZE\Bandaid\Tests;

use Spyc;
use ZE\Bandaid\Service\Mongo\UserService;
use ZE\Bandaid\Service\MongoUserService;

abstract class Abstract_TestCase extends \PHPUnit_Framework_TestCase
{
    protected $fixturePath = 'fixtures';
    protected $db = null;
    protected $dbType = 'pdo';
    protected $dbHelper;

    /**
     * @return mixed
     */
    public function getDbHelper()
    {
        return $this->dbHelper;
    }

    /**
     * @param mixed $dbHelper
     */
    public function setDbHelper($dbHelper)
    {
        $this->dbHelper = $dbHelper;
    }

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

    protected $joinTableFixtures;

    /**
     * @return mixed
     */
    public function getJoinTableFixtures()
    {
        return $this->joinTableFixtures;
    }

    /**
     * @param mixed $joinTableFixtures
     */
    public function setJoinTableFixtures($joinTableFixtures)
    {
        $this->joinTableFixtures = $joinTableFixtures;
    }

    public function truncateTables($tables)
    {
        foreach ($tables as $table) {
            switch ($this->dbType) {
                case 'pdo':
                    $stmt = $this->db->prepare("TRUNCATE TABLE $table");
                    $stmt->execute();
                    break;
                case 'mongo':
                    $this->db->$table->drop();
                    break;
            }

        }
    }

    public function returnFixturesData($truncate = true)
    {
        $returnData = array();
        foreach ($this->fixtures as $table => $options) {
            $data = Spyc::YAMLLoad($this->fixturePath . DIRECTORY_SEPARATOR . $table . '.yml');
            if ($truncate) {
                $this->truncateTables(array('user'));
            }
            $returnData[$table]= reset($data);
        }
        return $returnData;
    }

    public function loadUsers()
    {
        $this->setFixturePath(getcwd() . '/src/ZE/Bandaid/Tests/fixtures/users/mongo');
        $this->setFixtures(array('user'=>array()));
        $data = $this->returnFixturesData(true);
        $service = new UserService($this->db);
        foreach($data['user'] as $user){
            $service->createUser($user['email'],$user['password'],$user['email'], $user['id']);
        }

    }


    public function loadFixtures($truncate = false, $dualReference = true)
    {
        if (!empty($this->fixtures)) {

            foreach ($this->fixtures as $table => $options) {

                $data = Spyc::YAMLLoad($this->fixturePath . DIRECTORY_SEPARATOR . $table . '.yml');

                if ($truncate) {
                    $this->truncateTables(array_keys($data));
                }
                foreach ($data as $table => $fixtureData) {
                    switch ($this->dbType) {
                        case 'pdo':
                            $columns = array_keys(reset($fixtureData));
                            $values = $columns;
                            array_walk($values, function (&$item) {
                                $item = ':' . $item;
                            });

                            $query = 'INSERT INTO `' . $table . '`(' . implode(',', $columns) . ' ) VALUES ( ' . implode(',', $values) . ' )';
                            foreach ($fixtureData as $row) {
                                $stmt = $this->db->prepare($query);
                                foreach ($row as $key => &$value) {
                                    $stmt->bindParam(':' . $key, $value, \PDO::PARAM_STR);
                                }
                                try {
                                    $stmt->execute();
                                } catch (\Exception $e) {
                                    var_dump($e);
                                }
                            }
                            break;
                        case 'mongo':
                            foreach ($fixtureData as $row) {

                                if (isset($options['columns_to_delete'])) {
                                    $row = array_diff_key($row, array_flip($options['columns_to_delete']));
                                }
                                $this->dbHelper->saveRow($row, $table, $dualReference, null, null, $options);
                            }
                            break;
                    }
                }
            }
        }
    }

    public function loadJoinTableFixtures($dualReference = true)
    {
        if (!empty($this->joinTableFixtures)) {

            foreach ($this->joinTableFixtures as $yamlFile => $joinTableFixture) {

                $data = Spyc::YAMLLoad($this->fixturePath . DIRECTORY_SEPARATOR . $yamlFile . '.yml');

                foreach ($data as $table => $fixtureData) {
                    $this->dbHelper->saveJoinTableReferences($joinTableFixture, $fixtureData);

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
    public function getDb()
    {
        return $this->db;
    }

    /**
     * @param \PDO $pdo
     */
    public function setDb($db)
    {
        $this->db = $db;
    }


    public function __construct($dbType = 'pdo')
    {
        $this->dbType = $dbType;
        switch ($dbType) {
            case 'pdo':
                if ($this->db === null) {
                    $options = array(
                        \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'",
                        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                        \PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC
                    );
                    $this->db = new \PDO($GLOBALS['PDO_DSN'], $GLOBALS['PDO_USER'], $GLOBALS['PDO_PASSWD'], $options);
                }
                break;
            case 'mongo':
                if ($this->db === null) {
                    $host = $GLOBALS['MONGO_DBHOST'];
                    $database = $GLOBALS['MONGO_DBNAME'];
                    $server = "mongodb://$host:27017";
                    $connection = new \MongoClient($server);
                    $collection  = $connection->$database;
                    $this->db = $collection ;
                }
                break;
        }
    }


}

?>