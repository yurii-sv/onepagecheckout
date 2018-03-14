<select onChange="updatePayments(this)" id="payment_select" name="payment_method" class="form-control large-field">
    <?php foreach ($payment_methods as $payment_method) { ?>
    <option value='{"title": "<?= $payment_method['title'] ?>", "code": "<?= $payment_method['code'] ?>", "payment_method": "<?= $payment_method['code'] ?>", "agree": "1", "comment": ""}' class="payment_method_value <?= $payment_method['code'] ?>" style="" <?php if($payment_method['selected']) { ?> selected="selected" <?php } ?> >
    <?= $payment_method['title'] ?>
    </option>
    <?php } ?>
</select>