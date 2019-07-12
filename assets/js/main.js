jQuery('#instapago_cchname').keypress(function () {
  if (jQuery(this).val().length < 1) {
    jQuery(this).css('border-color', '#F05A1A');
    jQuery(this).attr('title', 'Debe ingresar el nombre y apellido del tarjetahabiente');
  }
  else {
    jQuery(this).css('border-color', '#2abb67');
  }
});
jQuery('#instapago_cchnameid').keypress(function () {
  if (jQuery(this).val().length < 5 || jQuery(this).val().length > 8) {
    jQuery(this).css('border-color', '#F05A1A');
    jQuery(this).attr('title', 'Su cédula debe ser mayor que 6 y menor que 8 digitos');
  }
  else {
    jQuery(this).css('border-color', '#2abb67');
  }
});
jQuery('#instapago_ccnum').keypress(function () {
  if (jQuery(this).val().length >= 17) {
    jQuery(this).css('border-color', '#F05A1A');
    jQuery(this).attr('title', 'Debe ingresar los números de su tarjeta');
  }
  else {
    jQuery(this).css('border-color', '#2abb67');
  }
});
jQuery('#instapago_cvv').keypress(function () {
  if (jQuery(this).val().length < 1) {
    jQuery(this).css('border-color', '#F05A1A');
    jQuery(this).attr('title', 'Debe ingresar el código de validación de su tarjeta');
  }
  else {
    jQuery(this).css('border-color', '#2abb67');
  }
});
