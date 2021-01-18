<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * PHP REGISTER
 */
add_shortcode('JAML_FORM_REGISTER', 'jaml_form_register_function');
function jaml_form_register_function($attr) {

    $args = shortcode_atts( array(     
		'image_logo' => 'http://17.0.0.2:8081/wp-content/uploads/2020/06/Objeto-Inteligente-de-Vetor-copiar-4.png',
	), $attr );

    $html = '';

    $html .= '<div id="container-form-email" class="container-fluid h-100 w-100 d-flex flex-column justify-content-center align-items-center">';
    $html .= '<form id="JamlFormRegister" class="jaml-restricted-form" style="width: min(800px, 100%)">';

    
    $html .= '<div class="jaml-form-recover-header d-flex flex-column align-items-center">';
    $html .= '<img class="img-fluid mb-3" src="' . $args['image_logo'] . '" alt="Logo" style="width: 120px" />';
    $html .= '<h3 class="mb-4 text-center">Criar Conta</h3>';
    $html .= '</div>';
    

    $html .= '<div class="form-row">';
    $html .= '<div class="form-group col-md-12">';
    $html .= '<input type="text" name="nome" class="form-control jaml-form-control jaml-form-valid jaml-form-text" placeholder="Seu Nome" style="height: 50px" />';
    $html .= '<small class="form-text text-danger d-none">Nome obrigatório</small>';
    $html .= '</div>';
    $html .= '</div>';

    $html .= '<div class="form-row">';
    $html .= '<div class="form-group col-md-12">';
    $html .= '<input type="text" name="nome" class="form-control jaml-form-control jaml-form-valid jaml-form-email" placeholder="Seu Email" style="height: 50px" />';
    $html .= '<small class="form-text text-danger d-none">Email obrigatório e/ou inválido</small>';
    $html .= '</div>';
    $html .= '</div>';

    $html .= '<div class="form-row">';

    $html .= '<div class="form-group col-md-6">';
    $html .= '<input type="text" name="nome" class="form-control jaml-form-control jaml-form-valid jaml-form-phone" placeholder="Seu Telefone" style="height: 50px" />';
    $html .= '<small class="form-text text-danger d-none">Whatsapp obrigatório</small>';
    $html .= '</div>';

    $html .= '<div class="form-group col-md-6">';
    $html .= '<input type="text" name="nome" class="form-control jaml-form-control jaml-form-valid jaml-form-cpf" placeholder="Seu CPF" style="height: 50px" />';
    $html .= '<small class="form-text text-danger d-none">CPF obrigatório e/ou inválido</small>';
    $html .= '</div>';

    $html .= '</div>';

    $html .= '<div class="form-row">';
    $html .= '<div class="form-group col-md-6">';
    $html .= '<select id="jamlSelectEstados" class="form-control jaml-form-control jaml-form-valid jaml-form-text jaml-form-state" style="height: 50px"></select>';
    $html .= '<small class="form-text text-danger d-none">Estado obrigatório</small>';
    $html .= '</div>';

    $html .= '<div class="form-group col-md-6">';
    $html .= '<select id="jamlSelectCidades" class="form-control jaml-form-control jaml-form-valid jaml-form-text jaml-form-city" style="height: 50px"></select>';
    $html .= '<small class="form-text text-danger d-none">Cidade obrigatória</small>';
    $html .= '</div>';
    $html .= '</div>';

    /*
    $html .= '<div class="form-row">';
    $html .= '<div class="form-check ml-2 mb-4">';
    $html .= '<input id="ckeckTerms" type="checkbox" class="form-check-input jaml-form-control jaml-form-valid jaml-form-check" style="margin-top: 6px; border: 2px solid transparent" />';
    $html .= '<label class="form-check-label text-bold font-italic font-weight-bold" for="ckeckTerms">Li e estou de acordo com os termos e condições</label>';
    $html .= '<small class="form-text text-danger d-none">Você precisar aceitar os termos e condições</small>';
    $html .= '</div>';
    $html .= '</div>';
    */

    $html .= '<button type="submit" class="btn btn-block mb-1 mt-4" style="height: 50px; background: #392466; color: #fff; font-weight: bold">Criar Conta</button>';

    $html .= '<p class="font-italic jaml-restricted-message mt-2" style="height: 60px; font-size: 0.8rem"></p>';

    $html .= '<div class="d-flex justify-content-center jaml-support jaml-support-register">';
    $html .= '<span class="d-flex align-items-center">';
    $html .= '<i class="bx bx-arrow-back mr-2" style="font-size: 20px"></i>';
    $html .= '<a href="#">Voltar para Home</a>';
    $html .= '</span>';
    $html .= '</div>';
    $html .= '</form>';
    $html .= '</div>';

    return $html;
}

/**
 * SCRIPTS REGISTER
 */
add_action( 'wp_footer', 'jaml_form_register_script');
function jaml_form_register_script() { ?>
<script type="text/javascript">
(function ($) {
    
    var JamlFormRegister = $("#JamlFormRegister");

    JamlFormRegister.find("button").on("click", function (e) {
        e.preventDefault();
        if (validateFormRestrictedAccess(JamlFormRegister)) {

            var jamlInput = JamlFormRegister.find('.jaml-form-control');
            var jamlMessage = JamlFormRegister.find('.jaml-restricted-message');

            $.ajax("<?php echo admin_url('admin-ajax.php'); ?>", {
				type: 'POST',
				data: {
					action: 'jaml_form_register',					 
					nome: jamlInput.eq(0).val(),
					email: jamlInput.eq(1).val(),
					telefone: jamlInput.eq(2).val(),
					cpf: jamlInput.eq(3).val(),
					estado: jamlInput.eq(4).val(),
					cidade: jamlInput.eq(5).val(),             
				},
				dataType: 'json',
				success: function(data) {  
					if(data.type === 'error') {
						jamlMessage.html(
                            '<span class="d-flex align-items-center text-danger"><i class="bx bx-info-circle mr-2" style="font-size:20px"></i>' + data.message + '</span>'
                        );
					}
					if(data.type === 'success') {						
						jamlInput.val("");
						JamlFormRegister.find(".jaml-form-check").prop("checked", false);
                        jamlMessage.html(
                            '<span class="d-flex align-items-center" style="color: #145322"><i class="bx bx-check-circle mr-2" style="font-size:20px"></i>' + data.message + '</span>'
                        );						
					}					                           
				},
				beforeSend: function(data) {
					jamlInput.prop( "disabled", true );
        			JamlFormRegister.find('button').prop( "disabled", true );
                    jamlMessage.html(
                        '<span class="d-flex align-items-center text-warning jaml-blink"><i class="bx bx-transfer mr-2" style="font-size:20px"></i>Aguarde... Estamos enviado suas informações</span>'
                    );
				},
				complete: function(data) {                             
					jamlInput.prop( "disabled", false );
        			JamlFormRegister.find('button').prop( "disabled", false );
					
				}
			});
        }
    });


    var jamlFormRegiterLink = JamlFormRegister.find('.jaml-support-register span');
    jamlFormRegiterLink.eq(0).on('click', function() {        
        window.location.href = JAMLRESTRICTHOMEURL; 
    })

})(jQuery);
</script>    
	
<?php }

add_action( 'wp_ajax_nopriv_jaml_form_register', 'jaml_form_register' );
add_action( 'wp_ajax_jaml_form_register', 'jaml_form_register' );
function jaml_form_register() { 
    
	sleep(1);

	global $wpdb;
    $table = $wpdb->prefix . "jaml_usuarios";

	$perfil = 0;
	$ativo = 1;
	$nome = filter_var($_POST['nome'], FILTER_SANITIZE_STRING);	
	$email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
	$telefone = filter_var($_POST['telefone'], FILTER_SANITIZE_STRING);
	$cpf = filter_var($_POST['cpf'], FILTER_SANITIZE_STRING);	
	$estado = filter_var($_POST['estado'], FILTER_SANITIZE_STRING);
	$cidade = filter_var($_POST['cidade'], FILTER_SANITIZE_STRING);
	$pass = substr(md5(uniqid(mt_rand(), true)), 0, 8);
	$criado_em = date('Y-m-d H:i:s');
	$alterado_em = date('Y-m-d H:i:s');    

	try {
		$findEmail = count($wpdb->get_results("SELECT * FROM $table WHERE email = '$email'"));
		if($findEmail > 0) {
			echo '{"type":"error", "message":"E-mail já está em uso"}';
			exit;		
		}

		$findCPF = count($wpdb->get_results("SELECT * FROM $table WHERE cpf = '$cpf'"));
		if($findCPF > 0) {
			echo '{"type":"error", "message":"CPF já está em uso"}';
			exit;		
		}	      
        
		$messageEmail = "Ola $nome, Seja bem-vindo(a), seu usuário é <strong>$email</strong>, sua senha é <strong>$pass</strong>";

		
		if(!wp_mail($email, 'Cadastro - Mulheres progressistas', $messageEmail, array('Content-Type: text/html; charset=UTF-8'))) {
			echo '{"type":"error", "message":"Houve um erro no cadastro (POP/MAIL). Contate o administrador"}';
			exit;
		}

		$wpdb->query("INSERT INTO $table(`id`, `perfil`, `ativo`, `nome`, `email`, `telefone`, `cpf`, `estado`, `cidade`, `pass`, `criado_em`, `alterado_em`) VALUES (NULL, $perfil, $ativo, '$nome', '$email', '$telefone', '$cpf', '$estado', '$cidade', '$pass', '$criado_em', '$alterado_em')");

		echo '{"type":"success", "message":"Cadastro realizado com sucesso. Em breve você receberá login e senha no seu e-mail. Fique atento na sua caixa de entrada e/ou verifique seus spans. Se não chegar realize novo cadastro ou contate o administrador."}';
        
		 
	} catch (Exception $e) {
		echo '{"type":"error", "message":"Erro ao realizar registro, contate o administrador"}';        
	}
	wp_die();
}
