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
    <div class="row">
        <div class="col-md-12">
            <h1><?php echo esc_html( 'Welcome to KKG Music App!');?></h1>
        </div>
    </div>
<form method = 'POST' action="<?php echo esc_attr('admin-post.php'); ?>" enctype="multipart/form-data" >
    <input type="hidden" name="action" value="kkg_music_upload" />
<?php wp_nonce_field( 'kkg_music_upload' ); ?> 
<input type = 'hidden' name = 'existFile' value = '<?php echo esc_html(sanitize_text_field($musicUrl));?>'>
<input type = 'hidden' name = 'postNum' value = '<?php echo esc_html(sanitize_text_field($musicId));?>'>
<input type = 'hidden' name = 'redirectToUrl' value = ''>
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
<div class = 'row mt-2'>
<div class = 'col-md-4'>
<p><?php echo esc_html( 'Upload The Music File');?></p>
</div>
<div class = 'col-md-8'>
<div class="file-upload">
  <div class="file-select">
    <div class="file-select-button" id="fileName"><?php echo esc_html( 'Choose File');?></div>
    <div class="file-select-name" id="noFile"><?php echo esc_html($musicName);?></div> 
    <input type="file" name="chooseFile" id="chooseFile" accept="audio/mp3" <?php echo esc_html($required);?>>
  </div>
</div>
</div>
</div><!-- / row -->
<input type = 'submit' name = 'Submit' class = 'btn btn-primary'>
</form>
</div><!-- / wrap -->