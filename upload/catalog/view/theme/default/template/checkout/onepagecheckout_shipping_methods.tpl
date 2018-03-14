<select onChange="updateShipping(this)" name="shipping_method" id="shipping-method" class="form-control large-field">
    <?php foreach ($shipping_methods as $shipping_method) { ?>
    <option value='{"title": "<?= $shipping_method['title'] ?>", "code": "<?= $shipping_method['value'] ?>", "comment":"", "shipping_method":"<?= $shipping_method['value'] ?>", "cost":"<?= $shipping_method['cost'] ?>","tax_class_id":""}' class="form-control large-field <?= substr($shipping_method['value'], strpos($shipping_method['value'], '.')+1 ) ?>" <?php if($shipping_method['selected']) { ?> selected="selected" <?php } ?> >
    <?= $shipping_method['title'] ?>
    </option>
    <?php } ?>
</select>