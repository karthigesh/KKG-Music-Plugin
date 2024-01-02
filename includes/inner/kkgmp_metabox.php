<div class="row mt-3 mb-3">
    <div class="col-md-12">
        <label for="choosen_url" class="show_if_music" style="">
        <?php echo esc_html( 'Url:');?>
            <input type="radio" name="kkgmusic_chooseType" id="choosen_url" class="music_chooseType" value="url" <?php echo esc_attr( $choosen_url );?>>
        </label>
        <label for="choosen_up" class="show_if_music" style="">
        <?php echo esc_html( 'Upload:');?>
            <input type="radio" name="kkgmusic_chooseType" id="choosen_up" class="music_chooseType" value="upload" <?php echo esc_attr( $choosen_up );?>>
        </label>
    </div>
</div>
<div class="mt-3 mb-3">
    <div class="row show-hide show-url"> 
        <div class="col-md-3">     
            <label for="choosen_url" class="show_if_music" style="">
                <?php echo esc_html( 'Url:');?>            
            </label>        
        </div>
        <div class="col-md-9">
        <input type="url" style="width:100%" id="kkgmusic_url" name="kkgmusic_url" value="<?php echo esc_attr( $musicurl );?>">
        </div>
    </div>
    <div class="row show-hide show-upload"> 
        <div class="col-md-3">     
            <label for="choosen_upload" class="show_if_music" style="">
                <?php echo esc_html( 'Upload:');?>           
            </label>        
        </div>
        <div class="col-md-9">
            <input id="kkg_up_btn" type="button" class="button button-primary button-large" value="Upload Music" />
            <input id="kkgmusic_file" type="hidden" name="kkgmusic_file" value="<?php echo esc_attr( $musicurl );?>" />
            <input id="kkgmusic_filename" type="hidden" name="kkgmusic_filename" value="<?php echo esc_attr( $musicfilename );?>" />
        </div>
    </div>
    <?php if(esc_attr( $musicurl ) != ""){?>  
    <div class="row show-music">         
        <div class="col-md-9">
        <audio controls controlsList="nodownload noremoteplayback ">
            <source src="<?php echo esc_attr( $musicurl );?>" type="audio/mp3">
            Your browser does not support the audio element.
        </audio>
        </div>
    </div>
    <?php }?> 
</div>