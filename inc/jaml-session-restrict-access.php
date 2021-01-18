<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * INCIAR A SESSÃO
 */
add_action('init', 'jaml_session_restricted_access', 1);
function jaml_session_restricted_access(){
    if( ! session_id() ) {        
        session_start();
    }
}

/**
 * HTML RESTRICTED MENU
 */
add_shortcode('jaml_restricted_menu', 'jaml_restricted_menu_function');
function jaml_restricted_menu_function() {

    $html = '';

    if(!isset($_SESSION['jaml_session_authorization'])) { 
        return $html;
        exit;
    }

    $html .= '<style>.jaml-restricted-menu{color:#a3a3a3;font-size:.8rem}.jaml-restricted-menu span:hover{color:#fff}</style>';

    $html .= '<div class="d-flex justify-content-end">';
    $html .= '<nav class="navbar jaml-restricted-menu">';
    $html .= '<span class="navbar-text nav-link d-flex align-items-center" style="cursor: pointer">';
    $html .= '<i class="bx bx-user" style="font-size: 1.2rem; margin-right: 0.2rem"></i>';
    $html .= '<strong>' . $_SESSION['jaml_session_authorization']['nome'] . '</strong>';
    $html .= '</span>';
    $html .= '<span id="JAMLExitSession" class="navbar-text nav-link d-flex align-items-center" style="cursor: pointer">';
    $html .= '<i class="bx bx-exit" style="font-size: 1.2rem; margin-right: 0.2rem"></i>';
    $html .= '<strong>Sair</strong>';
    $html .= '</span>';
    $html .= '</nav>';
    $html .= '</div>';

    return $html;
}

/**
 * SCRIPT RESTRICT LOGOUT
 */
add_action( 'wp_footer', 'jaml_session_exit_script');
function jaml_session_exit_script() { ?>
<script type="text/javascript">
(function($){  

    var JAMLHomeURL = <?php echo '"' . get_site_url() . '"'?>;
    var JAMLExitSession = $("#JAMLExitSession");

    JAMLExitSession.click(function(e) {
        e.preventDefault();
        
        $.ajax("<?php echo admin_url('admin-ajax.php'); ?>", {
            type: 'GET',
            data: {
                action: 'jaml_session_exit',                                                         
            },
            dataType: 'html',
            success: function(data) {                           
                window.location.href = JAMLHomeURL;                               
            },
            beforeSend: function(data) {
                
            },
            complete: function(data) {                             
                
            }
        });              
    }) 
})(jQuery);
</script>    
<?php }


/**
 * PHP RESTRICT LOGOUT
 */
add_action( 'wp_ajax_nopriv_jaml_session_exit', 'jaml_session_exit' );
add_action( 'wp_ajax_jaml_session_exit', 'jaml_session_exit' );
function jaml_session_exit() {
    session_destroy();
    wp_die();
}

/**
 * HTML FORM LOGIN
 */
function jaml_form_login($form_logo, $form_url_login, $form_url_register, $form_url_recover) {

    $html = '';

    $html .= '<div id="JamlContainerFormLogin" class="container-fluid d-flex flex-column justify-content-center align-items-center">';
    $html .= '<form id="JamlFormLogin" class="jaml-restricted-form" style="width: min(400px, 100%)">';
    $html .= '<div class="jaml-form-login-header d-flex flex-column align-items-center">';
    $html .= '<img class="img-fluid mb-3" src="' . $form_logo . '" alt="Logo" style="width: 120px" />';
    $html .= '<h2 class="mb-4 text-center">Faça seu Login</h2>';
    $html .= '</div>';
    $html .= '<div class="form-row">';
    $html .= '<div class="form-group col-md-12">';
    $html .= '<input type="text" name="nome" class="form-control jaml-form-control jaml-form-valid jaml-form-email" placeholder="Seu E-mail" style="height: 50px" />';
    $html .= '<small class="form-text text-danger d-none">E-mail obrigatório e/ou inválido</small>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '<div class="form-row">';
    $html .= '<div class="form-group col-md-12">';
    $html .= '<input type="password" name="nome" class="form-control jaml-form-control jaml-form-valid jaml-form-text" placeholder="Sua Senha" style="height: 50px" />';
    $html .= '<small class="form-text text-danger d-none">Senha obrigatória</small>';
    $html .= '</div>';
    $html .= '</div>';

    $html .= '<input type="hidden" id="UrlFormLogin" value="' . $form_url_login . '">';
    $html .= '<input type="hidden" id="UrlFormRegister" value="' . $form_url_register . '">';
    $html .= '<input type="hidden" id="UrlFormRecover" value="' . $form_url_recover . '">';

    $html .= '<button type="submit" class="btn btn-block mb-1" style="height: 50px; background: #392466; color: #fff; font-weight: bold">Entrar</button>';

    $html .= '<p class="font-italic jaml-restricted-message mt-1" style="height: 60px; font-size: 0.8rem"></p>';

    $html .= '<div class="d-flex justify-content-between jaml-support jaml-support-login">';
    $html .= '<span class="d-flex align-items-center">';
    $html .= '<i class="bx bx-exit mr-1" style="font-size: 20px"></i>';
    $html .= '<a href="#">Criar Conta</a>';
    $html .= '</span>';
    $html .= '<span class="d-flex align-items-center">';
    $html .= '<i class="bx bxs-lock-alt mr-1" style="font-size: 20px"></i>';
    $html .= '<a href="#">Esqueci minha Senha</a>';
    $html .= '</span>';
    $html .= '</div>';
    $html .= '</form>';
    $html .= '</div>';

    return $html;
}

/**
 * SCRIPTS FORM LOGIN
 */
add_action( 'wp_footer', 'jaml_form_login_script');
function jaml_form_login_script() { ?>
<script type="text/javascript">
(function ($) {

    var jamlFormLogin = $('#JamlFormLogin');
    var urlFormLogin = $('#UrlFormLogin').val();
    var urlFormRegister = $('#UrlFormRegister').val();
    var urlFormRecover = $('#UrlFormRecover').val();
    
    jamlFormLogin.find("button").on("click", function (e) {
        e.preventDefault();

        var jamlInput = jamlFormLogin.find('.jaml-form-control');
        var jamlMessage = jamlFormLogin.find('.jaml-restricted-message');        

        if (validateFormRestrictedAccess(jamlFormLogin)) {
            $.ajax("<?php echo admin_url('admin-ajax.php'); ?>", {
                type: 'POST',
                data: {
                    action: 'jaml_form_login_access',
                    email: jamlInput.eq(0).val(),
                    pass: jamlInput.eq(1).val(),
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
                        jamlMessage.html(
                            '<span class="d-flex align-items-center" style="color: #145322"><i class="bx bx-check-circle mr-2" style="font-size:20px"></i>' + data.message + '</span>'
                        );	
                        setTimeout(function() {
                            window.location.href = urlFormLogin;        
                        }, 1000)					
					}
                },
				beforeSend: function(data) {
					jamlInput.prop( "disabled", true );
        			jamlFormLogin.find('button').prop( "disabled", true );
                    jamlMessage.html(
                        '<span class="d-flex align-items-center text-warning jaml-blink"><i class="bx bx-transfer mr-2" style="font-size:20px"></i>Aguarde...   <strong class="ml-2">Estamos validando suas informações</strong></span>'
                    );
				},
				complete: function(data) {                             
					jamlInput.prop( "disabled", false );
        			jamlFormLogin.find('button').prop( "disabled", false );					
				}
            });
        }
    });


    // SUPORT LOGIN
    var jamlSupportLogin = jamlFormLogin.find('.jaml-support-login span');
    jamlSupportLogin.eq(0).on('click', function() {
        setTimeout(function() {
            window.location.href = urlFormRegister;        
        }, 1000)
    })

    jamlSupportLogin.eq(1).on('click', function() {
        setTimeout(function() {
            window.location.href = urlFormRecover;        
        }, 1000)
    })

})(jQuery);
</script>	
<?php }

/**
 * PHP FORM LOGIN
 */
add_action( 'wp_ajax_nopriv_jaml_form_login_access', 'jaml_form_login_access' );
add_action( 'wp_ajax_jaml_form_login_access', 'jaml_form_login_access' );
function jaml_form_login_access() { 
    
	sleep(2);

    global $wpdb;
    $table = $wpdb->prefix . "jaml_usuarios";

    $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);	
    $pass = filter_var($_POST['pass'], FILTER_SANITIZE_STRING);

       
    $isActiveUser = count($wpdb->get_results("SELECT * FROM $table WHERE email = '$email' AND ativo = 1"));     
    if($isActiveUser === 0) {
        echo '{"type":"error", "message":"Sua contra encontra-se temporariamente indisponível. Contate o adiministrador"}'; 
        exit;
    }
    
    $validateUser = $wpdb->get_results("SELECT * FROM $table WHERE email = '$email' AND pass = '$pass'");

    $isValidateUser = count($validateUser);
    
    if($isValidateUser === 0) {        
        echo '{"type":"error", "message":"Usuário e/ou senha inválidos. Tente novamente ou contate o administrador"}'; 
        exit;
    }
    
    $_SESSION['jaml_session_authorization'] = array();
    $_SESSION['jaml_session_authorization'] = array(
        'perfil' => $validateUser[0]->perfil,
        'nome' => $validateUser[0]->nome,      
        'email' => $validateUser[0]->email,
        'cpf' => $validateUser[0]->cpf,      
    );

    echo '{"type":"success", "message":"Acesso autorizado. Aguarde..., A página esta sendo carredada"}';

	wp_die();
}

