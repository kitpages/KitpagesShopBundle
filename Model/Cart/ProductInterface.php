<?php
namespace Kitpages\ShopBundle\Model\Cart;
/**
 * This interface is used for every product who need to
 * have a price in the cart or in the catalog
 */
interface ProductInterface
    extends CartableInterface
{
    /**
     * normal price without any reduction
     * @return float shop unit price
     */
    public function getShopUnitPrice();

    /**
     * normal price without any reduction
     * @param string $countryCode ("FR"|"US"|...)
     * @return float shop unit price
     */
    public function getShopUnitVat($countryCode);

    /**
     * returns the weight of the product (0 if virtual product)
     * @return float weight of the product (per unit)
     */
    public function getShopWeight();

}
?>
