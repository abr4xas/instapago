<!--
Plugin desarrollado por:
Angel Cruz <me@abr4xas.org>
(http://abr4xas.org)
https://www.behance.net/gallery/37073215/Instapago-Payment-Gateway-for-WooCommerce
-->
    <div class="instapago-form--container">

      <input type="text" id="instapago_cchname" class="instapago-form--input instapago-form--name" name="card_holder_name" tabindex="1" title="Nombre y Apellido del Tarjetahabiente" placeholder="Nombre y Apellido del Tarjetahabiente" autocomplete="off" maxlength="25" onkeyup="this.value=this.value.replace(/[^A-Za-z \u00f1\`]/g, '');">
      <?php //class="field text fn " eliminadas para evitar conflictos de clases css?>

      <select id="Field106" class="instapago-form--select instapago-form--tipe" name="Field106" tabindex="2" title="Identificación del Tarjetahabiente" >
        <option value="V" selected="">V</option>
        <option value="E">E</option>
        <option value="J">J</option>
        <option value="G">G</option>
      </select>
      <?php //class="field select medium" eliminadas para evitar conflictos de clases css?>

      <input type="text" id="instapago_cchnameid" class="instapago-form--input instapago-form--id" name="user_dni" tabindex="3" placeholder="Identificación del Tarjetahabiente" autocomplete="off" minlength="6" maxlength="8" pattern=".{6,}" onkeyup="this.value=this.value.replace(/[^0-9\.]/g, '');" >



              <input
              type="text"
              id="instapago_ccnum"
              class="instapago-form--input instapago-form--tdc-number"
              name="valid_card_number"
              tabindex="4"
              placeholder="Número de tarjeta"
              autocomplete="off"
              maxlength="16"
              onkeyup="this.value=this.value.replace(/[^0-9\.]/g, '');"
              >



                <input
                type="password"
                id="instapago_cvv"
                class="instapago-form--input instapago-form--ccv"
                name="cvc_code"
                tabindex="5"
                placeholder="Cód de seguridad"
                autocomplete="off"
                maxlength="3"
                onkeyup="this.value=this.value.replace(/[^0-9\.]/g, '');"
                >

            <div class="tdc-logos">
              <img src="<?php echo plugins_url('instapago/assets/images/instapago-visa-mastercard.png'); ?>" class="instapago-img" alt="Número de Tarjeta">
              <?php //class="img-responsive visa-mastercard help" eliminadas para evitar conflictos de clases css?>
            </div></br>

            <p class="instapago-form--txt-help">Fecha de vencimiento</p>
            </br>
            <select id="exp_month" class="instapago-form--select instapago-form--exp-month" name="exp_month" tabindex="6">
                <?php //class="field select medium " eliminadas para evitar conflictos de clases css?>
                <option value="-1">Mes</option>
                <?php
                    $months = [
                        '01' => '01',
                        '02' => '02',
                        '03' => '03',
                        '04' => '04',
                        '05' => '05',
                        '06' => '06',
                        '07' => '07',
                        '08' => '08',
                        '09' => '09',
                        '10' => '10',
                        '11' => '11',
                        '12' => '12',
                    ];
                    foreach ($months as $mes => $codMes) {
                        $mesActual = date('m');
                        if ($mes == $mesActual) {
                            echo '<option value="'.$codMes.'" selected>'.$mes.'</option>';
                        } else {
                            echo '<option value="'.$codMes.'">'.$mes.'</option>';
                        }
                    }
                ?>
           </select>


                <select id="exp_year" class="instapago-form--select instapago-form--exp-year" name="exp_year" tabindex="7">
                       <?php //class="field select medium" eliminadas para evitar conflictos de clases css?>
                        <option value="-1" selected>AÑO</option>
                        <?php
                            /*for ($y = date('Y'); $y <= date('Y') + 10; $y++)
                            Utilizar la funcion date limita el uso de tarjetas emitidas en el año en curso del sistema operativo,
                            con un rango de 10 años se asegura el uso de tarjetas vijentes.
                            */
                            for ($y = 2010; $y<=2018+10; $y++) {
                                $x = date('Y');
                                if ($y == $x) {
                                    echo '<option value="'.$y.'" selected>'.$y.'</option>';
                                } else {
                                    echo '<option value="'.$y.'">'.$y.'</option>';
                                }
                            }
                        ?>
                    </select>

            <br>
            <div class="instapago-copy instapago-text-center">
			    <p>
                    Está transacción sera procesada de forma segura gracias a la plataforma de
                </p>
                <img src="<?php echo plugins_url('instapago/assets/images/instapago.png'); ?>" class="instapago-img" alt="Instapago Banesco">
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
