<?php
// Set PHP strict mode
declare(strict_types=1);
header("Content-type:text/html;charset=utf8");
require_once 'cart.php';

use Cart\Cart;

session_start();

$cart = Cart::getCart();

if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['action'] == 'add') {
    $product = $_POST['product'];
    $cart->addItem($product);
}
if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['action'] == 'remove') {
    $product = $_POST['product'];
    $cart->removeItem($product);
}
if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['action'] == 'clearCart') {
    $cart->clearCart();
}
?>

<link rel='stylesheet' type='text/css' href='index.css'/>
<!--Product List-->
<h1>Product List</h1>
<table class="table">
    <tr>
        <th>Product Name</th>
        <th>Product Price</th>
        <th>Action</th>
    </tr>
    <?
    foreach ($cart->validProductList as $product) { ?>
        <form action="./index.php" method="post">
            <tr>
                <td>
                    <?= $product['name']; ?>
                </td>
                <td>
                    <?= Cart::getRoundNumber($product['price']); ?>
                </td>
                <td>
                    <input type="submit" value="Add me">
                </td>
            </tr>
            <input type="hidden" name="product" value=<?= $product['name']; ?>>
            <input type="hidden" name="action" value="add">
        </form>
    <? } ?>
</table>

<br/>

<!--Cart List-->

<h1>My Shopping Cart</h1>
<table class="table">
    <tr>
        <td>Product Name</td>
        <td>Price</td>
        <td>Quantity</td>
        <td>Action</td>
    </tr>


    <? if (!empty($cart->items)): ?>
        <? foreach ($cart->items as $product) { ?>
            <form action="./index.php" method="post">
                <tr>
                    <td>
                        <?= $product['name']; ?>
                    </td>
                    <td>
                        <?= Cart::getRoundNumber($product['price']); ?>
                    </td>
                    <td>
                        <?= $product['quantity']; ?>
                    </td>
                    <td>
                        <input type="submit" value="Remove one">
                    </td>
                </tr>
                <input type="hidden" name="product" value=<?= $product['name']; ?>>
                <input type="hidden" name="action" value="remove">
            </form>
        <? }
        ?>
        <tr>
            <td></td>
            <td>Total Price: <?= Cart::getRoundNumber($cart->totalPrice); ?></td>
            <td>Total Quantity: <?= Cart::getRoundNumber($cart->totalQuantity); ?></td>
            <form action="./index.php" method="post">
                <td><input type="submit" value="Clear Cart"></td>
                <input type="hidden" name="action" value="clearCart">
            </form>
        </tr>
    <? else: ?>
        <tr>
            <td class="td_empty">No Item Now.</td>
        </tr>
    <? endif; ?>

</table>
