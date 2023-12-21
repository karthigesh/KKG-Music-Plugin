<?php
if (!defined('ABSPATH'))
    exit;
$status = '';
if (filter_has_var(INPUT_GET, 'status')) {
    $stat = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_SPECIAL_CHARS);
    $status = '<div class="row mt-2"><div class="col-md-12">';
    switch ($stat) {
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
            $status .= "<div class='alert alert-warning' role='alert'>
            There has been issue on saving the Music Url! try again! 
        </div>";
            break;
        case 'failuref':
            $ins = ( isset($GLOBALS['uploadError']) ) ? $GLOBALS['uploadError'] : ' ';
            $status .= "<div class='alert alert-warning' role='alert'>
            There has been issue on saving the Music Url! try again!" . $ins . " 
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
            <h1><?php echo esc_html(__('Welcome to KKG Music App!', 'kkg-music')); ?></h1>
        </div>
    </div> 
    <?php
    $allowed = array(
        'div' => array(
            'class' => true,
            'role' => true,
        )
    );
    echo wp_kses($status, $allowed);
    ?>   
    <div class='row mt-2'>
        <div class='col-md-12'>
            <a href="<?php echo esc_url(KKGMP_ADD_URL); ?>" class="page-title-action"><?php echo esc_html(__('Add new', 'kkg-music')); ?></a>
            <a href="<?php echo esc_url(KKGMP_UP_URL); ?>" class="page-title-action"><?php echo esc_html(__('Upload', 'kkg-music')); ?></a>
        </div>
    </div>
    <div class='row mt-2'>
        <div class='col-md-12'>
            <p><?php echo esc_html(__('Use Shortcode [view_kkg_music element={{element}}] in pages and post to display the music player in your themes.', 'kkg-music')); ?></p>
        </div>
    </div>
    <?php
    $myListTable = new kkgmp_list_Table();
    $myListTable->prepare_items();
    $myListTable->search_box('Search', 'search');
    $myListTable->display()
    ?>
</div><!-- / wrap -->