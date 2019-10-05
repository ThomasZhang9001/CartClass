<?php
declare(strict_types=1);
require_once 'cart.php';

use Cart\Cart;
use PHPUnit\Framework\TestCase;

final class testCart extends TestCase
{
    protected  $cart;
    
    public function __construct()
    {
        parent::__construct();
        $this->cart =  Cart::getCart();
    }

    public function testGetCart(): void
    {
        $this->assertEquals(
            5,
            count($this->cart->validProductList)
        );

        $this->assertEquals(
            562.131,
             $this->cart->validProductList[2]['price']
        );

        $this->assertEquals(
            "Axe",
             $this->cart->validProductList[1]['name']
        );

        $this->assertInstanceOf(
            Cart::class,
            $_SESSION['cart']
        );
    }

    public function testAddItem(): void
    {
       // Because data would be persistent. We clean up data first, to make each test isolated.
        $this->cart->clearCart();

        // First time adding item
         $this->cart->addItem('Hacksaw');

        $this->assertEquals(
            1,
             $this->cart->getCount()
        );
        $this->assertEquals(
            18.45,
             $this->cart->items['Hacksaw']['price']
        );
        $this->assertEquals(
            1,
             $this->cart->items['Hacksaw']['quantity']
        );

        // Check total
        $this->assertEquals(
            18.45,
            $this->cart->totalPrice
        );
        $this->assertEquals(
            1,
            $this->cart->totalQuantity
        );

        // Now we add the same item and quantity is 3
         $this->cart->addItem('Hacksaw', 3);

        $this->assertEquals(
            1,
             $this->cart->getCount()
        );

        $this->assertEquals(
            4,
             $this->cart->items['Hacksaw']['quantity']
        );

        // Add another type of product
        $this->cart->addItem('Chisel');
        // Check total
        $this->assertEquals(
            86.7,
            $this->cart->totalPrice
        );
        $this->assertEquals(
            5,
            $this->cart->totalQuantity
        );

        // Test clearCart and do clean up
        $this->cart->clearCart();
        $this->assertEquals(
            0,
            $this->cart->getCount()
        );
        // Check total
        $this->assertEquals(
            0,
            $this->cart->totalPrice
        );
        $this->assertEquals(
            0,
            $this->cart->totalQuantity
        );
    }

    public function testGetValidProduct(): void
    {
        // Should has a exception
        $this->expectException(InvalidArgumentException::class);
        // First time adding item
        $this->cart->getValidProduct('Invalid product');
    }

    public function testRemoveItem() :void
    {
        // Because data would be persistent. We clean up data first, to make each test isolated.
        $this->cart->clearCart();
        // Add some item to test
        $this->cart->addItem('Axe');
        $this->cart->addItem('Sledgehammer', 3);

        $this->cart->removeItem('Sledgehammer');

        // Should still has two types of product
        $this->assertEquals(
            2,
            $this->cart->getCount()
        );

        $this->assertEquals(
            1,
            $this->cart->items['Axe']['quantity']
        );

        $this->assertEquals(
            2,
            $this->cart->items['Sledgehammer']['quantity']
        );

        // Check total
        $this->assertEquals(
            442,
            $this->cart->totalPrice
        );
        $this->assertEquals(
            3,
            $this->cart->totalQuantity
        );

        // Remove all Sledgehammer
        $this->cart->removeItem('Sledgehammer', 2);

        $this->assertEquals(
            1,
            $this->cart->getCount()
        );

        $this->assertEquals(
            1,
            $this->cart->items['Axe']['quantity']
        );

        $this->assertEquals(
            NULL,
            $this->cart->items['Sledgehammer']
        );

        // Check total
        $this->assertEquals(
            190.50,
            $this->cart->totalPrice
        );
        $this->assertEquals(
            1,
            $this->cart->totalQuantity
        );

        // Test clearCart and do clean up
        $this->cart->clearCart();
        $this->assertEquals(
            0,
            $this->cart->getCount()
        );
        // Check total
        $this->assertEquals(
            0,
            $this->cart->totalPrice
        );
        $this->assertEquals(
            0,
            $this->cart->totalQuantity
        );
    }
}
