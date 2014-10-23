<?php

namespace Kitpages\ShopBundle\Plugin;

class PaypalPlugin extends \JMS\Payment\CoreBundle\Plugin\AbstractPlugin
{
    public function processes($name)
    {
        return 'paypal' === $name;
    }
}