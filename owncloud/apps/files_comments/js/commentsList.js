$(document).ready(function () {
    var tdir = '',tfilename = '',poolingInterval = null;
    if (typeof FileActions !== 'undefined') {
        FileActions.registerEvent(function (dir, filename) {
            tdir = dir;
            tfilename = filename;
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
                    showControls(result.data);
                } else {
                    OC.dialogs.alert(result.data.message, t('files_comments', 'An error occurred!'));
                }
            }
        )
    }

    function showControls(comments) {
        var commentingUsersHtml = 'Users commenting:<br/><ul id="commentingUsers"></ul>';
        var commentInputHtml = '<textarea id="commentInput"></textarea>';
        var addCommentButtonHtml = '<button id="addComment">Add</button>';
        $('#comments').append(commentingUsersHtml);
        $('#comments').append(commentInputHtml);
        $('#comments').append(addCommentButtonHtml);
        $.each(comments, function (index, value) {
            $('#comments').append(generateCommentWrapper(value.uid_owner, value.uid_createdby, value.body,value.id));
        });
        bindEvents();
    }

    function generateCommentWrapper(uid_owner, uid_createdby, body, id) {
        var deleteButton = '';
        if(oc_current_user == uid_owner || oc_current_user == uid_createdby){
            deleteButton = '<a id="commentDelete" class="comment_delete" data-id="'+id+'">delete</a>';
        }
        return '<div id="commentWrapper" class="comment_wrapper">' +
            '<div id="commentBody" class="comment_body">' + body + '</div>' +
            '<div id="commentCreatedBy" class="comment_created_by">created by:' + uid_createdby + '</div>' +
            deleteButton +
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
                            showCommentsList(tdir,tfilename);
                        } else {
                            OC.dialogs.alert(result.data.message, t('files_comments', 'An error occurred!'));
                        }
                    });
            }
        });
        
        $('.comment_delete').on('click', function () {
            var deleteButton = $(this);
            var id = deleteButton.attr('data-id');
            $.post(OC.filePath('files_comments', 'ajax', 'deleteComment.php'),
                {id:id},
            function (result) {
                if(result.status == 'success'){
                    showCommentsList(tdir,tfilename);
                }
            })
        });

        if(!poolingInterval) {
            poolingInterval = window.setInterval(function () {
                var filepath = $('#editor').attr('data-dir') + '/' + $('#editor').attr('data-filename');
                $.get(OC.filePath('files_comments', 'ajax', 'getCommentingUsers.php'),
                    {filepath:filepath},
                    function (result) {
                    $('#commentingUsers').empty();
                    if(result.status == 'success'){
                        $.each(result.data,function (index,value) {
                            $('#commentingUsers').append('<li>' +value+'</li>');
                        });
                    }else{

                    }
                })
            }, 1000);
        }
    }
});
