<div class='wrap'>
    <div class="row">
        <div class="col-md-12">
            <h1>Welcome to KKG Music App!</h1>
        </div>
    </div>    
    <form method = 'POST'>
      <input type = 'hidden' name = 'action' value = 'kkg_music_save'>
      <?php wp_nonce_field();?>
      <input type = 'hidden' name = 'redirectToUrl' value = ''>  
      <div class = 'row'>
        <div class = 'col-md-4'>          
          <p>Title</p>
        </div>
        <div class = 'col-md-8'>
          <?php echo $GLOBALS['formmusicContent'];?>
        </div>
      </div><!-- / row -->    
      <div class = 'row mt-3'>
        <div class = 'col-md-4'>          
          <p><?php echo $GLOBALS['formmusicTitle'];?></p>
        </div>
        <div class = 'col-md-8'>
          <?php echo $GLOBALS['formmusicUrl'];?>
        </div>
      </div><!-- / row -->
      <input type = 'submit' name = 'Submit' class = 'btn btn-primary urlsubmit'>
  </form>
</div><!-- / wrap -->

