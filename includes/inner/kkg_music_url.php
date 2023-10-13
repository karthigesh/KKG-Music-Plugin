<?php
    $musicUrl = $musicTitle = '';
    $title = esc_html( 'kindly Enter the Music Streaming URL', 'kkg_music' );
    if ( filter_has_var( INPUT_GET, 'action' ) ) {
        $data = getkkgmusic( filter_input( INPUT_GET, 'element' ) );
        if($data){
          $title =esc_html( 'kindly Update the Music Streaming URL', 'kkg_music' );
          $musicUrl = ( isset( $data[ 'sub_musicurl' ] ) )?$data[ 'sub_musicurl' ]:'';
          $musicTitle = ( isset( $data[ 'music_title' ] ) )?$data[ 'music_title' ]:'';
        }
    }
    $formmusicTitle = $title;
    $formmusicUrl = render_input( 'url', 'musicUrl', 'musicUrl', $musicUrl, TRUE );
    $formmusicContent = render_input( 'text', 'musicTitle', 'musicTitle', $musicTitle, TRUE );
?>
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
          <?php echo $formmusicContent;?>
        </div>
      </div><!-- / row -->    
      <div class = 'row mt-3'>
        <div class = 'col-md-4'>          
          <p><?php echo $formmusicTitle;?></p>
        </div>
        <div class = 'col-md-8'>
          <?php echo $formmusicUrl;?>
        </div>
      </div><!-- / row -->
      <input type = 'submit' name = 'Submit' class = 'btn btn-primary urlsubmit'>
  </form>
</div><!-- / wrap -->

