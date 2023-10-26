<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    $musicName = 'No file chosen...';
    $musicUrl = '';
    $required = 'required';
    if ( filter_has_var( INPUT_GET, 'action' ) ) {
        $data = kkgmusic_getsingle( filter_input( INPUT_GET, 'element' ) );
        if($data){
          $musicUrl = ( isset( $data[ 'sub_musicurl' ] ) )?$data[ 'sub_musicurl' ]:'';
          $musicName = basename($musicUrl);
          $required = '';
        }
    }
   
?>
<div class='wrap'>
    <div class="row">
        <div class="col-md-12">
            <h1><?php echo esc_html( 'Welcome to KKG Music App!');?></h1>
        </div>
    </div>
<form method = 'POST' enctype="multipart/form-data">
<input type = 'hidden' name = 'action' value = 'kkg_music_upload'>
<input type = 'hidden' name = 'existFile' value = '<?php echo esc_html($musicUrl);?>'>
<?php wp_nonce_field();
?>
<input type = 'hidden' name = 'redirectToUrl' value = ''>
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