<?php
namespace litepubl\core\app;

use litepubl\core\container\ContainerInterface;
use litepubl\core\container\factories\Base;
use litepubl\core\events\EventManagerInterface;
use litepubl\core\events\EventInterface;
use litepubl\core\events\Callbacks;
use litepubl\core\events\Composite;
use litepubl\core\events\Events;
use litepubl\core\events\GlobalEvents;
use litepubl\core\storage\StorageInterface;
use litepubl\core\storage\PoolInterface;

class EventsFactory extends Base
{
    protected $implementations = [
    EventManagerInterface::class => Composite::class,
    EventInterface::class => Event::class,
    ];

    protected $classMap = [
    Callbacks::class => 'createCallbacks',
        Composite::class => 'createComposite',
    Events::class => 'createEvents',
    GlobalEvents::class => 'createGlobalEvents',
        ];

    public function createComposite(): Composite
    {
        $callbacks = $this->createCallbacks();
        $events = $this->createEvents();
        $globalEvents = $this->createGlobalEvents();

        return new Composite($callbacks, $events, $globalEvents);
    }

    public function createCallbacks(): Callbacks
    {
        return new Callbacks($this->container);
    }

    public function createEvents(): Events
    {
        return new Events($this->container);
    }


    public function createGlobalEvents(): GlobalEvents
    {
        return new GlobalEvents($this->container);
    }
}
