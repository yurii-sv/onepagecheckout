<?= $header ?>
<div class="container">
    <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
            <li><a href="<?= $breadcrumb['href'] ?>"><?= $breadcrumb['text'] ?></a></li>
        <?php } ?>
    </ul>
    <div id="errors">
        <?php if ($error_warning) { ?>
        <div class="alert alert-danger">
            <i class="fa fa-exclamation-circle"></i> <?= $error_warning ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
    </div>
    <div class="row">
        <?= $column_left ?>
        <?php
            if ($column_left && $column_right) {
                $class = 'col-sm-6';
            } elseif ($column_left || $column_right) {
                $class = 'col-sm-9';
            } else {
                $class = 'col-sm-12';
            }
        ?>
        <div id="content">
            <h1 style="text-align: center"><?= $heading_title ?></h1>

            <div class="container">
                <div class="checkout checkout-checkout">
                    <?= $content_top ?>

                    <div class="payment row">
                        <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 payment-data">
                            <div class="t-head"><?= $text_customer ?></div>
                            <?php if(!$c_logged) { ?>
                                <div id="login_warning" class='checkout-content note'>
                                    <?= $text_notlogged ?>
                                </div>
                            <?php } ?>
                            <div id="payment-address">
                                <div class="checkout-content" style="overflow: hidden; display: block;">
                                    <div class="fields-group">
                                        <?php if($c_logged) { ?>
                                            <label for="firstname-ch"><?= $text_full_name ?>:</label><br>
                                            <input type="text" class="form-control large-field" id="firstname-ch" name="firstname" value="<?= $c_name ?>" readonly="true"/>
                                        <?php } else { ?>
                                            <label for="firstname-ch"><span class="required">*</span> <?= $text_full_name ?>:</label><br>
                                            <input type="text" id="firstname-ch" name="firstname" value="" class="form-control large-field">
                                            <span class="error"></span>
                                        <?php }?>
                                    </div>

                                    <div class="fields-group">
                                        <label for="telephone-ch"><span class="required">*</span> <?= $text_telephone ?>:</label><br>
                                        <input type="tel" id="telephone-ch" name="telephone" value="<?= $telephone ?>" placeholder="<?= $text_phone_format ?>" class="form-control large-field">
                                        <span class="error"></span>
                                    </div>

                                    <div class="fields-group">
                                        <label for="email-ch"><?= $text_email ?>:</label><br>
                                        <input type="text" id="email-ch" name="email" value="<?= $email ?>" class="form-control large-field">
                                        <span class="error"></span>
                                    </div>

                                    <div class="fields-group">
                                        <label for="delivery"><?= $text_delivery_method ?>:</label>
                                        <div id="shipping">
                                            <?= $shipping_methods ?>
                                        </div>

                                        <br>
                                        <input type='hidden' name='delivery-type' value='delivery'/>
                                        <label for='address_1'><?= $text_delivery_type_2 ?>:</label><br/>
                                        <input type="text" name="address_1" id="address_1" value="<?= $address_1 ?>" class="form-control large-field" placeholder="<?= $text_delivery_placeholder ?>">
                                        <span class="error"></span>
                                    </div>

                                    <div class="fields-group">
                                        <label for="city-ch"><?= $text_town ?>:</label><br>
                                        <input type="text" id="city-ch" name="city" value="<?= $city ?>" class="form-control large-field">
                                        <span class="error"></span>
                                    </div>

                                    <div class="fields-group" style="">
                                        <label for="payment_select"><?= $text_payment_method ?>:</label><br>
                                        <div id="payment">
                                            <?= $payment_methods ?>
                                        </div>
                                    </div>

                                    <div class="fields-group">
                                        <label for="comment_field"><?= $text_comment ?>:</label><br>
                                        <input type="text" id="comment_field" class="form-control large-field" name="comment" value="<?= $comment ?>">
                                    </div>
                                </div>

                                <div class="fields-group">
                                    <?php if ($modules) { ?>
                                        <div>
                                            <?php
                                                foreach ($modules as $module) {
                                                    echo $module;
                                                }
                                            ?>
                                        </div>
                                    <?php } ?>
                                </div>

                            </div>
                        </div>
                        <div class="col-lg-7 col-md-7 col-sm-12 col-xs-12 checkout-data">
                            <div id="cartInfo" class="cart-info table-responsive">
                                <?= $cart ?>
                            </div>
                            <div id="confirm">
                                <div class="payment">
                                    <div id="ajax-button-confirm" class=" btn btn-lg btn-success">
                                        <?= $text_confirm ?>
                                    </div>
                                    <div id="payment-info"  style="display: none;"></div>
                                </div>
                            </div>
                            <div class="col-xs-12 checkout-subinfo">
                                <?= $content_bottom ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?= $column_right ?>
            </div>
        </div>
        <div id="LoginModal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h3><?= $text_returning_customer ?></h3>
                    </div>
                    <div class="modal-body">

                        <p><strong><?= $text_i_am_returning_customer ?></strong></p>
                        <form  method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label class="control-label" for="input-email"><?= $entry_email ?></label>
                                <input type="text" name="email" value="<?= $email ?>" placeholder="<?= $entry_email ?>" id="input-email" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="input-password"><?= $entry_password ?></label>
                                <input type="password" name="password" value="" placeholder="<?= $entry_password ?>" id="input-password" class="form-control" />
                                <a class="pull-right" href="<?= $forgotten ?>"><?= $text_forgotten ?></a>
                            </div>
                            <div class="btn btn-primary submit-login-form" ><?= $button_login ?></div>
                            <div class="text-right"><a href="<?= $register ?>"> <?=$text_register;?></a></div>
                        </form>
                        <div class="errors-block"></div>
                    </div>
                </div>

            </div>

        </div>
    </div>
    <?php
        if ($default_sm) {
            $data_sm = 1;
        } else {
            $data_sm = 0;
        }
    ?>
    <div id="dataSM" style="display: none;" data-sm="<?= $data_sm ?>"></div>
</div>

<script type="text/javascript"><!--

function updateShipping(s) {
    var data = JSON.parse(s.value);
    $.ajax({
        url: 'index.php?route=checkout/shipping_method/save',
        type: 'post',
        data: data,
        dataType: 'json',
        success: function(json) {
            if (json['error']) {
                if (json['error']['warning']) {
                 alert(json['error']['warning']);
                }
            } else {
                updateCart();
            }
        },
        error: function(xhr, ajaxOptions, thrownError) {
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    }); //ajax
}

    function updatePayments(s) {
        var data = JSON.parse(s.value);
        $.ajax({
            url: 'index.php?route=checkout/payment_method/save',
            type: 'post',
            data: data,
            dataType: 'json',
            success: function(json) {
                if (json['error']) {
                    if (json['error']['warning']) {
                        alert(json['error']['warning']);
                    }
                } else {
                    updateCart();
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        }); //ajax
    }

function updateCart() {
    $.ajax({
        url: 'index.php?route=checkout/onepagecheckout/cart',
        type: 'get',
        success: function(cart) {
            if (cart.status === false) {
                document.location.href = document.origin
            } else {
                $('#cartInfo').html(cart);
                updateShippingView();
                updatePaymentView();
                updateErrors();
                $('#ajax-button-confirm').css('display', 'inline-block');
                $('#payment-info').css('display', 'none');
            }
        }
    });
}

function updateShippingView() {
    $.ajax({
        url: 'index.php?route=checkout/onepagecheckout/shipping',
        type: 'get',
        success: function(shipping) {
            $('#shipping').html(shipping);
        }
    });
}

function updateErrors() {
    $.ajax({
        url: 'index.php?route=checkout/onepagecheckout/error',
        type: 'get',
        success: function(errors) {
            if (errors.error) {

                $text = '<div class="alert alert-danger"> <i class="fa fa-exclamation-circle"></i>'+ errors.error +'<button type="button" class="close" data-dismiss="alert">&times;</button></div>';
                $('#errors').html($text);
            } else {
                $('#errors').html('');
            }
        }
    });
}

function updatePaymentView() {
    $.ajax({
        url: 'index.php?route=checkout/onepagecheckout/payment',
        type: 'get',
        success: function(payment) {
            $('#payment').html(payment);
        }
    });
}

function updateQuantity(id, quantity) {

    var test = 'quantity['+id +']='+ quantity;

    $.ajax({
        url: 'index.php?route=checkout/onepagecheckout/updatequantity',
        type: 'post',
        data: test,
        success: function(data) {
            updateCart();
        },
        error: function(xhr, ajaxOptions, thrownError) {
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    }); //ajax
}

// increase number of product
function quantityMinus(id) {
    var current = parseInt($("#input-quantity"+id).val());
    var quantity = current - 1;
    updateQuantity(id, quantity);
}

// decrease of product
function quantityPlus(id) {
    var current = parseInt($("#input-quantity"+id).val());
    var quantity = current + 1;
    updateQuantity(id, quantity);
}

$(document).ready(function() {

    var default_sm = parseInt($("#dataSM").attr("data-sm"));
    if (default_sm === 1) {
        var sm = document.getElementById("shipping-method");
        updateShipping(sm);
    }

    // Mask for the telephone
    $("#telephone-ch").mask("380999999999", {autoсlear: false});

    $('#LoginModal .submit-login-form').on('click', function(){
        $.ajax({
            url: 'index.php?route=checkout/onepagecheckout/ajaxlogin',
            type: 'post',
            data: $('#LoginModal #input-email, #LoginModal #input-password '),
            dataType: 'json',
            beforeSend: function () {
                $('#firstname-ch+.error').html('');
                $('#email-ch+.error').html('');
                $('#telephone-ch+.error').html('');
                $('#address_1+.error').html('');
                $('#city-ch+.error').html('');
            },
            success: function(json) {
               if(json.errors != 0) {

                   if(typeof json.errors.warning!='undefined' && json.errors.warning!='') {
                       $('#LoginModal .errors-block').html(json.errors.warning);
                   }
                   if(typeof json.errors.errors!='undefined' && json.errors.errors!='') {
                       $('#LoginModal .errors-block').append( '<br>' + json.errors.error );
                   }

               } else if(json.errors==0){

                   $('#firstname-ch').prop('value',json.c_name);
                   $('#city-ch').prop('value',json.city);
                   $('#address_1').prop('value',json.address_1);
                   $('#email-ch').prop('value',json.email);
                   $('#telephone-ch').prop('value',json.telephone);
                   $('#LoginModal').modal('hide');
                   $('#login_warning').html('');

                   updateCart();
               }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        }); //ajax

        return false;
    });

    $('#ajax-button-confirm').on('click', function () {

        $.ajax({
            url: 'index.php?route=checkout/onepagecheckout',
            type: 'post',
            data: $('.checkout-checkout .payment-data input[type=\'text\'], .checkout-checkout .payment-data input[type=\'tel\'], .checkout-checkout .payment-data input[type=\'radio\']:checked, .checkout-checkout .payment-datainput input[type=\'checkbox\']:checked, .checkout-checkout .payment-data  select '),
            dataType: 'json',
            beforeSend: function () {
                $('#ajax-button-confirm').addClass('preloader');
                $('#firstname-ch+.error').html('');
                $('#email-ch+.error').html('');
                $('#telephone-ch+.error').html('');
                $('#address_1+.error').html('');
                $('#city-ch+.error').html('');
            },
            complete: function () {
                $('#ajax-button-confirm').removeClass('preloader');
            },
            success: function (json) {
                if (json.error) {
                    if (json['error']['firstname']) {
                        $('#firstname-ch+.error').html(json['error']['firstname']);
                    }

                    if (json['error']['email']) {
                        $('#email-ch+.error').html(json['error']['email']);
                    }

                    if (json['error']['telephone']) {
                        $('#telephone-ch+.error').html(json['error']['telephone']);
                    }

                    if (json['error']['address_1']) {
                        $('#address_1+.error').html(json['error']['address_1']);
                    }

                    if (json['error']['city']) {
                        $('#city-ch+.error').html(json['error']['city']);
                    }

                } else if(json['cod']) {
                    $.ajax({
                        type: 'get',
                        url: 'index.php?route=payment/cod/confirm',
                        cache: false,
                        beforeSend: function() {
                            $('#ajax-button-confirm').button('loading');
                        },
                        complete: function() {
                            $('#ajax-button-confirm').button('reset');
                        },
                        success: function() {
                            location = 'index.php?route=checkout/success';
                        }
                    });

                } else if(json['payment']) {
                    $('#payment-info').html(json['payment']);
                    $('#payment-info').css('display', 'block');
                    $('#ajax-button-confirm').css('display', 'none');
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });

});
//--></script>

<?= $footer ?>
