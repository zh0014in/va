OC.Comments = {
    icons: [],
    itemUsers: [],
    itemGroups: [],
    itemPrivateLink: false,
    users: [],
    loadIcons: function () {
        // Cache all icons for shared files
        $.getJSON(OC.filePath('files_comments', 'ajax', 'getstatuses.php'), function (result) {
            if (result && result.status === 'success') {
                $.each(result.data, function (item, hasPrivateLink) {
                    if (hasPrivateLink) {
                        OC.Comments.icons[item] = OC.imagePath('core', 'actions/public');
                    } else {
                        OC.Comments.icons[item] = OC.imagePath('core', 'actions/shared');
                    }
                });
            }
        });
    },
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
                    OC.Comments.itemGroups = item.groups;
                    OC.Comments.itemPrivateLink = item.privateLink;
                }
            }
        });
    },
    invite: function (source, uid_shared_with, permissions, callback) {
        $.post(OC.filePath('files_comments', 'ajax', 'invite.php'), {
            sources: source,
            uid_shared_with: uid_shared_with,
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
    changePermissions: function (source, uid_shared_with, permissions) {
        $.post(OC.filePath('files_comments', 'ajax', 'setpermissions.php'), {
            source: source,
            uid_shared_with: uid_shared_with,
            permissions: permissions
        }, function (result) {
            if (!result || result.status !== 'success') {
                OC.dialogs.alert('Error', 'Error while changing permissions');
            }
        });
    },
    showDropDown: function (item, appendTo) {
        OC.Comments.loadItem(item);
        var html = '<div id="dropdown" class="drop" data-item="' + item + '">';
        html += '<select data-placeholder="User" id="share_with" class="chzen-select">';
        html += '<option value=""></option>';
        html += '</select>';
        html += '<div id="sharedWithList">';
        html += '<ul id="userList"></ul>';
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
                            $(user).appendTo('#share_with');
                        });
                        $('#share_with').trigger('liszt:updated');
                    }
                }
            });
        } else {
            $.each(OC.Comments.users, function (index, user) {
                $(user).appendTo('#share_with');
            });
            $('#share_with').trigger('liszt:updated');
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
        $('#dropdown').show('blind');
        $('#share_with').chosen();
    },
    hideDropDown: function (callback) {
        $('#dropdown').hide('blind', function () {
            $('#dropdown').remove();
            if (callback) {
                callback.call();
            }
        });
    },
    addCommentPermissionWith: function (uid_comment_with, permissions, parentFolder) {
        if (parentFolder) {
            var sharedWith = '<li>Parent folder ' + parentFolder + ' shared with ' + uid_comment_with + '</li>';
        } else {
            var checked = ((permissions > 0) ? 'checked="checked"' : 'style="display:none;"');
            var style = ((permissions == 0) ? 'style="display:none;"' : '');
            var sharedWith = '<li data-uid_shared_with="' + uid_comment_with + '">';
            sharedWith += uid_comment_with;
            sharedWith += '<input type="checkbox" name="permissions" id="' + uid_comment_with + '" class="permissions" ' + checked + ' />';
            sharedWith += '<label class="edit" for="' + uid_comment_with + '" ' + style + '>can edit</label>';
            sharedWith += '</li>';
        }
        $(sharedWith).appendTo('#userList');
        // Remove user from select form
        $('#share_with option[value="' + uid_comment_with + '"]').remove();
        $('#share_with').trigger('liszt:updated');
    },
    dirname: function (path) {
        return path.replace(/\\/g, '/').replace(/\/[^\/]*$/, '');
    }
}

$(document).ready(function () {

    if (typeof FileActions !== 'undefined') {
        OC.Comments.loadIcons();
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
                OC.Comments.icons[item] = OC.imagePath('core', 'actions/share');
                return OC.Comments.icons[item];
            }
        }, function (filename) {
            var file = $('#dir').val() + '/' + filename;
            var appendTo = $('tr').filterAttr('data-file', filename).find('td.filename');
            // Check if drop down is already visible for a different file
            if (($('#dropdown').length > 0)) {
                if (file != $('#dropdown').data('item')) {
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
        if (!($(event.target).hasClass('drop')) && $(event.target).parents().index($('#dropdown')) == -1) {
            if ($('#dropdown').is(':visible')) {
                OC.Comments.hideDropDown(function () {
                    $('tr').removeClass('mouseOver');
                });
            }
        }
    });

    $('#sharedWithList li').live('mouseenter', function (event) {
        // Show permissions and unshare button
        $(':hidden', this).show();
    });

    $('#sharedWithList li').live('mouseleave', function (event) {
        // Hide permissions and unshare button
        $('a', this).hide();
        if (!$('input:[type=checkbox]', this).is(':checked')) {
            $('input:[type=checkbox]', this).hide();
            $('label', this).hide();
        }
    });

    $('#share_with').live('change', function () {
        var item = $('#dropdown').data('item');
        var uid_shared_with = $(this).val();
        var pos = uid_shared_with.indexOf('(group)');
        var isGroup = false;
        if (pos != -1) {
            // Remove '(group)' from uid_shared_with
            uid_shared_with = uid_shared_with.substr(0, pos);
            isGroup = true;
        }
        OC.Comments.invite(item, uid_shared_with, 0, function () {
            if (isGroup) {
                // Reload item because we don't know which users are in the group
                OC.Comments.loadItem(item);
                var users;
                $.each(OC.Comments.itemGroups, function (index, group) {
                    if (group.gid == uid_shared_with) {
                        users = group.users;
                    }
                });
                OC.Comments.addCommentPermissionWith(uid_shared_with, 0, users, false);
            } else {
                OC.Comments.addCommentPermissionWith(uid_shared_with, 0, false, false);
            }
            // Change icon
            if (!OC.Comments.itemPrivateLink) {
                OC.Comments.icons[item] = OC.imagePath('core', 'actions/shared');
            }
        });
    });

    $('.permissions').live('change', function () {
        var permissions = (this.checked) ? 1 : 0;
        OC.Comments.changePermissions($('#dropdown').data('item'), $(this).parent().data('uid_shared_with'), permissions);
    });
});