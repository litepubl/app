<?php
namespace LitePubl\Core\App;

use litepubl\core\container\ContainerInterface;
use litepubl\core\container\factories\Base;
use LitePubl\Core\DB\AdapterInterface;
use LitePubl\Core\DB\MysqliAdapter;
use LitePubl\Core\DB\PdoAdapter;
use LitePubl\Core\DB\DB;
use LitePubl\Core\DB\EventsInterface;
use LitePubl\Core\DB\LogEvents;
use LitePubl\Core\DB\NullEvents;
use LitePubl\Core\DB\ConnectException;
use \mysqli;
use \PDO;

class DBFactory extends Base
{
    protected $implementations = [
    AdapterInterface::class => MysqliAdapter::class,
    EventsInterface::class => LogEvents::class,
    ];

    protected $classMap = [
    MysqliAdapter::class => 'createMysqliAdapter',
    PdoAdapter::class => 'createPdoAdapter',
    mysqli::class => 'createMysqli',
        DB::class => 'createDB',
    LogEvents::class => 'createLogEvents',
    NullEvents::class => 'createNullEvents',
        ];

    public function createMysqliAdapter(): MysqliAdapter
    {
        return new MysqliAdapter($this->container->get(mysqli::class));
    }

    public function createDB(): DB
    {
        $adapter = $this->container->get(AdapterInterface::class);
        $events = $this->container->get(EventsInterface::class);

        $config = $this->container->get(Config::class);
        $options = $config->getArgs($this->container, DB::class);

        return new DB($adapter, $events, $options['prefix']);
    }

    public function createPdoAdapter(): PdoAdapter
    {
        return new PdoAdapter($this->container->get(PDO::class));
    }

    public function createLogEvents(): LogEvents
    {
        $config = $this->container->get(Config::class);
        $options = $config->getArgs($this->container, LogEvents::class);

        return new LogEvents($options['format'] ?? null, $options['summaryFormat'] ?? null);
    }

    public function createnullEvents(): NullEvents
    {
        return new NullEvents();
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
