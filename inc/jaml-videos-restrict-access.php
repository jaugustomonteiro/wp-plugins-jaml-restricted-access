<?php


/**
 * CUSTOM POST VIDEOS
 */
add_action( 'init', 'jaml_custom_post_video' );
function jaml_custom_post_video() {
 
  $labels = array(
    'name'               => __( 'Vídeos' ),
    'singular_name'      => __( 'Vídeo' ),
    'add_new'            => __( 'Adicionar novo Vídeo' ),
    'add_new_item'       => __( 'Adicionar novo Vídeo' ),
    'edit_item'          => __( 'Editar Vídeo' ),
    'new_item'           => __( 'Novo Vídeo' ),
    'all_items'          => __( 'Todos Vídeos' ),
    'view_item'          => __( 'Visualizar Vídeos' ),
    'search_items'       => __( 'Localizar Vídeos' )
  );
 
  $args = array(
    'labels'            => $labels,
    'description'       => 'Holds our Vídeos',
    'public'            => true,
    'menu_position'     => 6,
    'supports'          => array( 'title', 'thumbnail', 'custom-fields' ),
    'has_archive'       => true,
    'show_in_admin_bar' => true,
    'show_in_nav_menus' => true,
    'has_archive'       => true,
    'menu_icon'         => 'dashicons-video-alt3',
  );
 
  register_post_type( 'video', $args);
} 

/**
 *  STYLE VIDEOS
 */
add_action( 'wp_head', 'jaml_form_videos_styles');
function jaml_form_videos_styles() { ?>
<style type="text/css">
  .jaml-play-video:hover {
    cursor: pointer;
    color: #fff !important;
    opacity: 1 !important;
  }   
</style>
<?php }

/**
 * PHP REGISTER
 */
add_shortcode('JAML_VIDEOS_RESTRICT', 'jaml_videos_restrict_function');
function jaml_videos_restrict_function($attr) {

    $args = shortcode_atts( array(     
		'image_logo' => 'http://17.0.0.2:8081/wp-content/uploads/2020/06/Objeto-Inteligente-de-Vetor-copiar-4.png',
        'form_url_login' => 'videos',
        'form_url_register' => 'register',
        'form_url_recover' => 'recover',
	), $attr );

    $form_logo = $args['image_logo'];
    $form_url_login = site_url() . '/' . $args['form_url_login'];
    $form_url_register = site_url() . '/' . $args['form_url_register'];
    $form_url_recover = site_url() . '/' . $args['form_url_recover'];

    if(!isset($_SESSION['jaml_session_authorization'])) { 
        return jaml_form_login($form_logo, $form_url_login, $form_url_register, $form_url_recover);
        exit;
    }

    $html = '';

    $html .= '<div class="container p-3">';
    
    $html .= '<div class="input-group mb-3">';
    $html .= '<input type="text" class="form-control jaml-input-video-search" placeholder="Palestrante ou Tema" />';
    $html .= '<div class="input-group-append">';
    $html .= '<span class="input-group-text" style="background: #392466"><i class="bx bx-search text-white"></i></span>';
    $html .= '</div>';
    $html .= '</div>';

    $html .= '<div class="row row-cols-1 row-cols-md-3 jaml-video-list">';
    
    $videos_args = array(
      'post_type'   => 'video',
      'posts_per_page' => '-1'
    );

    $videos = new WP_Query( $videos_args ); 

    if($videos->have_posts()) {		
      while($videos->have_posts()) {  
        $videos->the_post();     

        $thumbnail_youtube = wp_get_attachment_image_src(get_post_meta(get_the_ID(), 'thumbnail_youtube', true), 'full')[0];
        $palestrate = get_post_meta(get_the_ID(), 'palestrantes', true);
        $titulo_da_palestra = get_post_meta(get_the_ID(), 'titulo_da_palestra', true);
        $indetificador_youtube = get_post_meta(get_the_ID(), 'indetificador_youtube', true);

        $html .= '<div class="col mb-4">';
        $html .= '<div class="card h-100 position-relative jaml-container-videos">';
        $html .= '<div style="width: 100%; height: 200px; background: url(' . $thumbnail_youtube . ') no-repeat center center; background-size: cover"></div>';
        $html .= '<div style="position: absolute; width: 100%; height: 100%; background: transparent; display: flex; align-items: center; justify-content: center">';
        $html .= '<i class="bx bx-play-circle jaml-play-video" style="font-size: 80px; color: #ccc; opacity: 0.5; transition: all 0.5s"></i>';
        $html .= '</div>';
        $html .= '<input type="hidden" class="jaml-container-video-url" value="' . $indetificador_youtube . '" />';
        $html .= '<strong class="d-none">' . $palestrate . '</strong>';
        $html .= '<p class="d-none">' . $titulo_da_palestra .  '</p>';
        $html .= '</div>';
        $html .= '</div>';
      }
    }

    $html .= '</div>';


    $html .= '<div class="modal fade" id="JAMLModalVideos" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">';
    $html .= '<div class="modal-dialog modal-lg modal-dialog-centered">';
    $html .= '<div class="modal-content">';
    $html .= '<div class="modal-header">';
    $html .= '<h5 class="modal-title text-dark" id="staticBackdropLabel">Modal title</h5>';
    $html .= '<button type="button" class="close">';
    $html .= '<span aria-hidden="true">&times;</span>';
    $html .= '</button>';
    $html .= '</div>';
    $html .= '<div class="modal-body">';
    $html .= '<div class="embed-responsive embed-responsive-16by9">';
    $html .= '<iframe src="" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';

    return $html;
}

/**
 * JAVASCRIPT
 */

add_action( 'wp_footer', 'jaml_form_videos_script');

function jaml_form_videos_script() { ?>
<script type="text/javascript">
(function ($) {
  // MODAL VIDEOS
  var JAMLContainerVideos = $(".jaml-container-videos");

  var JAMLModalVideos = $("#JAMLModalVideos");

  var JAMLPlayVideo = JAMLContainerVideos.find(".jaml-play-video");

  JAMLPlayVideo.click(function () {
    var JAMLValueVideo = $(this).parents("div").siblings(".jaml-container-video-url").val();
    var JAMLTitleVideo = $(this).parents("div").siblings("strong").text();
    JAMLModalVideos.find(".modal-title").text(JAMLTitleVideo);
    JAMLModalVideos.find("iframe").attr("src", "https://www.youtube.com/embed/" + JAMLValueVideo + "?autoplay=0&modestbranding=1&showinfo=1");
    $("#JAMLModalVideos").modal("show");
    $(".ytp-chrome-top-buttons").hide();
  });

  JAMLModalVideos.find("button").click(function () {
    JAMLModalVideos.find("iframe").attr("src", "");
    $("#JAMLModalVideos").modal("hide");
  });

  $(".jaml-input-video-search").keyup(function () {
    var filter = $(this).val();
    $(".jaml-video-list > div").each(function () {
      if ($(this).text().search(new RegExp(filter, "i")) < 0) {
        $(this).hide();
      } else {
        $(this).show();
      }
    });
  });

})(jQuery);
</script>	
<?php }