$(document).ready(function () {
    if (typeof FileActions !== 'undefined') {
        FileActions.registerEvent(function (dir, filename) {
            showCommentsList(dir, filename);
        });
    }


    function showCommentsList(dir, filename) {
        // remove old comments
        $('#comments').remove();
        $('#content').append('<div id="comments"></div>');
        console.log(dir);
        console.log(filename);
        var data = $.getJSON(
            OC.filePath('files_comments', 'ajax', 'loadcomments.php'),
            {file: filename, dir: dir},
            function (result) {
                if (result.status == 'success') {
                    console.log(result);
                    showControls(result.data);
                } else {
                    OC.dialogs.alert(result.data.message, t('files_comments', 'An error occurred!'));
                }
            }
        )
    }

    function showControls(comments) {
        var commentInputHtml = '<textarea id="commentInput"></textarea>';
        var addCommentButtonHtml = '<button id="addComment">Add</button>';

        $('#comments').append(commentInputHtml);
        $('#comments').append(addCommentButtonHtml);
        $.each(comments, function (index, value) {
            $('#comments').append(generateCommentWrapper(value.uid_owner, value.uid_createdby, value.body));
        });
        bindEvents();
    }

    function generateCommentWrapper(uid_owner, uid_createdby, body) {
        return '<div id="commentWrapper" class="comment_wrapper">' +
            '<div id="commentBody" class="comment_body">' + body + '</div>' +
            '<div id="commentCreatedBy" class="comment_created_by">created by:' + uid_createdby + '</div>' +

            '</div>';
    }

    function bindEvents() {
        $('#addComment').on('click', function () {
            var commentBody = $('#commentInput').val();
            if (commentBody) {
                // Get file path
                var filepath = $('#editor').attr('data-dir') + '/' + $('#editor').attr('data-filename');
                $.post(OC.filePath('files_comments', 'ajax', 'addComment.php'),
                    {filepath: filepath, body: commentBody}, function (result) {
                        if (result.status == 'success') {

                        } else {
                            OC.dialogs.alert(result.data.message, t('files_comments', 'An error occurred!'));
                        }
                    });
            }
        });
    }
});
