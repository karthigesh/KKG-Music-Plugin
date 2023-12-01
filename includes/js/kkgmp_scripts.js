var urlParams = new URLSearchParams(window.location.search);
if (urlParams.has('status')) {
    setTimeout(function () {
        history.pushState('', '',
            location.href.split('&')[0]);
        location.reload();
    }, 10000); // 1 min = 1000 ms * 60 = 60000
}
jQuery(function ($) {
    $('#chooseFile').bind('change', function () {
        var filename = $("#chooseFile").val();
        var fileExtension = ['mp3','aac','m4a','amr'];
        if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
            alert("Only '.mp3' formats are allowed.");
            $(".file-upload").removeClass('active');
            $("#noFile").text("No file chosen...");
            return false;
        } else {
            if (/^\s*$/.test(filename)) {
                $(".file-upload").removeClass('active');
                $("#noFile").text("No file chosen...");
            }
            else {
                $(".file-upload").addClass('active');
                $("#noFile").text(filename.replace("C:\\fakepath\\", ""));
            }
        }

    });
    $('#musicUrl').change(function () {
        let text = this.value;
        let pattern = /^.*\.(mp3|aac|m4a|amr)$/i;
        if (pattern.test(text)) {
            $('.urlsubmit').attr('disabled', false);
        } else {
            $('.urlsubmit').attr('disabled', true);
        }
    });
    $('#delete_music').click(function(){
        var id = $(this).data('id');
        var url = $(this).data('url');
        var nonce = $(this).data('nonce');
        var action = $(this).data('action');
        if (confirm("Are you sure to delete?")) {
            $.ajax({
                type : "post",
                dataType : "json",
                url : url,
                data : {id: id, nonce : nonce, action: action},
                success: function(response) {
                if(response.status) {
                        window.location.href = response.url;
                }else {
                    alert("Your vote could not be added")
                }
                }
            });  
        } 
    });
});