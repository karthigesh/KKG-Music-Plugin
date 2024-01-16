var urlParams = new URLSearchParams(window.location.search);
if (urlParams.has('status')) {
    setTimeout(function () {
        history.pushState('', '',
            location.href.split('&')[0]);
        location.reload();
    }, 10000); // 1 min = 1000 ms * 60 = 60000
}
jQuery(function ($) {
    var custom_uploader;
    $('#kkg_up_btn').click(function(){
        //If the uploader object has already been created, reopen the dialog
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }
        //Extend the wp.media object
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Music',
            button: {
                text: 'Choose Music'
            },
            multiple: false
        });

        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on('select', function() {
            attachment = custom_uploader.state().get('selection').first().toJSON();
            if(attachment.type === "audio"){
                $('#kkgmusic_file').val(attachment.url);
                $('#kkgmusic_filename').val(attachment.filename);
            }else{               
                $('#kkgmusic_file').val("");
                $('#kkgmusic_filename').val("");
            }
        });

        //Open the uploader dialog
        custom_uploader.open();
    });

});