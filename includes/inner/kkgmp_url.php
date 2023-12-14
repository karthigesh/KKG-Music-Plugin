<?php
if ( ! defined( 'ABSPATH' ) ){ exit;}
// Exit if accessed directly
$musicUrl = $musicTitle = '';
$musicId = 0;
if ( filter_has_var( INPUT_GET, 'action' ) ) {
    $input = absint( filter_input( INPUT_GET, 'element', FILTER_SANITIZE_NUMBER_INT ) );
    $musicId = kkgmusic_encryptor('encrypt', $input);
    $data = kkgmusic_getsingle( $input );
    if ( $data ) {
        $musicUrl = ( isset( $data[ 'sub_musicurl' ] ) )?sanitize_url( $data[ 'sub_musicurl' ], array('http', 'https') ):'';
        $musicTitle = ( isset( $data[ 'music_title' ] ) )?esc_attr( $data[ 'music_title' ] ):'';
    }else{
        wp_safe_redirect(KKGMP_LIST_URL, 307);
        die;
    }
}
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
<form method = 'POST' action="<?php echo esc_attr('admin-post.php'); ?>" class="kkgmp_upload" >
<input type = 'hidden' name = 'action' value = 'kkg_music_save'>
<input type = 'hidden' name = 'postNum' value = '<?php echo esc_html(sanitize_text_field($musicId));?>'>
<?php wp_nonce_field( 'kkg_music_save' );
echo wp_kses($formmusicHidden,array( 'input' => array('type' => array(),'name' => array(),'value' => array())));
?>
<table class="form-table" role="presentation">
<tr class="form-field form-required">
    <th scope="row"><label for="musicTitle"><?php _e( 'Title' ); ?> <span class="description"><?php _e( '(required)' ); ?></span></label></th>
    <td><?php echo wp_kses($formmusicContent,array( 'input' => array('type' => array(),'id' => array(),'name' => array(),'class' => array(),'value' => array(),'required' => array())));
?></td>
</tr>
<tr class="form-field form-required">
    <th scope="row"><label for="musicUrl"><?php _e('kindly Enter the Music Streaming URL' ); ?> <span class="description"><?php _e( '(required)' ); ?></span></label></th>
    <td><?php echo wp_kses($formmusicUrl,array( 'input' => array('type' => array(),'id' => array(),'name' => array(),'class' => array(),'value' => array(),'required' => array())));
?></td>
</tr>
</table>
<input type = 'submit' name = 'Submit' class = 'button button-primary urlsubmit'>
</form>
</div><!-- / wrap -->

