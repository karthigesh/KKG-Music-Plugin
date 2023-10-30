<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$status = '';
    if ( isset( $_GET[ 'status' ] ) ) {
        $status = '<div class="row mt-2"><div class="col-md-12">';
        switch( $_GET[ 'status' ] ) {
            case 'successu':
            $status .= "<div class='alert alert-success' role='alert'>
            The Music Url has been saved successfully!
        </div>";
            break;
            case 'successf':
              $status .= "<div class='alert alert-success' role='alert'>
              The Music File has been saved successfully!
          </div>";
              break;
            case 'failure':
            $ins = ( isset( $GLOBALS[ 'uploadError' ] ) )?$GLOBALS[ 'uploadError' ]:' ';
            $status .= "<div class='alert alert-warning' role='alert'>
            There has been issue on saving the Music Url! try again!".$ins." 
        </div>";
            break;
            case 'delete':
            $status .= "<div class='alert alert-danger' role='alert'>
            The Music Url has been deleted successfully!
        </div>";
            break;
            case 'deletefail':
            $status .= "<div class='alert alert-warning' role='alert'>
            The Music Url has not been deleted! try again!
        </div>";

            break;
        }
        $status .= '</div></div>';
    }
  ?>
<div class='wrap'>
    <div class="row">
        <div class="col-md-12">
          <h1><?php echo esc_html(__( 'Welcome to KKG Music App!', 'kkg-music' ));?></h1>
        </div>
    </div> 
    <?php echo $status;?>   
    <div class='row mt-2'>
      <div class='col-md-12'>
        <a href="<?php echo site_url('/wp-admin/admin.php?page=add_music');?>" class="page-title-action"><?php echo esc_html(__( 'Add new', 'kkg-music' ));?></a>
        <a href="<?php echo site_url('/wp-admin/admin.php?page=up_music');?>" class="page-title-action"><?php echo esc_html(__( 'Upload', 'kkg-music' ));?></a>
      </div>
    </div>
    <div class='row mt-2'>
      <div class='col-md-12'>
        <p><?php echo esc_html(__( 'Use Shortcode [view_kkg_music] in pages and post to display the music player in your themes.', 'kkg-music' ));?></p>
      </div>
    </div>
</div><!-- / wrap -->

