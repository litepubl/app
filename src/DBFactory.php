<?php
namespace LitePubl\Core\App;

use litepubl\core\container\ContainerInterface;
use litepubl\core\container\factories\Base;
use LitePubl\Core\DB\AdapterInterface;
use LitePubl\Core\DB\MySql;
use LitePubl\Core\DB\PdoAdapter;
use LitePubl\Core\DB\DB;
use LitePubl\Core\DB\ConnectException;
use \mysqli;
use \PDO;

class DBFactory extends Base
{
    protected $implementations = [
    AdapterInterface::class => MySql::class,
    ];

    protected $classMap = [
    MySql::class => 'createMySql',
    PdoAdapter::class => 'createPdoAdapter',
    mysqli::class => 'createMysqli',
        DB::class => 'createDB',
        ];

    public function createMySql(): MySql
    {
        return new MySql($this->container->get(mysqli::class));
    }

    public function createDB(): DB
    {
        return new DB($this->container->get(AdapterInterface::class));
    }

    public function createPdoAdapter(): PdoAdapter
    {
        return new PdoAdapter($this->container->get(PDO::class));
    }
    public function createMysqli(): mysqli
    {
        $config = $this->container->get(Config::class);
        $options = $config->getArgs($this->container, mysqli::class);
        $result = new mysqli($options['host'], $options['username'], $options['passwd'], $options['dbname'], $options['port'], $options['socket']);

        if (mysqli_connect_error()) {
            throw new ConnectException('Error connect to mysqli database');
        }

        $result->set_charset('utf8');

        if (isset($options['sql_mode']) && $options['sql_mode']) {
            $result->query("set sql_mode = '{$options['sql_mode']}'");
        }

        return $result;
    }

    public function createPDO():PDO
    {
        $config = $this->container->get(Config::class);
        $options = $config->getArgs($this->container, PDO::class);
        $result = new PDO($options['dsn'], $options['username'], $options['password'], $options['options']);
        $result->exec('SET NAMES utf8');
        return $result;
    }
}
