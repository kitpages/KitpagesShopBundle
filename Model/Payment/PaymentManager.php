<?php
namespace Kitpages\ShopBundle\Model\Payment;

use Kitpages\ShopBundle\Entity\Order;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Validator\Constraints\True;

class PaymentManager
{

    public function __construct(
        FormFactory $formFactory,
        Router $router,
        $paymentList
    )
    {
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->paymentList = $paymentList;
    }

    public function getChoosePaymentForm(Order $order)
    {
        foreach($this->paymentList as $paymentKey => $paymentParameterList) {
            $paymentData[$paymentKey] = array(
                'return_url' => $this->router->generate($paymentParameterList['return_url'], array(
                    'orderId' => $order->getId(),
                ), true),
                'cancel_url' => $this->router->generate($paymentParameterList['cancel_url'], array(
                    'orderId' => $order->getId(),
                ), true)
            );
        }



        $form = $this->formFactory->create('jms_choose_payment_method', null, array(
            'amount'   => $order->getPriceIncludingVat(),
            'currency' => 'EUR',
            'predefined_data' => $paymentData
        ,
        ));
        $form->add(
            'systemTerms',
            'checkbox',
            array(
                'required' => true,
                'value' => 'yes',
                'label' => ' ',
                'mapped' => false,
                'constraints' => new True()
            )
        );
        return $form;
    }



}