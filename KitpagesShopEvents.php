<?php

namespace Kitpages\ShopBundle;

final class KitpagesShopEvents
{
    const AFTER_ORDER_PAYED = 'kitpages_shop.event.after_order_payed';

    const AFTER_ORDER_CANCELED = 'kitpages_shop.event.after_order_canceled';

    const AFTER_CART_INIT = 'kitpages_shop.event.after_cart_init';

    const AFTER_TRANSACTION_REFUSED = 'kitpages_shop.event.after_transaction_refused';
}