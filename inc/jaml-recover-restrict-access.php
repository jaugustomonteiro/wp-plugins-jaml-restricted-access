<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * PHP REGISTER
 */
add_shortcode('JAML_FORM_RECOVER', 'jaml_form_recover_function');
function jaml_form_recover_function($attr) {

    $args = shortcode_atts( array(     
		'image_logo' => 'http://17.0.0.2:8081/wp-content/uploads/2020/06/Objeto-Inteligente-de-Vetor-copiar-4.png',
	), $attr );

    $html = '';

    $html .= '<div id="container-form-email" class="container-fluid h-100 w-100 d-flex flex-column justify-content-center align-items-center">';
    $html .= '<form id="JamlFormRecover" class="jaml-restricted-form" style="width: min(400px, 100%)">';
    $html .= '<div class="jaml-form-recover-header d-flex flex-column align-items-center">';
    $html .= '<img class="img-fluid mb-3" src="' . $args['image_logo'] . '" alt="Logo" style="width: 120px" />';
    $html .= '<h2 class="mb-4 text-center">Recuperar senha</h2>';
    $html .= '</div>';
    $html .= '<p class="text-center font-weight-bold" style="color: #dddddd">Digite seu endereço de E-mail e CPF para recuperar sua senha</p>';
    $html .= '<div class="form-row">';
    $html .= '<div class="form-group col-md-12">';
    $html .= '<input type="text" name="nome" class="form-control jaml-form-control jaml-form-valid jaml-form-email" placeholder="Seu E-mail" style="height: 50px" />';
    $html .= '<small class="form-text text-danger d-none">E-mail obrigatório e/ou inválido</small>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '<div class="form-row">';
    $html .= '<div class="form-group col-md-12">';
    $html .= '<input type="text" name="nome" class="form-control jaml-form-control jaml-form-valid jaml-form-cpf" placeholder="Seu CPF" style="height: 50px" />';
    $html .= '<small class="form-text text-danger d-none">CPF obrigatório e/ou inválido</small>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '<button type="submit" class="btn btn-block mb-1" style="height: 50px; background: #392466; color: #fff; font-weight: bold">Recuperar Senha</button>';

    $html .= '<p class="font-italic jaml-restricted-message mt-1" style="height: 60px; font-size: 0.8rem"></p>';

    $html .= '<div class="d-flex justify-content-center jaml-support jaml-support-recover">';
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
 * SCRIPTS RECOVER
 */
add_action( 'wp_footer', 'jaml_form_recover_script');
function jaml_form_recover_script() { ?>
<script type="text/javascript">
(function ($) {
    
    var jamlFormRecover = $("#JamlFormRecover");
    
    jamlFormRecover.find('button').on("click", function (e) {
        e.preventDefault();
        if (validateFormRestrictedAccess(jamlFormRecover)) {

            var jamlInput = jamlFormRecover.find('.jaml-form-control');
            var jamlMessage = jamlFormRecover.find('.jaml-restricted-message');

            $.ajax("<?php echo admin_url('admin-ajax.php'); ?>", {
				type: 'POST',
				data: {
					action: 'jaml_form_recover_php',					
					email: jamlInput.eq(0).val(),					
					cpf: jamlInput.eq(1).val(),					           
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
						jamlFormRecover.find(".jaml-form-check").prop("checked", false);
                        jamlMessage.html(
                            '<span class="d-flex align-items-center" style="color: #145322"><i class="bx bx-check-circle mr-2" style="font-size:20px"></i>' + data.message + '</span>'
                        );						
					}		                           
				},
				beforeSend: function(data) {
					jamlInput.prop( "disabled", true );
        			jamlFormRecover.find('button').prop( "disabled", true );
                    jamlMessage.html(
                        '<span class="d-flex align-items-center text-warning jaml-blink"><i class="bx bx-transfer mr-2" style="font-size:20px"></i>Aguarde... Estamos enviado suas informações</span>'
                    );
				},
				complete: function(data) {                             
					jamlInput.prop( "disabled", false );
        			jamlFormRecover.find('button').prop( "disabled", false );
					
				}
			});
        }
    });

    var jamlSupportRecover = jamlFormRecover.find('.jaml-support-recover span');
    jamlSupportRecover.eq(0).on('click', function() {        
        window.location.href = JAMLRESTRICTHOMEURL; 
    })

})(jQuery);
</script>    
	
<?php }

add_action( 'wp_ajax_nopriv_jaml_form_recover_php', 'jaml_form_recover_php' );
add_action( 'wp_ajax_jaml_form_recover_php', 'jaml_form_recover_php' );

function jaml_form_recover_php() { 
    
	sleep(1);
   
	global $wpdb;
    $table = $wpdb->prefix . "jaml_usuarios";

	$email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);	
	$cpf = filter_var($_POST['cpf'], FILTER_SANITIZE_STRING);		
	$pass = substr(md5(uniqid(mt_rand(), true)), 0, 8);
	  
     
	try {
		$findEmailCPF = $wpdb->get_results("SELECT * FROM $table WHERE email = '$email' AND cpf = '$cpf'");
        
		if(count($findEmailCPF) === 0) {
			echo '{"type":"error", "message":"E-mail e/ou CPF não conferem. Tente novamente ou contato o administrador"}';
			exit;		
		}

        $nome = $findEmailCPF[0]->nome;
        $email = $findEmailCPF[0]->email;

        $messageEmail = "Ola $nome, Seja bem-vindo(a), seu usuário é <strong>$email</strong>, sua senha é <strong>$pass</strong>";
		
        
		if(!wp_mail($email, 'Cadastro - Mulheres progressistas', $messageEmail, array('Content-Type: text/html; charset=UTF-8'))) {
			echo '{"type":"error", "message":"Houve um erro no cadastro (POP/MAIL). Contate o administrador"}';
			exit;
		}

        $wpdb->query("UPDATE `wp_jaml_usuarios` SET pass = '$pass' WHERE email = '$email' AND cpf = '$cpf'");        
        
        echo '{"type":"success", "message":"Recuperação realizado com sucesso. Em breve você receberá login e senha no seu e-mail. Fique atento na sua caixa de entrada e/ou verifique seus spans. Se não chegar tente novamente ou contate o administrador."}';
	} catch (Exception $e) {
		echo '{"type":"error", "message":"Erro ao recuperar senha, contate o administrador"}';        
	}
    
    
	wp_die();
}