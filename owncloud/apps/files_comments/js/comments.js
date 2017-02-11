OC.Comments={
	icons:[],
	itemUsers:[],
	itemGroups:[],
	itemPrivateLink:false,
	usersAndGroups:[],
	loadIcons:function() {
		// Cache all icons for shared files
		$.getJSON(OC.filePath('files_sharing', 'ajax', 'getstatuses.php'), function(result) {
			if (result && result.status === 'success') {
				$.each(result.data, function(item, hasPrivateLink) {
					if (hasPrivateLink) {
						OC.Comments.icons[item] = OC.imagePath('core', 'actions/public');
					} else {
						OC.Comments.icons[item] = OC.imagePath('core', 'actions/shared');
					}
				});
			}
		});
	},
	loadItem:function(item) {
		$.ajax({type: 'GET', url: OC.filePath('files_sharing', 'ajax', 'getitem.php'), data: { item: item }, async: false, success: function(result) {
			if (result && result.status === 'success') {
				var item = result.data;
				OC.Comments.itemUsers = item.users;
				OC.Comments.itemGroups = item.groups;
				OC.Comments.itemPrivateLink = item.privateLink;
			}
		}});
	},
	share:function(source, uid_shared_with, permissions, callback) {
		$.post(OC.filePath('files_sharing', 'ajax', 'share.php'), { sources: source, uid_shared_with: uid_shared_with, permissions: permissions }, function(result) {
			if (result && result.status === 'success') {
				if (callback) {
					callback(result.data);
				}
			} else {
				OC.dialogs.alert(result.data.message, 'Error while sharing');
			}
		});
	},
	unshare:function(source, uid_shared_with, callback) {
		$.post(OC.filePath('files_sharing', 'ajax', 'unshare.php'), { source: source, uid_shared_with: uid_shared_with }, function(result) {
			if (result && result.status === 'success') {
				if (callback) {
					callback();
				}
			} else {
				OC.dialogs.alert('Error', 'Error while unsharing');
			}
		});
	},
	changePermissions:function(source, uid_shared_with, permissions) {
		$.post(OC.filePath('files_sharing','ajax','setpermissions.php'), { source: source, uid_shared_with: uid_shared_with, permissions: permissions }, function(result) {
			if (!result || result.status !== 'success') {
				OC.dialogs.alert('Error', 'Error while changing permissions');
			}
		});
	},
	showDropDown:function(item, appendTo) {
		OC.Comments.loadItem(item);
		var html = '<div id="dropdown" class="drop" data-item="'+item+'">';
		html += '<select data-placeholder="User or Group" id="share_with" class="chzen-select">';
		html += '<option value=""></option>';
		html += '</select>';
		html += '<div id="sharedWithList">';
		html += '<ul id="userList"></ul>';
		html += '<div id="groups" style="display:none;">';
		html += '<br />';
		html += 'Groups: ';
		html += '<ul id="groupList"></ul>';
		html += '</div>';
		html += '</div>';
		$(html).appendTo(appendTo);
		if (OC.Comments.usersAndGroups.length < 1) {
			$.ajax({type: 'GET', url: OC.filePath('files_sharing', 'ajax', 'userautocomplete.php'), async: false, success: function(users) {
				if (users) {
					OC.Comments.usersAndGroups = users;
					$.each(users, function(index, user) {
						$(user).appendTo('#share_with');
					});
					$('#share_with').trigger('liszt:updated');
				}
			}});
		} else {
			$.each(OC.Comments.usersAndGroups, function(index, user) {
				$(user).appendTo('#share_with');
			});
			$('#share_with').trigger('liszt:updated');
		}
		if (OC.Comments.itemUsers) {
			$.each(OC.Comments.itemUsers, function(index, user) {
				if (user.parentFolder) {
					OC.Comments.addSharedWith(user.uid, user.permissions, false, user.parentFolder);
				} else {
					OC.Comments.addSharedWith(user.uid, user.permissions, false, false);
				}
			});
		}
		if (OC.Comments.itemGroups) {
			$.each(OC.Comments.itemGroups, function(index, group) {
				if (group.parentFolder) {
					OC.Comments.addSharedWith(group.gid, group.permissions, group.users, group.parentFolder);
				} else {
					OC.Comments.addSharedWith(group.gid, group.permissions, group.users, false);
				}
			});
		}
		$('#dropdown').show('blind');
		$('#share_with').chosen();
	},
	hideDropDown:function(callback) {
		$('#dropdown').hide('blind', function() {
			$('#dropdown').remove();
			if (callback) {
				callback.call();
			}
		});
	},
	addSharedWith:function(uid_shared_with, permissions, isGroup, parentFolder) {
		if (parentFolder) {
			var sharedWith = '<li>Parent folder '+parentFolder+' shared with '+uid_shared_with+'</li>';
		} else {
			var checked = ((permissions > 0) ? 'checked="checked"' : 'style="display:none;"');
			var style = ((permissions == 0) ? 'style="display:none;"' : '');
			var sharedWith = '<li data-uid_shared_with="'+uid_shared_with+'">';
			sharedWith += '<a href="" class="unshare" style="display:none;"><img class="svg" alt="Unshare" src="'+OC.imagePath('core','actions/delete')+'"/></a>';
			sharedWith += uid_shared_with;
			sharedWith += '<input type="checkbox" name="permissions" id="'+uid_shared_with+'" class="permissions" '+checked+' />';
			sharedWith += '<label class="edit" for="'+uid_shared_with+'" '+style+'>can edit</label>';
			sharedWith += '</li>';
		}
		if (isGroup) {
			// Groups are added to a different list
			$('#groups').show();
			$(sharedWith).appendTo('#groupList');
			// Remove group from select form
			$('#share_with option[value="'+uid_shared_with+'(group)"]').remove();
			$('#share_with').trigger('liszt:updated');
			// Remove users in group from select form
			$.each(isGroup, function(index, user) {
				$('#share_with option[value="'+user+'"]').remove();
				$('#share_with').trigger('liszt:updated');
			});
		} else {
			$(sharedWith).appendTo('#userList');
			// Remove user from select form
			$('#share_with option[value="'+uid_shared_with+'"]').remove();
			$('#share_with').trigger('liszt:updated');
		}
		
	},
	removeSharedWith:function(uid_shared_with) {
		var option;
		if ($('#userList li[data-uid_shared_with="'+uid_shared_with+'"]').length > 0) {
			$('#userList li[data-uid_shared_with="'+uid_shared_with+'"]').remove();
			option = '<option value="'+uid_shared_with+'">'+uid_shared_with+'</option>';
		} else if ($('#groupList li[data-uid_shared_with="'+uid_shared_with+'"]').length > 0) {
			$('#groupList li[data-uid_shared_with="'+uid_shared_with+'"]').remove();
			if ($('#groupList li').length < 1) {
				$('#groups').hide();
			}
			option = '<option value="'+uid_shared_with+'(group)">'+uid_shared_with+' (group)</option>';
		}
		$(option).appendTo('#share_with');
		$('#share_with').trigger('liszt:updated');
	},
	showPrivateLink:function(item, token) {
		$('#privateLinkCheckbox').attr('checked', true);
		var link = parent.location.protocol+'//'+location.host+OC.linkTo('', 'public.php')+'?service=files&token='+token;
		if (token.indexOf('&path=') == -1) {
			link += '&file=' + encodeURIComponent(item).replace(/%2F/g, '/');
		} else {
			// Disable checkbox if inside a shared parent folder
			$('#privateLinkCheckbox').attr('disabled', 'true');
		}
		$('#privateLinkText').val(link);
		$('#privateLinkText').show('blind', function() {
			$('#privateLinkText').after('<br id="emailBreak" />');
			$('#email').show();
			$('#emailButton').show();
		});
	},
	hidePrivateLink:function() {
		$('#privateLinkText').hide('blind');
		$('#emailBreak').remove();
		$('#email').hide();
		$('#emailButton').hide();
	},
	emailPrivateLink:function() {
		var link = $('#privateLinkText').val();
		var file = link.substr(link.lastIndexOf('/') + 1).replace(/%20/g, ' ');
		$.post(OC.filePath('files_sharing', 'ajax', 'email.php'), { toaddress: $('#email').val(), link: link, file: file } );
		$('#email').css('font-weight', 'bold');
		$('#email').animate({ fontWeight: 'normal' }, 2000, function() {
			$(this).val('');
		}).val('Email sent');
	},
	dirname:function(path) {
		return path.replace(/\\/g,'/').replace(/\/[^\/]*$/, '');
	}
}

$(document).ready(function() {

	if (typeof FileActions !== 'undefined') {
		OC.Comments.loadIcons();
		FileActions.register('all', 'Invite to comment', function(filename) {
			// Return the correct sharing icon
			if (scanFiles.scanning) { return; } // workaround to prevent additional http request block scanning feedback
			var item =  $('#dir').val() + '/' + filename;
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
		}, function(filename) {
			var file = $('#dir').val() + '/' + filename;
			var appendTo = $('tr').filterAttr('data-file',filename).find('td.filename');
			// Check if drop down is already visible for a different file
			if (($('#dropdown').length > 0)) {
				if (file != $('#dropdown').data('item')) {
					OC.Comments.hideDropDown(function () {
						$('tr').removeClass('mouseOver');
						$('tr').filterAttr('data-file',filename).addClass('mouseOver');
						OC.Comments.showDropDown(file, appendTo);
					});
				}
			} else {
				$('tr').filterAttr('data-file',filename).addClass('mouseOver');
				OC.Comments.showDropDown(file, appendTo);
			}
		});
	};
	
	$(this).click(function(event) {
		if (!($(event.target).hasClass('drop')) && $(event.target).parents().index($('#dropdown')) == -1) {
			if ($('#dropdown').is(':visible')) {
				OC.Comments.hideDropDown(function() {
					$('tr').removeClass('mouseOver');
				});
			}
		}
	});

	$('#sharedWithList li').live('mouseenter', function(event) {
		// Show permissions and unshare button
		$(':hidden', this).show();
	});
	
	$('#sharedWithList li').live('mouseleave', function(event) {
		// Hide permissions and unshare button
		$('a', this).hide();
		if (!$('input:[type=checkbox]', this).is(':checked')) {
			$('input:[type=checkbox]', this).hide();
			$('label', this).hide();
		}
	});
	
	$('#share_with').live('change', function() {
		var item = $('#dropdown').data('item');
		var uid_shared_with = $(this).val();
		var pos = uid_shared_with.indexOf('(group)');
		var isGroup = false;
		if (pos != -1) {
			// Remove '(group)' from uid_shared_with
			uid_shared_with = uid_shared_with.substr(0, pos);
			isGroup = true;
		}
		OC.Comments.share(item, uid_shared_with, 0, function() {
			if (isGroup) {
				// Reload item because we don't know which users are in the group
				OC.Comments.loadItem(item);
				var users;
				$.each(OC.Comments.itemGroups, function(index, group) {
					if (group.gid == uid_shared_with) {
						users = group.users;
					}
				});
				OC.Comments.addSharedWith(uid_shared_with, 0, users, false);
			} else {
				OC.Comments.addSharedWith(uid_shared_with, 0, false, false);
			}
			// Change icon
			if (!OC.Comments.itemPrivateLink) {
				OC.Comments.icons[item] = OC.imagePath('core', 'actions/shared');
			}
		});
	});
	
	$('.unshare').live('click', function() {
		var item = $('#dropdown').data('item');
		var uid_shared_with = $(this).parent().data('uid_shared_with');
		OC.Comments.unshare(item, uid_shared_with, function() {
			OC.Comments.removeSharedWith(uid_shared_with);
			// Reload item to update cached users and groups for the icon check
			OC.Comments.loadItem(item);
			// Change icon
			if (!OC.Comments.itemPrivateLink && !OC.Comments.itemUsers && !OC.Comments.itemGroups) {
				OC.Comments.icons[item] = OC.imagePath('core', 'actions/share');
			}
		});
	});
	
	$('.permissions').live('change', function() {
		var permissions = (this.checked) ? 1 : 0;
		OC.Comments.changePermissions($('#dropdown').data('item'), $(this).parent().data('uid_shared_with'), permissions);
	});
	
	$('#privateLinkCheckbox').live('change', function() {
		var item = $('#dropdown').data('item');
		if (this.checked) {
			// Create a private link
			OC.Comments.share(item, 'public', 0, function(token) {
				OC.Comments.showPrivateLink(item, token);
				// Change icon
				OC.Comments.icons[item] = OC.imagePath('core', 'actions/public');
			});
		} else {
			// Delete private link
			OC.Comments.unshare(item, 'public', function() {
				OC.Comments.hidePrivateLink();
				// Change icon
				if (OC.Comments.itemUsers || OC.Comments.itemGroups) {
					OC.Comments.icons[item] = OC.imagePath('core', 'actions/shared');
				} else {
					OC.Comments.icons[item] = OC.imagePath('core', 'actions/share');
				}
			});
		}
	});
	
	$('#privateLinkText').live('click', function() {
		$(this).focus();
		$(this).select();
	});

	$('#emailPrivateLink').live('submit', function() {
		OC.Comments.emailPrivateLink();
	});
});