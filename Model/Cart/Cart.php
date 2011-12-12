<?php
namespace Kitpages\ShopBundle\Model\Cart;
/**
 * a cart in the shop
 * this cart is a service in the DIC
 */
class Cart
    implements CartInterface
{
    ////
    // variables
    ////
    protected $lineList = array();
    protected $lineSequence = 1;

    public function __construct()
    {
    }
    ////
    // Method of the CartInterface
    ////
    /**
     * @param CartableInterface $item : item to add
     * @param int $quantity
     * @param CartLineInterface $parentLine : parent line of this new product (null if no parentLine
     * @return CartLineInterface the line added
     */
    public function addLine(
        CartableInterface $item,
        $quantity = 1,
        CartLineInterface $parentLine = null
    )
    {
        $cartLine = $this->createEmptyCartLine();
        $cartLine->setCartable($item);
        $cartLine->setQuantity($quantity);
        if ($parentLine instanceof CartLineInterface) {
            $cartLine->setParentLineId($parentLine->getId());
        }
        $cartLine->setId($this->lineSequence);
        $this->lineList[$this->lineSequence] = $cartLine;

        $this->lineSequence ++;
        return $cartLine;
    }

    /**
     * remove a ligne from the cart. If there is other lines
     * with this line as parentLine, these products will be
     * removed too.
     * @param int $lineId id of the line
     */
    public function deleteLine($lineId)
    {
        // remove child lines with parentLineId == $lineId
        foreach ($this->lineList as $line) {
            if ( $line->getParentLineId() === $lineId ) {
                $this->deleteLine($line->getId());
            }
        }
        if (array_key_exists($lineId, $this->lineList) ) {
            unset($this->lineList[$lineId]);
        }
    }

    /**
     * remove all lines from the cart
     */
    public function emptyCart()
    {
        $this->lineList = array();
    }

    /**
     * returns a given line from the cart
     * @param int $lineId
     * @return CartLineInterface
     */
    public function getLine($lineId) {
        if (array_key_exists($lineId, $this->lineList) ) {
            return $this->lineList[$lineId];
        }
        return null;
    }

    /**
     * returns all the lines of the cart
     * @return array of CartLineInterface
     */
    public function getLineList()
    {
        return $this->lineList;
    }

    ////
    // protected methods
    ////
    /**
     * create an empty line. This method can be subclassed to
     * change the type of a cartLine (should implement CartLineInterface)
     * @return CartLine empty line in the cart
     */
    protected function createEmptyCartLine()
    {
        return new CartLine();
    }
}
