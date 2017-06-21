<?php
namespace LitePubl\Core\App;

use litepubl\core\container\factories\Base;
use LitePubl\Core\Mailer\MailerInterface;
use LitePubl\Core\Mailer\Mailer;
use LitePubl\Core\Options\Options;
use LitePubl\Core\Mailer\AdapterInterface;
use LitePubl\Core\Mailer\MailAdapter;
use LitePubl\Core\Mailer\SmtpAdapter;
use LitePubl\Core\LogManager\LogManagerInterface;
use \SMTP;

class MailerFactory extends Base
{
    protected $implementations = [
    MailerInterface::class => Mailer::class,
    AdapterInterface::class => MailAdapter::class,
    ];

    protected $classMap = [
    Mailer::class => 'createMailer',
    MailAdapter::class => 'createMailAdapter',
    SmtpAdapter::class => 'createSmtpAdapter',
    SMTP::class => 'createSmtp',
        ];

    public function createMailer(): Mailer
    {
        $adapter = $this->container->get(AdapterInterface::class);
        $options = $this->container->get(Options::class);

        return new Mailer($adapter, $options->fromName, $options->fromEmail, $options->adminName, $options->adminEmail);
    }

    public function createMailAdapter(): MailAdapter
    {
        return new MailAdapter();
    }

    public function createSmtpAdapter(): SmtpAdapter
    {
        $smtp = $this->container->get(SMTP::class);

        return new SmtpAdapter($smtp, $account);
    }

    public function createSmtp(): SMTP
    {
        return new SMTP();
    }
}
