<div class="row mt-3 mb-3" style="display:none;">
    <div class="col-md-12">
        <label for="choosen_up" class="show_if_music" style="">
            <?php echo esc_html('Set Music File:'); ?>
            <input type="radio" name="kkgmusic_chooseType" id="choosen_up" class="music_chooseType" value="upload" checked>
        </label>
    </div>
</div>
<div class="mt-3 mb-3">
    <div class="show-upload"> 
        <a href="#" id="kkg_up_btn">Upload Music</a>
        <input id="kkgmusic_file" type="hidden" name="kkgmusic_file" value="<?php echo esc_attr($musicurl); ?>" />
        <input id="kkgmusic_filename" type="hidden" name="kkgmusic_filename" value="<?php echo esc_attr($musicfilename); ?>" />
    </div>
</div>