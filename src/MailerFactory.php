<?php
namespace LitePubl\Core\App;

use litepubl\core\container\ContainerInterface;
use litepubl\core\container\factories\Base;
use LitePubl\Core\Mailer\MailerInterface;
use LitePubl\Core\Mailer\Mailer;
use LitePubl\Core\Mailer\Smtp;

class DBFactory extends Base
{
    protected $implementations = [
    MailerInterface::class => Mailer::class,
    ];

    protected $classMap = [
    Mailer::class => 'createMailer',
    Smtp::class => 'createSmtp',
        ];

    public function createMailer(): Mailer
    {
        return new Mailer($this->container->get(mysqli::class));
    }

    public function createSmtp(): Smtp
    {
        return new Smtp();
    }
}
