<style type="text/css">* {-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;}form > div {clear: both;overflow: hidden;padding: 1px;margin: 0 0 10px 0;}form > div > fieldset > div > div {margin: 0 0 5px 0;}form > div > label,legend {width: 25%;float: left;padding-right: 10px;}form > div > div,form > div > fieldset > div {width: 100%;float: right;}form > div > fieldset label {font-size: 90%;}fieldset {border: 0;padding: 0;}input[type=text],input[type=email],input[type=url],input[type=password] {width: 100%;color: #666;border: 1px solid #ccc;height: 50px;}input[type=text]:focus,input[type=email]:focus,input[type=url]:focus,input[type=password]:focus,textarea:focus {outline: 0;border-color: #4697e4;}.instacontainer {width: 100%}.div1,.div2 {display: inline-block;}.div1 {float: left;width: 10%;}.div2 {width: 90%;}.div3 {float: left;width: 66%;}.div4 {width: 20%;float: left;}.div5 {width: 13%;padding-top: 14px;float: left;}.div6 {width: 50%;float: left;}select {background: #f5f5f5;color: #404040;height: 50px;font-family: sans-serif;font-size: 14px;line-height: 1.5;border-radius: 0;width: 100%}.instapago-img {margin: 0 auto;}.img-responsive {display: block;max-width: 100%;height: auto;}img.visa-mastercard {margin: 0 auto;}.text-center {text-align: center;}.copy {float: left;width: 100%;}img.instapago-img {left: 30%;}@media (max-width:768px) {.div1 {float: left;width: 13%;}.div2 {width: 87%;}.div3 {float: left;width: 77%;}.div4 {width: 23%;}img.instapago-img {left: 3%;}.div5 {display: none;}}@media (max-width: 600px) {form > div {margin: 0 0 15px 0;}form > div > label,legend {width: 100%;float: none;margin: 0 0 5px 0;}form > div > div,form > div > fieldset > div {width: 100%;float: none;}input[type=text],input[type=email],input[type=url],input[type=password],textarea,select {width: 100%;}.div1 {float: left;width: 13%;}.div2 {width: 87%;}.div3 {float: left;width: 60%;}.div4 {width: 40%;float: right;}.div5 {display: none;}}@media (min-width: 1200px){form > div > label,legend {text-align: right;}}</style>
<!--
Plugin desarrollado por:
Angel Cruz <me@abr4xas.org>
(http://abr4xas.org)
https://www.behance.net/gallery/37073215/Instapago-Payment-Gateway-for-WooCommerce
-->
       <div class="instacontainer">
            <div>
                <div>
                    <input id="instapago_cchname"  maxlength="25"  type="text" autocomplete="off" name="card_holder_name"
					onkeyup="this.value=this.value.replace(/[^A-Za-z \u00f1\`]/g, '');" class="field text fn" tabindex="1" placeholder="NOMBRE Y APELLIDO DEL TARJETAHABIENTE"  title="Nombre y Apellido del Tarjetahabiente">
                </div>
            </div>
            <div class="div1">
                <select id="Field106" name="Field106" class="field select medium" tabindex="2" title="Identificación del Tarjetahabiente">
                    <option value="V" selected="">V</option>
                    <option value="E">E</option>
                    <option value="J">J</option>
                    <option value="G">G</option>
                </select>
            </div>
            <div class="div2">
                <input id="instapago_cchnameid" type="text" autocomplete="off" pattern=".{6,}" minlength="6"
						maxlength="8" name="user_dni" onkeyup="this.value=this.value.replace(/[^0-9\.]/g, '');" tabindex="3" placeholder="IDENTIFICACIÓN DEL TARJETAHABIENTE">
            </div>
            <div class="div3">
                <input id="instapago_ccnum" type="text" name="valid_card_number" maxlength="16"
					autocomplete="off" onkeyup="this.value=this.value.replace(/[^0-9\.]/g, '');" tabindex="4" placeholder="NÚMERO DE TARJETA">
            </div>
            <div class="div4">
                <input type="password" id="instapago_cvv" name="cvc_code" maxlength="3" onkeyup="this.value=this.value.replace(/[^0-9\.]/g, '');" tabindex="5" placeholder="CÓD DE SEGURIDAD">
            </div>
            <div class="div5">
                <img src="<?php echo plugins_url('instapago/images/instapago-visa-mastercard.png'); ?>" class="img-responsive visa-mastercard help"
                    alt="Número de Tarjeta">
            </div>
            <div class="div6">
                <select name="exp_month" id="exp_month" class="field select medium" tabindex="6">
                        <option value="-1">Mes</option>
                    <?php
                        $months = [
                            '01'=> '01',
                            '02'=> '02',
                            '03'=> '03',
                            '04'=> '04',
                            '05'=> '05',
                            '06'=> '06',
                            '07'=> '07',
                            '08'=> '08',
                            '09'=> '09',
                            '10'=> '10',
                            '11'=> '11',
                            '12'=> '12',
                        ];
                        foreach ($months as $mes=>$cod_mes) {
                            $m = date('m');
                            if ($mes == $m) {
                                echo '<option value="'.$cod_mes.'" selected>'.$mes.'</option>';
                            } else {
                                echo '<option value="'.$cod_mes.'">'.$mes.'</option>';
                            }
                        }
                    ?>
                    </select>
            </div>
            <div class="div6">
                <select name="exp_year" id="exp_year" class="field select medium" tabindex="7">
                        <option value="-1">AÑO</option>
                        <?php
                            for ($y = date('Y'); $y<=date('Y')+10; $y++) {
                                $x = date('Y');
                                if ($y == $x) {
                                    echo '<option value="'.$y.'" selected>'.$y.'</option>';
                                } else {
                                    echo '<option value="'.$y.'">'.$y.'</option>';
                                }
                            }
                        ?>
                    </select>
            </div>
            <br>
            <div class="copy">
			    <p class="text-center">
                    Está transacción sera procesada de forma segura gracias a la plataforma de
            <img src="<?php echo plugins_url('instapago/images/instapago.png'); ?>" class="instapago-img img-responsive" alt="Instapago Banesco">
                </p>
            </div>
        </div>
	<!--
Plugin desarrollado por:
Angel Cruz <me@abr4xas.org>
(http://abr4xas.org)
https://www.behance.net/gallery/37073215/Instapago-Payment-Gateway-for-WooCommerce
-->
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