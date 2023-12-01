<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    $musicName = 'No file chosen...';
    $musicUrl = $musicTitle = '';
    $musicId = 0;
    $required = 'required';
    if ( filter_has_var( INPUT_GET, 'action' ) ) {
        $input = absint( filter_input( INPUT_GET, 'element', FILTER_SANITIZE_NUMBER_INT ) );
        $musicId = kkgmusic_encryptor('encrypt', $input);
        $data = kkgmusic_getsingle( $input );
        if($data){
          $musicUrl = ( isset( $data[ 'sub_musicurl' ] ) )?$data[ 'sub_musicurl' ]:'';
          $musicTitle = ( isset( $data[ 'music_title' ] ) )?esc_attr( $data[ 'music_title' ] ):'';
          $musicName = basename($musicUrl);
          $required = '';
        }else{
            wp_safe_redirect(KKGMP_LIST_URL, 307);
            die;
        }
    }
$formmusicContent = kkgmusic_render_input( 'text', 'musicTitle', 'musicTitle', $musicTitle, TRUE );
?>
<div class='wrap'>
<h1 id="upload-head"><?php echo esc_html( 'Welcome to KKG Music App!');?></h1>
<form method = 'POST' action="<?php echo esc_attr('admin-post.php'); ?>" enctype="multipart/form-data" class="kkgmp_upload" >
    <input type="hidden" name="action" value="kkg_music_upload" />
<?php wp_nonce_field( 'kkg_music_upload' ); ?> 
<input type = 'hidden' name = 'existFile' value = '<?php echo esc_html(sanitize_text_field($musicUrl));?>'>
<input type = 'hidden' name = 'postNum' value = '<?php echo esc_html(sanitize_text_field($musicId));?>'>
<input type = 'hidden' name = 'redirectToUrl' value = ''>
<table class="form-table" role="presentation">
<tr class="form-field form-required">
    <th scope="row"><label for="musicTitle"><?php _e( 'Title' ); ?> <span class="description"><?php _e( '(required)' ); ?></span></label></th>
    <td><?php echo wp_kses($formmusicContent,array( 'input' => array('type' => array(),'id' => array(),'name' => array(),'class' => array(),'value' => array(),'required' => array())));
?></td>
</tr>
<tr class="form-field form-required">
    <th scope="row"><label for="chooseFile"><?php _e( 'Upload The Music File' ); ?> <span class="description"><?php _e( '(required)' ); ?></span></label></th>
    <td>
    <div class="file-upload">
        <div class="file-select">
            <div class="file-select-button" id="fileName"><?php echo esc_html( 'Choose File');?></div>
            <div class="file-select-name" id="noFile"><?php echo esc_html($musicName);?></div> 
            <input type="file" name="chooseFile" id="chooseFile" accept="audio/mp3" <?php echo esc_html($required);?>>
        </div>
    </div>
    </td>
</tr>
</table>
<input type = 'submit' name = 'Submit' class="button button-primary">
</form>
</div><!-- / wrap -->