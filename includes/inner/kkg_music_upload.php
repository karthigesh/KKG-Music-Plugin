<?php
require_once plugin_dir_path( __FILE__ ) . 'kkgmp-functions.php';
?>
<form method = 'POST' enctype="multipart/form-data">
<input type = 'hidden' name = 'action' value = 'kkg_music_upload'>
<?php wp_nonce_field();
?>
<input type = 'hidden' name = 'redirectToUrl' value = ''>
<div class = 'row'>
<div class = 'col-md-4'>
<p>Upload The Music File</p>
</div>
<div class = 'col-md-8'>
<div class="file-upload">
  <div class="file-select">
    <div class="file-select-button" id="fileName">Choose File</div>
    <div class="file-select-name" id="noFile">No file chosen...</div> 
    <input type="file" name="chooseFile" id="chooseFile" accept="audio/mp3" required>
  </div>
</div>
</div>
</div><!-- / row -->
<input type = 'submit' name = 'Submit' class = 'btn btn-primary'>
</form>