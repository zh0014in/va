OC.Comments = {
    icons: [],
    itemUsers: [],
    itemGroups: [],
    itemPrivateLink: false,
    users: [],

    loadItem: function (item) {
        $.ajax({
            type: 'GET',
            url: OC.filePath('files_comments', 'ajax', 'getitem.php'),
            data: {item: item},
            async: false,
            success: function (result) {
                if (result && result.status === 'success') {
                    var item = result.data;
                    OC.Comments.itemUsers = item.users;
                }
            }
        });
    },
    invite: function (filepath, uid_commenting_with, permissions, callback) {
        $.post(OC.filePath('files_comments', 'ajax', 'invite.php'), {
            sources: filepath,
            uid_commenting_with: uid_commenting_with,
            permissions: permissions
        }, function (result) {
            if (result && result.status === 'success') {
                if (callback) {
                    callback(result.data);
                }
            } else {
                OC.dialogs.alert(result.data.message, 'Error while sharing');
            }
        });
    },
    changePermissions: function (filepath, uid_commenting_with, permissions) {
        $.post(OC.filePath('files_comments', 'ajax', 'setpermissions.php'), {
            source: filepath,
            uid_commenting_with: uid_commenting_with,
            permissions: permissions
        }, function (result) {
            if (!result || result.status !== 'success') {
                OC.dialogs.alert('Error', 'Error while changing permissions');
            }
        });
    },
    showDropDown: function (item, appendTo) {
        OC.Comments.loadItem(item);
        var html = '<div id="commenting_dropdown" class="drop" data-item="' + item + '">';
        html += '<select data-placeholder="User" id="commenting_with" class="chzen-select">';
        html += '<option value=""></option>';
        html += '</select>';
        html += '<div id="commentingWithList">';
        html += '<ul id="commentingUserList"></ul>';
        html += '</div>';
        html += '</div>';
        $(html).appendTo(appendTo);
        if (OC.Comments.users.length < 1) {
            $.ajax({
                type: 'GET',
                url: OC.filePath('files_comments', 'ajax', 'userautocomplete.php'),
                async: false,
                success: function (users) {
                    if (users) {
                        OC.Comments.users = users;
                        $.each(users, function (index, user) {
                            $(user).appendTo('#commenting_with');
                        });
                        $('#commenting_with').trigger('liszt:updated');
                    }
                }
            });
        } else {
            $.each(OC.Comments.users, function (index, user) {
                $(user).appendTo('#commenting_with');
            });
            $('#commenting_with').trigger('liszt:updated');
        }
        if (OC.Comments.itemUsers) {
            $.each(OC.Comments.itemUsers, function (index, user) {
                if (user.parentFolder) {
                    OC.Comments.addCommentPermissionWith(user.uid, user.permissions, user.parentFolder);
                } else {
                    OC.Comments.addCommentPermissionWith(user.uid, user.permissions, false);
                }
            });
        }
        $('#commenting_dropdown').show('blind');
        $('#commenting_with').chosen();
    },
    hideDropDown: function (callback) {
        $('#commenting_dropdown').hide('blind', function () {
            $('#commenting_dropdown').remove();
            if (callback) {
                callback.call();
            }
        });
    },
    addCommentPermissionWith: function (uid_commenting_with, permissions, parentFolder) {
        if (parentFolder) {
            var commentingWith = '<li>Parent folder ' + parentFolder + ' shared with ' + uid_commenting_with + '</li>';
        } else {
            var checked = ((permissions > 0) ? 'checked="checked"' : 'style="display:none;"');
            var style = ((permissions == 0) ? 'style="display:none;"' : '');
            var commentingWith = '<li data-uid_commenting_with="' + uid_commenting_with + '">';
            commentingWith += uid_commenting_with;
            commentingWith += '<input type="checkbox" name="permissions" id="' + uid_commenting_with + '" class="permissions" ' + checked + ' />';
            commentingWith += '</li>';
        }
        $(commentingWith).appendTo('#commentingUserList');
        // Remove user from select form
        $('#commenting_with option[value="' + uid_commenting_with + '"]').remove();
        $('#commenting_with').trigger('liszt:updated');
    },
    dirname: function (path) {
        return path.replace(/\\/g, '/').replace(/\/[^\/]*$/, '');
    }
}

$(document).ready(function () {

    if (typeof FileActions !== 'undefined') {
        FileActions.register('all', 'Invite to comment', function (filename) {
            // Return the correct sharing icon
            if (scanFiles.scanning) {
                return;
            } // workaround to prevent additional http request block scanning feedback
            var item = $('#dir').val() + '/' + filename;
            // Check if icon is in cache
            if (OC.Comments.icons[item]) {
                return OC.Comments.icons[item];
            } else {
                var last = '';
                var path = OC.Comments.dirname(item);
                // Search for possible parent folders that are shared
                while (path != last) {
                    if (OC.Comments.icons[path]) {
                        OC.Comments.icons[item] = OC.Comments.icons[path];
                        return OC.Comments.icons[item];
                    }
                    last = path;
                    path = OC.Comments.dirname(path);
                }
                OC.Comments.icons[item] = OC.imagePath('core', 'actions/info');
                return OC.Comments.icons[item];
            }
        }, function (filename) {
            var file = $('#dir').val() + '/' + filename;
            var appendTo = $('tr').filterAttr('data-file', filename).find('td.filename');
            // Check if drop down is already visible for a different file
            if (($('#commenting_dropdown').length > 0)) {
                if (file != $('#commenting_dropdown').data('item')) {
                    OC.Comments.hideDropDown(function () {
                        $('tr').removeClass('mouseOver');
                        $('tr').filterAttr('data-file', filename).addClass('mouseOver');
                        OC.Comments.showDropDown(file, appendTo);
                    });
                }
            } else {
                $('tr').filterAttr('data-file', filename).addClass('mouseOver');
                OC.Comments.showDropDown(file, appendTo);
            }
        });
    }
    ;

    $(this).click(function (event) {
        if (!($(event.target).hasClass('drop')) && $(event.target).parents().index($('#commenting_dropdown')) == -1) {
            if ($('#commenting_dropdown').is(':visible')) {
                OC.Comments.hideDropDown(function () {
                    $('tr').removeClass('mouseOver');
                });
            }
        }
    });

    $('#commentingWithList li').live('mouseenter', function (event) {
        // Show permissions and unshare button
        $(':hidden', this).show();
    });

    $('#commentingWithList li').live('mouseleave', function (event) {
        // Hide permissions and unshare button
        $('a', this).hide();
        if (!$('input:[type=checkbox]', this).is(':checked')) {
            $('input:[type=checkbox]', this).hide();
            $('label', this).hide();
        }
    });

    $('#commenting_with').live('change', function () {
        var item = $('#commenting_dropdown').data('item');
        var uid_commenting_with = $(this).val();

        OC.Comments.invite(item, uid_commenting_with, 0, function () {
            OC.Comments.addCommentPermissionWith(uid_commenting_with, 0, false);
        });
    });

    $('.permissions').live('change', function () {
        var permissions = (this.checked) ? 1 : 0;
        OC.Comments.changePermissions($('#commenting_dropdown').data('item'), $(this).parent().data('uid_commenting_with'), permissions);
    });
});