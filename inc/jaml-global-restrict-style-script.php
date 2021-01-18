<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

add_action( 'wp_head', 'jaml_global_restrict_styles');
function jaml_global_restrict_styles() { ?>
<style type="text/css">
    .jaml-restricted-form h3 {
        color: #ffffff;
    }

    .jaml-restricted-form input {
        border-radius: 8px;
    }

    .jaml-restricted-form button {
        opacity: 0.7;
        transition: all 0.5s;
        border-radius: 8px;
    }

    .jaml-restricted-form button:hover {
        opacity: 1;
    }

    .jaml-restricted-form .jaml-support span,
    .jaml-restricted-form .jaml-support a {
        text-decoration: none;
        color: #dddddd;
        font-weight: bold;
        font-size: 12px;
    }

    .jaml-restricted-form .jaml-support span:hover,
    .jaml-restricted-form .jaml-support a:hover {
        text-decoration: none;
        color: #fff;
        font-weight: bold;
     }

    @keyframes jaml-blink {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 0.2; }
    }

    .jaml-blink {
      -webkit-animation: jaml-blink .75s linear infinite;
      -moz-animation: jaml-blink .75s linear infinite;
      -ms-animation: jaml-blink .75s linear infinite;
      -o-animation: jaml-blink .75s linear infinite;
      animation: jaml-blink .75s linear infinite;
    }

    #JamlContainerFormLogin {
      width: 100vw;
      height: 75vh; 
    }
</style>
<?php }



add_action( 'wp_footer', 'jaml_global_restrict_script');

function jaml_global_restrict_script() { ?>
<script type="text/javascript">
(function ($) {
  $(".jaml-form-phone").mask("00 000000000");
  $(".jaml-form-cpf").mask("00000000000");

  var jamlFormState = $(".jaml-form-state");
  jamlFormState.append('<option value="">Estado</option>');

  var jamlFormCity = $(".jaml-form-city");
  jamlFormCity.append('<option value="">Cidades</option>');

  for (var i in jaml_estados) {
    jamlFormState.append("<option id=" + jaml_estados[i].id + ">" + jaml_estados[i].nome + "</option>");
  }

  jamlFormState.change(function () {
    jamlFormCity.empty();
    jamlFormCity.append('<option value="">Cidades</option>');

    var estadoId = $(this).find("option:selected").attr("id");
    var jamlCidades = jaml_cidades.filter((cidade) => cidade.estado_id === estadoId);

    for (var i in jamlCidades) {
      jamlFormCity.append("<option id=" + jamlCidades[i].id + ">" + jamlCidades[i].nome + "</option>");
    }
  });
})(jQuery);


var JAMLRESTRICTHOMEURL = <?php echo '"' . get_site_url() . '"'?>;

function addStateCity() {
  for (var i in jaml_estados) {
    jamlSelectEstados.append("<option id=" + jaml_estados[i].id + ">" + jaml_estados[i].nome + "</option>");
  }
}

function checkEmailRestrictedAccess(input) {
  if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(input)) {
    return true;
  }
  return false;
}

function isValidCPFRestrictedAccess(cpf) {
  if (typeof cpf !== "string") return false;
  cpf = cpf.replace(/[\s.-]*/gim, "");
  if (
    !cpf ||
    cpf.length != 11 ||
    cpf == "00000000000" ||
    cpf == "11111111111" ||
    cpf == "22222222222" ||
    cpf == "33333333333" ||
    cpf == "44444444444" ||
    cpf == "55555555555" ||
    cpf == "66666666666" ||
    cpf == "77777777777" ||
    cpf == "88888888888" ||
    cpf == "99999999999"
  ) {
    return false;
  }
  var soma = 0;
  var resto;
  for (var i = 1; i <= 9; i++) soma = soma + parseInt(cpf.substring(i - 1, i)) * (11 - i);
  resto = (soma * 10) % 11;
  if (resto == 10 || resto == 11) resto = 0;
  if (resto != parseInt(cpf.substring(9, 10))) return false;
  soma = 0;
  for (var i = 1; i <= 10; i++) soma = soma + parseInt(cpf.substring(i - 1, i)) * (12 - i);
  resto = (soma * 10) % 11;
  if (resto == 10 || resto == 11) resto = 0;
  if (resto != parseInt(cpf.substring(10, 11))) return false;
  return true;
}

function validateFormRestrictedAccess(form) {
  var settings = {
    borderDefault: "#ccc",
    backgroundDefault: "#FFF",
    colorError: "#dc3545",
    backgroundError: "#fbeaec",
  };

  var inputError = {
    border: "1px solid " + settings.colorError,
    background: settings.backgroundError,
  };

  var InputDefault = {
    border: "1px solid " + settings.borderDefault,
    background: settings.backgroundDefault,
  };

  var jamlCkeckError = {
    outline: "2px solid " + settings.colorError,
  };

  var jamlCheckDefault = {
    outline: "2px solid transparent",
    outlineOffset: "-2px",
  };

  var jamlFormInput = form.find(".jaml-form-control");
  var jamlFormMessage = form.find(".jaml-restricted-message");
  var jamlFormCheck = form.find(".jaml-form-check");

  jamlFormInput.css(InputDefault);
  jamlFormCheck.css(jamlCheckDefault);

  jamlFormMessage.html("&nbsp");
  for (var i = 0; i < jamlFormInput.length; i++) {
    if (jamlFormInput.eq(i).hasClass("jaml-form-text") && jamlFormInput.eq(i).hasClass("jaml-form-valid") && jamlFormInput.eq(i).val() === "") {
      jamlFormInput.eq(i).css(inputError);
      jamlFormMessage.html(
        '<span class="d-flex align-items-center text-danger"><i class="bx bx-info-circle mr-2" style="font-size:20px"></i>' + jamlFormInput.eq(i).siblings("small").text() + "</span>"
      );
      return false;
    }

    if (jamlFormInput.eq(i).hasClass("jaml-form-phone") && jamlFormInput.eq(i).hasClass("jaml-form-valid") && jamlFormInput.eq(i).val() === "") {
      jamlFormInput.eq(i).css(inputError);
      jamlFormMessage.html(
        '<span class="d-flex align-items-center text-danger"><i class="bx bx-info-circle mr-2" style="font-size:20px"></i>' + jamlFormInput.eq(i).siblings("small").text() + "</span>"
      );
      return false;
    }

    if (jamlFormInput.eq(i).hasClass("jaml-form-email") && jamlFormInput.eq(i).hasClass("jaml-form-valid")) {
      if (!checkEmailRestrictedAccess(jamlFormInput.eq(i).val())) {
        jamlFormInput.eq(i).css(inputError);
        jamlFormMessage.html(
          '<span class="d-flex align-items-center text-danger"><i class="bx bx-info-circle mr-2" style="font-size:20px"></i>' + jamlFormInput.eq(i).siblings("small").text() + "</span>"
        );
        return false;
      }
    }

    if (jamlFormInput.eq(i).hasClass("jaml-form-cpf") && jamlFormInput.eq(i).hasClass("jaml-form-valid")) {
      if (!isValidCPFRestrictedAccess(jamlFormInput.eq(i).val())) {
        jamlFormInput.eq(i).css(inputError);
        jamlFormMessage.html(
          '<span class="d-flex align-items-center text-danger"><i class="bx bx-info-circle mr-2" style="font-size:20px"></i>' + jamlFormInput.eq(i).siblings("small").text() + "</span>"
        );
        return false;
      }
    }

    if (jamlFormInput.eq(i).hasClass("jaml-form-check") && jamlFormInput.eq(i).hasClass("jaml-form-valid")) {
      if (!jamlFormInput.eq(i).is(":checked")) {
        jamlFormInput.eq(i).css(jamlCkeckError);
        jamlFormMessage.html(
          '<span class="d-flex align-items-center text-danger"><i class="bx bx-info-circle mr-2" style="font-size:20px"></i>' + jamlFormInput.eq(i).siblings("small").text() + "</span>"
        );
        return false;
      }
    }
  }
  return true;
}
</script>	
<?php }