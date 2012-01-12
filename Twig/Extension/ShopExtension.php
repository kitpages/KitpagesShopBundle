<?php
namespace Kitpages\ShopBundle\Twig\Extension;

use Symfony\Component\Locale\Locale;

class ShopExtension extends \Twig_Extension
{

    public static function countryName($value, $locale)
    {
        $countryList = \Symfony\Component\Locale\Locale::getDisplayCountries($locale);
        if (!in_array($value, $countryList)) {
            return $countryList[$value];
        } else {
            return $value;
        }
    }

    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return array An array of filters
     */
    public function getFilters()
    {
        return array(
            'kit_shop_country' => new \Twig_Filter_Function('Kitpages\ShopBundle\Twig\Extension\ShopExtension::countryName')
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'kitpages_shop_shop';
    }
}
