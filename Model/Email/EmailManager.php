<?php
namespace Kitpages\ShopBundle\Model\Email;

use Kitpages\ShopBundle\Entity\Order;
use Kitpages\ShopBundle\Entity\OrderHistory;
use Kitpages\ShopBundle\Entity\OrderLine;
use Kitpages\ShopBundle\Entity\OrderUser;
use Kitpages\ShopBundle\Entity\Invoice;
use Kitpages\ShopBundle\Model\Cart\CartInterface;
use Kitpages\ShopBundle\Event\ShopEvent;
use Kitpages\ShopBundle\KitpagesShopEvents;

use Kitano\PaymentBundle\Event\PaymentEvent;
use Kitano\PaymentBundle\Model\Transaction;

use Symfony\Component\HttpFoundation\Session;
use Symfony\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Templating\EngineInterface;


class EmailManager
{
    /** @var null|\Symfony\Bundle\DoctrineBundle\Registry */
    protected $doctrine = null;
    /** @var null|LoggerInterface */
    protected $logger = null;

    /** @var null|\Symfony\Component\Templating\EngineInterface */
    protected $templating = null;

    /** @var null|\Symfony\Component\EventDispatcher\EventDispatcherInterface */
    protected $dispatcher = null;

    /** @var null|\Swift_Mailer */
    protected $mailer = null;

    /** @var null|string */
    protected $fromEmail = null;

    /** @var array of emails */
    protected $invoiceEmailList = array();

    public function __construct(
        Registry $doctrine,
        LoggerInterface $logger,
        \Swift_Mailer $mailer,
        EngineInterface $templating,
        EventDispatcherInterface $dispatcher,
        $fromEmail,
        $invoiceEmailList
    )
    {
        $this->doctrine = $doctrine;
        $this->logger = $logger;
        $this->templating = $templating;
        $this->dispatcher = $dispatcher;
        $this->mailer =  $mailer;
        $this->fromEmail = $fromEmail;
        $this->invoiceEmailList = $invoiceEmailList;
    }


    /**
     * Event listener that send an email
     * @param ShopEvent $event
     */
    public function afterOrderPayedEvent(ShopEvent $event)
    {
        if ($event->isPropagationStopped()) {
            return;
        }
        $order = $event->get("order");
        $transaction = $event->get("transaction");

        $subject = $this->templating->render(
            "KitpagesShopBundle:Email:afterOrderPayedSubject.html.twig",
            array(
                "order" => $order,
                "transaction" => $transaction
            )
        );
        $body = $this->templating->render(
            "KitpagesShopBundle:Email:afterOrderPayedBody.html.twig",
            array(
                "order" => $order,
                "transaction" => $transaction
            )
        );

        $message = \Swift_Message::newInstance()
            ->setFrom($this->fromEmail)
            ->setTo($order->getInvoiceUser()->getEmail())
            ->setSubject($subject)
            ->setBody($body)
            ->setContentType('text/html');
        $this->mailer->send($message);

        // mail to administrators with invoice
        $invoiceSubject = $this->templating->render(
            "KitpagesShopBundle:Email:afterOrderPayedInvoiceEmailSubject.html.twig",
            array(
                "order" => $order,
                "transaction" => $transaction
            )
        );
        $message = \Swift_Message::newInstance()
            ->setFrom($this->fromEmail)
            ->setTo($this->invoiceEmailList)
            ->setSubject($invoiceSubject)
            ->setBody($order->getInvoice()->getContentHtml())
            ->setContentType('text/html');
        $this->mailer->send($message);

    }
}
