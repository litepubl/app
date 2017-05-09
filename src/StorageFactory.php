<?php
namespace litepubl\core\app;

use litepubl\core\container\ContainerInterface;
use litepubl\core\container\factories\Base;
use litepubl\core\storage\StorageInterface;
use litepubl\core\storage\PoolInterface;
use litepubl\core\storage\Storage;
use litepubl\core\storage\Pool;
use litepubl\core\storage\LockerInterface;
use litepubl\core\storage\FileLocker;
use litepubl\core\storage\serializer\SerializerInterface;
use litepubl\core\storage\serializer\Php;
use litepubl\core\storage\serializer\JSon;
use litepubl\core\storage\serializer\Serialize;
use litepubl\core\logfactory\Factory as LogFactory;
use litepubl\core\logfactory\FactoryInterface as LogFactoryInterface;
use litepubl\core\logmanager\LogManagerInterface;
use litepubl\core\logmanager\LazyFactory;

class StorageFactory extends Base
{
    public $fileLockerName = 'storage.lok';
    protected function getClassMap(): array
    {
        return [
        Paths::class => 'createPaths',
        Storage::class => 'createStorage',
        Pool::class => 'createPool',
        Php::class => 'createPhp',
        JSon::class => 'createJSon',
        Serialize::class => 'createSerialize',
        FileLocker::class => 'createFileLocker',
        LogFactory::class => 'createLogFactory',
        LazyFactory::class => 'createLazyFactory',
        ];
    }

    public function createStorage(): Storage
    {
        $serializer = $this->container->get(SerializerInterface::class);
        $logFactory = $this->container->get(LogFactoryInterface::class);
        $paths = $this->container->get(Paths::class);
        $path = ltrim($paths->data, '\/') . '/';

        return new Storage($serializer, $logFactory, $path);
    }

    public function createPhp(): Php
    {
        return new Php();
    }

    public function createJSon(): JSon
    {
        return new JSon(0, true);
    }

    public function createSerialize(): Serialize
    {
        return new Serialize();
    }

    public function createPool(): Pool
    {
        $storage = $this->container->get(storageInterface::class);
        $locker = $this->container->get(LockerInterface::class);

        return new Pool($storage, $locker);
    }

    public function createFileLocker(): FileLocker
    {
        $paths = $this->container->get(Paths::class);
        return new FileLocker($paths->data . $this->fileLockerName);
    }

    public function createLazyFactory(): LazyFactory
    {
        return new LazyLogFactory([$this, 'getLogManager']);
    }

    public function getLogManager(): LogManagerInterface
    {
        return $this->container->get(LogManagerInterface::class);
    }
}
