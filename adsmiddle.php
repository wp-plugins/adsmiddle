<?php
   /*
      Plugin Name: AdsMiddle
      Plugin URI: http://wjharil.com/blog/adsense-en-medio-de-post-wordpress/
      Description: Simple plugin para agregar adsense al medio del post.
      Version: 1.0
      Author: WJHARIL
      Author URI: http://wjharil.com
      Licence: GPL2
   */
  
  add_action( 'admin_menu', 'wp_addmidle' );
  function wp_addmidle() {
    add_options_page( 'AdsMiddle', 'AdsMiddle', 'manage_options', 'wp_addmidle', 'wp_addmidle_options' );
  }
  function wp_addmidle_options() {

    if (!current_user_can('manage_options')){
        wp_die( __('Pequeño padawan... debes utilizar la fuerza sayayin para entrar aquí.') );
    }

    $opt_name = 'wp_middle_cantidad';
    $opt_name2 = 'wp_middle_adsense';
    $hidden_field_name = 'wp_middle_image_hidden';
    $hidden_field_name2 = 'wp_middle_adsense_hidden';
    $data_field_name = 'wp_middle_cantidad';
    $data_field_name2 = 'wp_middle_adsense';
    $opt_val = get_option( $opt_name );
    $opt_val2 = get_option( $opt_name2 );

    if( isset($_POST[ $hidden_field_name ]) && isset($_POST[ $hidden_field_name2 ]) && $_POST[ $hidden_field_name ] == 'ruta_hidden' && $_POST[ $hidden_field_name2 ] == 'ruta_hidden') {
        $opt_val = $_POST[ $data_field_name ];
        update_option( $opt_name, $opt_val );
        $opt_val2 = $_POST[ $data_field_name2 ];
        update_option( $opt_name2, $opt_val2 );
        ?>
            <div class="updated"><p><strong><?php _e('Opciones guardadas.', 'wp_skinhome_menu' ); ?></strong></p></div>
        <?php
        }

        echo '<div class="wrap">';
            echo "<h2>" . __( 'AdsMiddle Opciones', 'wp_skinhome_menu' ) . "</h2>";

        ?>
        <h4>AddsMiddle, es un plugin que según los párrafos indicados, mostrará el anuncio. Se recomienda poner 3, 4.<br>
        Si deseas que esté en medio del post, ingresar "medio" sin comillas.</h4>
        <form name="form1" method="post" action="">
            <input type="hidden" name="<?php echo $hidden_field_name; ?>" value="ruta_hidden">
            <input type="hidden" name="<?php echo $hidden_field_name2; ?>" value="ruta_hidden">
            <p>
                <label style="float:left; margin: 10px 0px 5px; width: 180px;"><?php _e('Parrafos en los que mostrar anuncio: ', 'wp_skinhome_menu' ); ?></label>
                
                <input class="regular-text" style="border: 1px solid #DDD;margin: 12px 3px 0px 0px;padding: 5px; width: 400px;" name="<?php echo $data_field_name; ?>" id="" placeholder="Ejm: 1, 2, 4" value="<?php echo $opt_val;?>" />

            </p>
            <p style="display:block;">
                <label style="float:left; margin: 10px 0px 5px; width: 180px;"><?php _e('Adsense: ', 'wp_skinhome_menu' ); ?></label>
                <textarea style="float: left;margin: 12px 3px 0px 0px;padding: 5px; width: 400px;" name="<?php echo $data_field_name2; ?>" id="" placeholder="Recomendado : 336 x 280"><?php echo stripslashes(htmlspecialchars($opt_val2));?></textarea> 

            </p>
            <br style="height: 2px; line-height: 82px;">
            <p class="submit" style="display:block;">
                <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
            </p>

            <p>
              <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                <input type="hidden" name="cmd" value="_s-xclick">
                <input type="hidden" name="hosted_button_id" value="FAFVZ4DPJRQQJ">
                <input type="image" src="https://www.paypalobjects.com/es_ES/ES/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal. La forma rápida y segura de pagar en Internet.">
                <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
              </form>
            </p>
            <p><i>Recomendaciones a: wjharil@gmail.com</i></p>
        </form>
    </div>
  <?php
  }

  function ad_mid_content($content) {
    if( !is_single() )
      return $content;   
    global $post; $postid = $post->ID;

    global $table_prefix;
    $dbh = new wpdb( DB_USER, DB_PASSWORD, DB_NAME, DB_HOST );
    $table = $table_prefix.'options';
    $query_cantidad = "SELECT option_value FROM $table WHERE option_name = 'wp_middle_cantidad'";
    $query_adsense = "SELECT option_value FROM $table WHERE option_name = 'wp_middle_adsense'";
    $res_cantidad = $dbh->get_results( $query_cantidad );
    $res_adsense = $dbh->get_results( $query_adsense );

    $data_contenido = $res_cantidad[0]->option_value;
    $word_count = str_word_count(strip_tags($content));
    
    if($word_count < 190) return $content;
   
    $content = explode ( "</p>", $content );

    if($data_contenido == 'medio')
    {
      $data_contenido = array(ceil(count($content)/2));
    }else
    {
      $data_contenido = explode(', ', $data_contenido);
    }
    $nuevo_contenido = '';
   
    for ( $i = 0; $i < count ( $content ); $i ++) {      
      if(in_array($i, $data_contenido)){
        $nuevo_contenido .= '<div class="anuncio_titulo" style="margin: 10px 0px !important;display: block;text-align: center;">';
        $nuevo_contenido .= stripslashes($res_adsense[0]->option_value);
        $nuevo_contenido .= '</div>';
      }
      $nuevo_contenido .= $content[$i] . "";
    }
    return $nuevo_contenido;
  }
  add_action('the_content','ad_mid_content' );

  //Inicialmente tomando de: http://ayudawp.com/insertar-anuncio-despues-de-un-parrafo-concreto-en-wordpress/
  //Pasado a plugin por : wjharil.com
?>