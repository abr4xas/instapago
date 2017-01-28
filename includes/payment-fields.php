<style type="text/css">.id_customer,.customer_id,.instapago_ccnum,.month,.year,.instapago_cvv,.instapago_cchname{background:#f5f5f5;height:50px;padding-left:20px;font-weight:500;margin-bottom:24px;border-radius:0}.id_customer{width:10%;float:left}.customer_id{width:90%;float:right}label{text-transform:uppercase}.month,.year{max-width:50%;float:left}img.instapago-img{max-width:100%;display:block;margin:0 auto!important;max-height:100%!important;float:none!important}img.visa-mastercard{max-width:70px;float:right}#add_payment_method #payment ul.payment_methods li input,.help,.woocommerce-checkout #payment ul.payment_methods li input{margin:0}select.form-control{background:#f5f5f5;color:#404040;height:50px;font-family:sans-serif;font-size:14px;line-height:1.5;border-radius:0}.required{color:#ff6347;font-weight:700;border:0}.help{position:absolute;top:57%;right:24px;height:16px}.m-bottom{margin-bottom:13px;margin-top:27px}@media (max-width:768px){.customer_id{width:74%;float:right}.id_customer{width:26%;float:left}.help{top:70%}}</style>
<!--
Plugin desarrollado por:
Angel Cruz <me@abr4xas.org>
(http://abr4xas.org)
https://www.behance.net/gallery/37073215/Instapago-Payment-Gateway-for-WooCommerce
-->
<fieldset>
	<div class="woocommerce-checkout-review-order">
		<div class="row">
			<div class="col-md-12 m-bottom">
				<label class="control-label" title="Nombre y Apellido del Tarjetahabiente">Nombre y apellido del tarjetahabiente<abbr class="required" title="Información Necesaria">*</abbr></label>
				<input id="instapago_cchname" maxlength="25" class="form-control instapago_cchname" type="text" autocomplete="off" name="card_holder_name"
					onkeyup="this.value=this.value.replace(/[^A-Za-z \u00f1\`]/g, '');">
			</div>
			<div class="col-md-12 m-bottom">
				<label class="control-label" title="Identificación del Tarjetahabiente">Identificación del Tarjetahabiente <abbr class="required" title="Información Necesaria">*</abbr></label>
				<div class="form-group">
					<select class="form-control input-inline id_customer">
                        <option value="V" selected>V</option>
                        <option value="E">E</option>
                        <option value="J">J</option>
                        <option value="G">G</option>
                    </select>
					<input id="instapago_cchnameid" class="form-control customer_id" type="text" autocomplete="off" pattern=".{6,}" minlength="6"
						maxlength="8" name="user_dni" onkeyup="this.value=this.value.replace(/[^0-9\.]/g, '');">
				</div>
			</div>
			<div class="col-md-12 m-bottom">
				<label class="control-label" title="Número de Tarjeta *">Número de Tarjeta <abbr class="required" title="Información Necesaria">*</abbr></label>
				<img src="<?php echo plugins_url('instapago/images/instapago-visa-mastercard.png'); ?>" class="img-responsive visa-mastercard help" alt="Número de Tarjeta">
				<input id="instapago_ccnum" class="form-control instapago_ccnum" type="text" name="valid_card_number" maxlength="16"
					autocomplete="off" placeholder="xxxx xxxx xxxx xxxx" onkeyup="this.value=this.value.replace(/[^0-9\.]/g, '');">
			</div>
			<div class="col-md-12 m-bottom">
				<label class="control-label" title="Fecha de Vencimiento">fecha de vencimiento<abbr class="required" title="Información Necesaria">*</abbr></label>
				<div class="form-group">
					<select name="exp_month" id="exp_month" class="form-control input-inline input-small month">
                        <option value="-1">MES</option>
                    <?php
                        $months = [
                            '01'=>'01',
                            '02'=>'02',
                            '03'=>'03',
                            '04'=>'04',
                            '05'=>'05',
                            '06'=>'06',
                            '07'=>'07',
                            '08'=>'08',
                            '09'=>'09',
                            '10'=>'10',
                            '11'=>'11',
                            '12'=>'12',
                        ];
                        foreach($months as $mes=>$cod_mes)
                        {
                            $m = date('m');
                            if ($mes == $m) {
                                echo '<option value="'. $cod_mes . '" selected>' . $mes . '</option>';
                            } else {
                                echo '<option value="'. $cod_mes . '">' . $mes . '</option>';
                            }
                        }
                    ?>
                    </select>
					<select name="exp_year" id="exp_year" class="form-control input-inline input-small year">
                        <option value="NULL">AÑO</option>
                        <?php

                            for($y=date('Y');$y<=date('Y')+10;$y++) {
                                $x = date('Y');
                            if ($y == $x){
                                echo '<option value="'. $y .'" selected>'. $y .'</option>';
                            } else {
                                echo '<option value="'. $y .'">'. $y .'</option>';
                            }
                         ?>
                        <?php }?>
                    </select>
				</div>
			</div>
			<div class="col-md-12 m-bottom">
				<label class="control-label" for="instapago_cvv" title="Cod. de Seguridad">
                        Cód de Seguridad <abbr class="required" title="Información Necesaria">*</abbr></label>
				<input type="password" class="form-control instapago_cvv" id="instapago_cvv" name="cvc_code" maxlength="3" placeholder="xxx"
					onkeyup="this.value=this.value.replace(/[^0-9\.]/g, '');">
				<span class="help">3 digitos.</span>
			</div>
			<div class="col-md-12 m-bottom">
				<p class="text-center">Está transacción sera procesada de forma segura gracias a la plataforma de</p>
				<img src="<?php echo plugins_url('instapago/images/instapago.png'); ?>" class="instapago-img img-responsive" alt="Instapago Banesco">
			</div>
		</div>
	</div>
	<!--
Plugin desarrollado por:
Angel Cruz <me@abr4xas.org>
(http://abr4xas.org)
https://www.behance.net/gallery/37073215/Instapago-Payment-Gateway-for-WooCommerce
-->
</fieldset>
<script type="text/javascript">
	jQuery('#instapago_cchname').keypress(function(){
        if (jQuery(this).val().length < 1 ) {
            jQuery(this).css('border-color','#F05A1A');
            jQuery(this).attr('title','Debe ingresar el nombre y apellido del tarjetahabiente');
        }else{
            jQuery(this).css('border-color','#2abb67');
        }
    });
    jQuery('#instapago_cchnameid').keypress(function(){
        if (jQuery(this).val().length < 5 || jQuery(this).val().length > 8) {
            jQuery(this).css('border-color','#F05A1A');
            jQuery(this).attr('title','Su cédula debe ser mayor que 6 y menor que 8 digitos');
        }else{
            jQuery(this).css('border-color','#2abb67');
        }
    });
    jQuery('#instapago_ccnum').keypress(function(){
        if (jQuery(this).val().length >= 17) {
            jQuery(this).css('border-color','#F05A1A');
            jQuery(this).attr('title','Debe ingresar los números de su tarjeta');
        }else {
            jQuery(this).css('border-color','#2abb67');
        }
    });
    jQuery('#instapago_cvv').keypress(function(){
        if (jQuery(this).val().length < 1) {
            jQuery(this).css('border-color','#F05A1A');
            jQuery(this).attr('title','Debe ingresar el código de validación de su tarjeta');
        }else{
            jQuery(this).css('border-color','#2abb67');
        }
    });
</script>