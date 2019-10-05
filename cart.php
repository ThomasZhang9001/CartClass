<?php

namespace Cart;
use Exception;
use InvalidArgumentException;

require_once 'productList.php';

class Cart
{

    private static $cart = null;
    public $items = array();
    public $validProductList = array();
    public $totalPrice = 0.00;
    public $totalQuantity = 0;


    protected function __construct()
    {
        // Get product list
        $this->validProductList = getProductList();
    }

    // Get instance
    protected static function getInstance(): Cart
    {
        if (!(self::$cart instanceof self)) {
            self::$cart = new self();
        }
        return self::$cart;
    }

    // Get singleton cart class and save to session if not exist in session
    public static function getCart(): Cart
    {
        if (!isset($_SESSION['cart']) || !($_SESSION['cart'] instanceof self)) {
            $_SESSION['cart'] = self::getInstance();
        }
        return $_SESSION['cart'];
    }

    /**
     * Add item to cart
     * @param string $name product name
     * @param int $quantity product quantity to add
     */
    public function addItem(string $name, int $quantity = 1): void
    {
        try {
            $validProduct = $this->getValidProduct($name);
        } catch (\Exception $exception) {
            // We can do log or something else here. Make it robust.
            return;
        }

        // If already in cart, we just add quantity
        if ($this->hadProduct($name)) {
            $this->increaseItemQuantity($name, $quantity);
        } else if ($validProduct) {
            $this->items[$name] = array(
                'name' => $name,
                'price' => $validProduct['price'],
                'quantity' => $quantity
            );
        }

        // Update Total quantity and price, it can optimize
        $this->setQuantityTotal();
        $this->setPriceTotal();
    }

    /**
     * @param string $name product name
     * @return array
     * @throws Exception
     */
    public function getValidProduct(string $name): array
    {
        $key = array_search($name, array_column($this->validProductList, 'name'));
        if ($key !== FALSE) {
            return $this->validProductList[$key];
        } else {
            throw new InvalidArgumentException('Invalid product provided.');
        }
    }

    /**
     * Empty current cart
     */
    public function clearCart(): void
    {
        $this->items = array();
        // Update Total quantity and price, it can optimize
        $this->setQuantityTotal();
        $this->setPriceTotal();
    }

    /**
     * Check if a product is already in the cart
     * @param string $name
     * @return bool
     */
    public function hadProduct(string $name): bool
    {
        return array_key_exists($name, $this->items);
    }

    /**
     * @param string $name
     * @param int $quantity
     */
    public function increaseItemQuantity(string $name, int $quantity = 1): void
    {
        $this->items[$name]['quantity'] += $quantity;
    }

    /**
     * @param string $name
     * @param int $quantity
     */
    public function removeItem(string $name, int $quantity = 1): void
    {
        if ($this->hadProduct($name)) {
            $this->items[$name]['quantity'] -= $quantity;
        }
        // If the item all get removed, remove the product from cart
        if ($this->items[$name]['quantity'] < 1) {
            $this->removeCartProduct($name);
        }
        // Update Total quantity and price, it can optimize
        $this->setQuantityTotal();
        $this->setPriceTotal();
    }

    /**
     *
     * @param string $name
     */
    public function removeCartProduct(string $name): void
    {
        unset($this->items[$name]);
    }

    /**
     * Get how many types of product in the cart
     * @return int
     */
    public function getCount(): int
    {
        return count($this->items);
    }

    /**
     * Set the total quantity of the items in cart
     */
    public function setQuantityTotal(): void
    {
        if ($this->getCount() === 0) {
            $this->totalQuantity = 0;
            return;
        }

        $sum = 0;
        foreach ($this->items as $item) {
            $sum += $item['quantity'];
        }

        $this->totalQuantity = $sum;
    }

    /**
     * Set the total price of the items in cart
     */
    public function setPriceTotal(): void
    {
        if ($this->getCount() === 0) {
            $this->totalPrice = 0.00;
            return;
        }

        $price = 0.00;
        foreach ($this->items as $item) {
            $price += $item['quantity'] * $item['price'];
        }

        $this->totalPrice = $price;
    }

    /**
     *  Format the number
     */
    public static function getRoundNumber(float $number): float
    {
        return round($number, 2);
    }

}
