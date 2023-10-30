<?php
if ( ! defined( 'ABSPATH' ) ) exit;
// Exit if accessed directly
$musicUrl = $musicTitle = '';
$title =  'kindly Enter the Music Streaming URL' ;
if ( filter_has_var( INPUT_GET, 'action' ) ) {
    $input = absint( filter_input( INPUT_GET, 'element', FILTER_SANITIZE_NUMBER_INT ) );
    $data = kkgmusic_getsingle( $input );
    if ( $data ) {
        $title =  'kindly Update the Music Streaming URL';
        $musicUrl = ( isset( $data[ 'sub_musicurl' ] ) )?esc_url( $data[ 'sub_musicurl' ] ):'';
        $musicTitle = ( isset( $data[ 'music_title' ] ) )?esc_html( $data[ 'music_title' ] ):'';
    }
}
$formmusicTitle = $title;
$formmusicUrl = kkgmusic_render_input( 'url', 'musicUrl', 'musicUrl', $musicUrl, TRUE );
$formmusicContent = kkgmusic_render_input( 'text', 'musicTitle', 'musicTitle', $musicTitle, TRUE );
$formmusicHidden = kkgmusic_render_input( 'hidden', 'action', '', 'kkg_music_save');
?>
<div class = 'wrap'>
<div class = 'row'>
<div class = 'col-md-12'>
<h1><?php echo esc_html( 'Welcome to KKG Music App!' );
?></h1>
</div>
</div>
<form method = 'POST'>
<input type = 'hidden' name = 'action' value = 'kkg_music_save'>
<?php wp_nonce_field( 'kkg_music_save' );
echo wp_kses($formmusicHidden,array( 'input' => array('type' => array(),'name' => array(),'value' => array())));
?>
<div class = 'row mt-2'>
<div class = 'col-md-4'>
<p><?php echo esc_html( 'Title' );
?></p>
</div>
<div class = 'col-md-8'>
<?php echo wp_kses($formmusicContent,array( 'input' => array('type' => array(),'id' => array(),'name' => array(),'class' => array(),'value' => array(),'required' => array())));
?>
</div>
</div><!-- / row -->
<div class = 'row mt-3'>
<div class = 'col-md-4'>
<p><?php echo esc_html($formmusicTitle);
?></p>
</div>
<div class = 'col-md-8'>
<?php echo wp_kses($formmusicUrl,array( 'input' => array('type' => array(),'id' => array(),'name' => array(),'class' => array(),'value' => array(),'required' => array())));?>
</div>
</div><!-- / row -->
<input type = 'submit' name = 'Submit' class = 'btn btn-primary urlsubmit'>
</form>
</div><!-- / wrap -->

