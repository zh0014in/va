function showCommentsList(dir, filename) {
    // remove old comments
    $('#comments').remove();
    $('#content').append('<div id="comments"></div>');
    console.log(dir);console.log(filename);
    var data = $.getJSON(
        OC.filePath('files_comments','ajax','loadcomments.php'),
        {file:filename,dir:dir},
        function (result) {
            if(result.status == 'success'){
                console.log(result);

            }else{
                OC.dialogs.alert(result.data.message, t('files_comments','An error occurred!'));
            }
        }
    )
}

function showControls() {

}


$(document).ready(function () {
    if(typeof FileActions!=='undefined'){
        FileActions.registerEvent(function(dir,filename){
            showCommentsList(dir,filename);
        });
    }

});
