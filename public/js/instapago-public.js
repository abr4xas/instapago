(function ($) {
    "use strict";

    /**
     * All of the code for your public-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
     *
     * });
     *
     * When the window is loaded:
     *
     * $( window ).load(function() {
     *
     * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.
     */
})(jQuery);

document.addEventListener("DOMContentLoaded", function () {
    var cchname = document.getElementById("instapago_cchname");
    var cchnameid = document.getElementById("instapago_cchnameid");
    var ccnum = document.getElementById("instapago_ccnum");
    var cvv = document.getElementById("instapago_cvv");

    function setBorderStyle(element, borderColor) {
        element.style.borderColor = borderColor;
        element.style.border = "2px solid " + borderColor;
    }

    if (cchname) {
        cchname.addEventListener("keypress", function () {
            if (cchname.value.length < 1) {
                setBorderStyle(cchname, "#F05A1A");
                cchname.setAttribute(
                    "title",
                    "Debe ingresar el nombre y apellido del tarjetahabiente"
                );
            } else {
                setBorderStyle(cchname, "#2abb67");
            }
        });
    }

    if (cchnameid) {
        cchnameid.addEventListener("keypress", function () {
            if (cchnameid.value.length < 5 || cchnameid.value.length > 8) {
                setBorderStyle(cchnameid, "#F05A1A");
                cchnameid.setAttribute(
                    "title",
                    "Su cédula debe ser mayor que 6 y menor que 8 dígitos"
                );
            } else {
                setBorderStyle(cchnameid, "#2abb67");
            }
        });
    }

    if (ccnum) {
        ccnum.addEventListener("keypress", function () {
            if (ccnum.value.length >= 17) {
                setBorderStyle(ccnum, "#F05A1A");
                ccnum.setAttribute(
                    "title",
                    "Debe ingresar los números de su tarjeta"
                );
            } else {
                setBorderStyle(ccnum, "#2abb67");
            }
        });
    }

    if (cvv) {
        cvv.addEventListener("keypress", function () {
            if (cvv.value.length < 1) {
                setBorderStyle(cvv, "#F05A1A");
                cvv.setAttribute(
                    "title",
                    "Debe ingresar el código de validación de su tarjeta"
                );
            } else {
                setBorderStyle(cvv, "#2abb67");
            }
        });
    }
});
