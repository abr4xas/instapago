<!--
Plugin desarrollado por:
Angel Cruz <bullgram@gmail.com>
(http://abr4xas.org)
https://angelcruz.dev
-->
<div class="instapago-form--container">
    <div id="div_cliente">
        <input type="text" id="instapago_cchname" class="instapago-form--input instapago-form--name" name="card_holder_name" tabindex="1" title="Nombre y Apellido del Tarjetahabiente" placeholder="Nombre y Apellido del Tarjetahabiente" autocomplete="off" maxlength="25" onkeyup="this.value=this.value.replace(/[^A-Za-z \u00f1\`]/g, '');">

        <?php //class="field text fn " eliminadas para evitar conflictos de clases css
        ?>
        <select id="Field106" class="instapago-form--select instapago-form--tipe" name="Field106" tabindex="2" title="Identificación del Tarjetahabiente">
            <option value="V" selected="">V</option>
            <option value="E">E</option>
            <option value="J">J</option>
            <option value="G">G</option>
        </select>
        <input type="text" id="instapago_cchnameid" class="instapago-form--input instapago-form--id" name="user_dni" tabindex="3" placeholder="Identificación del Tarjetaahabiente" autocomplete="off" minlength="6" maxlength="8" pattern=".{6,}" onkeyup="this.value=this.value.replace(/[^0-9\.]/g, '');">
    </div>
    <?php //class="field select medium" eliminadas para evitar conflictos de clases css
    ?>
    <div id="div_tarjetas">
        <input type="text" id="instapago_ccnum" class="instapago-form--input instapago-form--tdc-number" name="valid_card_number" tabindex="4" placeholder="Número de tarjeta" autocomplete="off" maxlength="16" onkeyup="this.value=this.value.replace(/[^0-9\.]/g, '');">
        <input type="password" id="instapago_cvv" class="instapago-form--input instapago-form--ccv" name="cvc_code" tabindex="5" placeholder="Cód de seguridad" autocomplete="off" maxlength="3" onkeyup="this.value=this.value.replace(/[^0-9\.]/g, '');">
        <div class="tdc-logos">
            <img src="<?php echo plugins_url('instapago/assets/images/instapago-visa-mastercard.png'); ?>" class="instapago-img" alt="Número de Tarjeta">
            <?php //class="img-responsive visa-mastercard help" eliminadas para evitar conflictos de clases css
            ?>
        </div>
    </div>
    <div id="div_venc">
        <p class="instapago-form--txt-help">Fecha de vencimiento</p>
        </br>
        <select id="exp_month" class="instapago-form--select instapago-form--exp-month" name="exp_month" tabindex="6">
            <?php //class="field select medium " eliminadas para evitar conflictos de clases css
            ?>
            <option value="-1">Mes</option>
            <?php
            $months = [
                '01',
                '02',
                '03',
                '04',
                '05',
                '06',
                '07',
                '08',
                '09',
                '10',
                '11',
                '12',
            ];
            $mesActual = date('m');
            foreach ($months as $mes) {
                $selected = ($mes == $mesActual) ? 'selected' : '';
                echo '<option value="' . $mes . '" ' . $selected . '>' . $mes . '</option>';
            }
            ?>
        </select>
        <select id="exp_year" class="instapago-form--select instapago-form--exp-year" name="exp_year" tabindex="7">
            <?php //class="field select medium" eliminadas para evitar conflictos de clases css
            ?>
            <option value="-1" selected>AÑO</option>
            <?php
            /**
             * for ($y = date('Y'); $y <= date('Y') + 10; $y++)
             * Utilizar la funcion date limita el uso de tarjetas
             * emitidas en el año en curso del sistema operativo,
             * con un rango de 10 años se asegura el uso de tarjetas vijentes.
             */
            $x = date('Y');
            for ($y = 2010; $y <= 2018 + 10; $y++) {
                $selected = ($y == $x) ? 'selected' : '';
                echo '<option value="' . $y . '" ' . $selected . '>' . $y . '</option>';
            }
            ?>
        </select>
    </div>
    <div class="instapago-copy instapago-text-center">
        <p>
            Está transacción sera procesada de forma segura gracias a la plataforma de
        </p>
        <img src="<?php echo plugins_url('instapago/assets/images/instapago.png'); ?>" class="instapago-img" alt="Instapago Banesco">
    </div>
</div>

<!--
Plugin desarrollado por:
Angel Cruz <bullgram@gmail.com>
(http://abr4xas.org)
https://angelcruz.dev
-->
<script type="text/javascript">
    jQuery('#instapago_cchname').keypress(function() {
        if (jQuery(this).val().length < 1) {
            jQuery(this).css('border-color', '#F05A1A');
            jQuery(this).attr('title', 'Debe ingresar el nombre y apellido del tarjetahabiente');
        } else {
            jQuery(this).css('border-color', '#2abb67');
        }
    });
    jQuery('#instapago_cchnameid').keypress(function() {
        if (jQuery(this).val().length < 5 || jQuery(this).val().length > 8) {
            jQuery(this).css('border-color', '#F05A1A');
            jQuery(this).attr('title', 'Su cédula debe ser mayor que 6 y menor que 8 digitos');
        } else {
            jQuery(this).css('border-color', '#2abb67');
        }
    });
    jQuery('#instapago_ccnum').keypress(function() {
        if (jQuery(this).val().length >= 17) {
            jQuery(this).css('border-color', '#F05A1A');
            jQuery(this).attr('title', 'Debe ingresar los números de su tarjeta');
        } else {
            jQuery(this).css('border-color', '#2abb67');
        }
    });
    jQuery('#instapago_cvv').keypress(function() {
        if (jQuery(this).val().length < 1) {
            jQuery(this).css('border-color', '#F05A1A');
            jQuery(this).attr('title', 'Debe ingresar el código de validación de su tarjeta');
        } else {
            jQuery(this).css('border-color', '#2abb67');
        }
    });
</script>

<style type="text/css">
    #instapago_cchname,
    #Field106,
    #instapago_cchnameid,
    #instapago_ccnum,
    #instapago_cvv,
    #tdc-logos,
    #exp_month,
    #exp_year {
        display: inline-block;
    }

    #instapago_cchname {
        width: 40%;
    }

    #Field106 {
        width: 10%;
    }

    #instapago_cchnameid {
        width: 30%;
    }

    #instapago_ccnum {
        width: 25%;
    }

    #instapago_cvv {
        width: 15%;
    }

    #exp_month {
        width: 10%;
    }

    #exp_year {
        width: 10%;
    }

    @media screen and (max-width: 609px) {

        #instapago_cchname,
        #Field106,
        #instapago_cchnameid,
        #instapago_ccnum,
        #instapago_cvv,
        #exp_month,
        #exp_year {
            width: 100%;
        }
    }
</style>
