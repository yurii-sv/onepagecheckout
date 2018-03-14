<table class="table">
    <thead>
        <tr>
            <td class="name t-head" colspan="2"><?= $text_product ?></td>
            <td class="price t-head"><?= $text_price ?></td>
            <td class="quantity t-head"><?= $text_quantity ?></td>
            <td class="t-head"></td>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $product): ?>
            <tr>
                <td class="image">
                    <a href="<?= $product['href'] ?>" title="<?php echo $product['name'] ?>">
                        <img src="<?php echo  $product['thumb'] ?>" style="max-width: 100px" alt="<?php echo $product['name'] ?>">
                    </a>
                </td>
                <td class="name">
                    <a href="<?= $product['href'] ?>"><?= $product['name'] ?></a>
                    <?php if (!$product['stock']) { ?>
                        <span class="text-danger">***</span>
                    <?php } ?>
                    <div class="p-model">
                        <?= $product['model'] ?>
                    </div>
                    <div class="cart-option">
                        <?php foreach ($product['option'] as $option) { ?>
                            - <small><?= $option['name'] ?>: <?= $option['value'] ?></small>
                            <br/>
                        <?php } ?>
                        <?php if ($product['recurring']) { ?>
                            - <small><?= $text_payment_profile ?>: <?= $product['profile_name'] ?></small>
                        <?php } ?>
                    </div>
                </td>
                <td class="price"><?= $product['price'] ?></td>
                <td class="quantity">
                    <div class="quantity-group">
                        <input type="button" id="minus" value="-" class="form-control btn-qt" onclick="quantityMinus(<?= $product['cart_id'] ?>)" />
                        <input type="text" name="quantity" value="<?= $product['quantity'] ?>" size="2" id="input-quantity<?= $product['cart_id'] ?>" class="form-control cart-quantity" onchange="updateQuantity(<?= $product['cart_id'] ?>, this.value)" />
                        <input type="button" id="plus" value="&#43;" class="form-control btn-qt" onclick="quantityPlus(<?= $product['cart_id'] ?>)"/>
                    </div>
                </td>
                <td class="remove">
                    <button type="button" class="btn btn-danger" onclick="updateQuantity(<?= $product['cart_id'] ?>, 0)">
                        <i class="fa fa-times-circle"></i>
                    </button>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php foreach ($totals as $total): ?>
            <tr class="subtotal">
                <td class="name subtotal" colspan="2">
                    <strong><?php echo $total['title']; ?>:</strong>
                </td>
                <td class="price" colspan="3"><?php echo $total['text']; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
