<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class='wrap'>
    <div class="row">
        <div class="col-md-12">
          <h1><?php echo esc_html(__( 'Welcome to KKG Music App!', 'kkg_music' ));?></h1>
        </div>
    </div>    
    <div class='row mt-2'>
      <div class='col-md-12'>
        <a href="/wp-admin/admin.php?page=add_music" class="page-title-action"><?php echo esc_html(__( 'Add new', 'kkg_music' ));?></a>
        <a href="/wp-admin/admin.php?page=up_music" class="page-title-action"><?php echo esc_html(__( 'Upload', 'kkg_music' ));?></a>
      </div>
    </div>
    <div class='row mt-2'>
      <div class='col-md-12'>
        <p><?php echo esc_html(__( 'Use Shortcode [view_kkg_music] in pages and post to display the music player in your themes.', 'kkg_music' ));?></p>
      </div>
    </div>
</div><!-- / wrap -->

