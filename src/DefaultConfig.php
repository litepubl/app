<?php

namespace litepubl\core\app;

return [
'remap' => [
],

'implementations' => [
litepubl\core\storage\StorageInterface::class => litepubl\core\storage\Storage::class,
litepubl\core\storage\PoolInterface::class => litepubl\core\storage\Pool::class,
LoggerInterface::class => Logger::class,
],

'factories' => [

],
];
