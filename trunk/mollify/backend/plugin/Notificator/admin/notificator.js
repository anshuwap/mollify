/**
 * Copyright (c) 2008- Samuli Järvelä
 *
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html. If redistributing this code,
 * this entire header must remain intact.
 */

function NotificatorListView() {
	var that = this;
	this.pageUrl = "notificator.html";
	this.list = null;
	
	this.onLoadView = function() {
		$("#button-add-notification").click(that.addNotification);
		$("#button-remove-notification").click(that.onRemoveNotification);
		$("#button-refresh").click(that.onRefresh);

		$("#notifications-list").jqGrid({        
			datatype: "local",
			multiselect: false,
			autowidth: true,
			height: '100%',
		   	colNames:['ID', 'Name'],
		   	colModel:[
			   	{name:'id',index:'id', width:60, sortable:true, sorttype:"int"},
			   	{name:'name',index:'name',width:150, sortable:true},
		   	],
		   	sortname:'id',
		   	sortorder:'desc',
			onSelectRow: function(id){
				that.onNotificationSelectionChanged();
			}
		});
		
/*		$("#users-list").jqGrid({        
			datatype: "local",
			multiselect: true,
			autowidth: true,
			height: '150px',
		   	colNames:['Selected'],
		   	colModel:[
			   	{name:'name',index:'name',width:250, sortable:true},
		   	],
		   	sortname:'name',
		   	sortorder:'desc'
		});

		$("#available-users-list").jqGrid({        
			datatype: "local",
			multiselect: true,
			autowidth: true,
			height: '150px',
		   	colNames:['Available'],
		   	colModel:[
			   	{name:'name',index:'name',width:250, sortable:true},
		   	],
		   	sortname:'name',
		   	sortorder:'desc'
		});*/
			
		getEventTypes(that.refreshTypes, onServerError);
	}
	
	this.refreshTypes = function(types) {
		that.types = types;
		getUsers(that.refreshUsers, onServerError);
	}
	
	this.refreshUsers = function(users) {
		that.users = users;		
		that.onRefresh();
	}
	
	this.onRefresh = function() {
		getNotifications(that.refreshList, onServerError);
	}

	function getEventTypes(success, fail) {
		request("POST", 'events/types', success, fail);
	}
	
	this.refreshList = function(list) {
		that.list = list;
		that.notificationsById = {}
		
		var grid = $("#notifications-list");
		grid.jqGrid('clearGridData');
		
		for (var i=0; i < list.length; i++) {
			var r = list[i];
			
			that.notificationsById[r.id] = r;
			grid.jqGrid('addRowData', r.id, r);
		}
		that.onNotificationSelectionChanged();
	}
	
	this.getSelectedNotification = function() {
		return $("#notifications-list").getGridParam("selrow");
	}

	this.getNotification = function(id) {
		return that.list[id];
	}
	
	this.onNotificationSelectionChanged = function() {
		var n = that.getSelectedNotification();
		var selected = (n != null);
		enableButton("button-remove-notification", selected);
		that.editing = null;
		
		if (!n) {	
			$("#notification-details").html("Select notification from the list");
			if (that.list.length == 0) {
				$("#notification-details").html('<div class="message">Click "Add Notification" to create a new notification</div>');
			} else {
				$("#notification-details").html('<div class="message">Select a notification from the list to view details</div>');
			}
			return;
		}

		getNotificationDetails(n, that.showNotificationDetails, onServerError);
	}
	
	this.showNotificationDetails = function(d) {
		that.editing = d;
		
		var html = $.template("<div id='notification-details-info' class='details-info'><h1>Notification ${id}</h1></div>").apply(d);
		html += "<div id='notification-details-data' class='details-data'>";
		
		html += that.detailSection("name", "Name", that.detailValue('name', d.name), "edit-name");
		html += that.detailSection("event", "Events", that.eventsSection(d), "edit-events");
		html += that.detailSection("message", "Message", that.messageSection(d), "edit-message");
		html += that.detailSection("recipient", "Recipients", that.recipientsSection(d), "edit-recipients");
		
		html += "</div>";
		
		$("#notification-details").html(html);
		
		$("#edit-name").click(that.onEditName);
		$("#edit-message").click(that.onEditMessage);
		$("#edit-events").click(that.onEditEvents);
		
		if (d.events.length > 0) that.showEvents(d);
	}
	
	this.onEditName = function() {
		that.addEditNotification(that.editing.id, that.editing.name);
	}

	this.onEditMessage = function() {
		that.editNotificationMessage(that.editing.id, that.editing.message_title, that.editing.message);
	}

	this.onEditEvents = function() {
		that.editNotificationEvents(that.editing.id, that.editing.events);
	}
	
	this.detailSection = function(id, title, html, editId) {
		if (editId) {
			return $.template("<div id='notification-details-section-${id}' class='notification-details-section'><div class='title'>${title}<a id='${editId}'>Edit</a></div><div class='content'>${html}</div></div>").apply({id:id, title:title, html:html, editId:editId});
		}
		return $.template("<div id='notification-details-section-${id}' class='notification-details-section'><div class='title'>${title}</div><div class='content'>${html}</div></div>").apply({id:id, title:title, html:html});
	}

	this.detailValue = function(id, value) {
		return $.template("<div class='notification-details-value'>${value}</div>").apply({id:id, value:value});
	}
	
	this.messageSection = function(d) {
		var v = {
			title: (d.message_title != null && d.message_title.length > 0) ? d.message_title : '<i>No title</i>',
			message: (d.message != null && d.message.length > 0) ? d.message : '<i>No message</i>',
		}
		return $.template("<div class='notification-details-value notification-message'><p><div class='title'>${title}</div></p><p>${message}</p></div>").apply(v);
	}

	this.eventsSection = function(d) {
		if (d.events.length == 0) return "<div class='notification-details-value notification-events'><i>Any event</i></div>";
		return "<div class='notification-details-value notification-events'><table id='notification-event-list'/></div>";
	}
	
	this.showEvents = function(d) {
		$("#notification-event-list").jqGrid({        
			datatype: "local",
			width: 250,
		   	colNames:['Event'],
		   	colModel:[
			   	{name:'name',index:'name',width:250, sortable:true},
		   	],
		   	sortname:'name',
		   	sortorder:'desc'
		});
			
		var list = $("#notification-event-list");
		list.jqGrid('clearGridData');
		
		for (var i=0; i < d.events.length; i++) {
			var t = d.events[i];
			var o = {"id": t, "name": that.types[t]};
			list.jqGrid('addRowData', t, o);
		}
	}

	this.recipientsSection = function(d) {
		if (d.recipients.length == 0) return "<div class='notification-details-value notification-recipients'><i>No recipients</i></div>";
		return "<div class='notification-details-value notification-recipients'><table id='notification-recipient-list'/></div>";
	}
	
	function timeFormatter(time, options, obj) {
		return formatDateTime(time);
	}
	
	function notNullFormatter(o, options, obj) {
		if (o == null) return '';
		return o;
	}
	
	this.addNotification = function() {
		that.addEditNotification(null, null);
	}
	
	this.addEditNotification = function(id, name) {
		if (!that.addEditNotificationDialogInit) {
			that.addEditNotificationDialogInit = true;

			$("#add-edit-notification-dialog").dialog({
				autoOpen: false,
				bgiframe: true,
				height: 'auto',
				width: 350,
				modal: true,
				resizable: false,
				title: "",
				buttons: {}
			});
		}
		
		var cb = function() {
				var newName = $("#notification-name").val();
				if (newName.length == 0) return;
				
				if (id) {
					editNotification(id, {name: newName}, function() {
						$("#add-edit-notification-dialog").dialog('close');
						that.onNotificationSelectionChanged();
					}, onServerError);
				} else {
					addNotification(newName, function() {
						$("#add-edit-notification-dialog").dialog('close');
						that.onRefresh();
					}, onServerError);
				}
			};
		
		var buttons = {
			Cancel: function() {
				$(this).dialog('close');
			}
		}
		if (id) {
			buttons.Edit = cb;
			$("#add-edit-notification-dialog").dialog('option', 'title', "Edit Notification Name");
		} else {
			buttons.Add = cb;
			$("#add-edit-notification-dialog").dialog('option', 'title', "Add Notification");
		}
		
		$("#add-edit-notification-dialog").dialog('option', 'buttons', buttons);
		$("#notification-name").val(name == null ? "" : name);
		$("#add-edit-notification-dialog").dialog('open');
	}
	
	this.editNotificationMessage = function(id, title, message) {
		if (!that.editNotificationMessageDialogInit) {
			that.editNotificationMessageDialogInit = true;

			$("#edit-notification-message-dialog").dialog({
				autoOpen: false,
				bgiframe: true,
				height: 'auto',
				width: 350,
				modal: true,
				resizable: false,
				title: "Edit Notification Message",
				buttons: {}
			});
		}
		
		var buttons = {
			Cancel: function() {
				$(this).dialog('close');
			},
			Edit: function() {
				var newTitle = $("#notification-message-title").val();
				if (newTitle.length == 0) return;
				var newMessage = $("#notification-message").val();
				if (newMessage.length == 0) return;
				
				editNotification(id, {message_title: newTitle, message: newMessage}, function() {
					$("#edit-notification-message-dialog").dialog('close');
					that.onNotificationSelectionChanged();
				}, onServerError);
			}
		}
		
		$("#edit-notification-message-dialog").dialog('option', 'buttons', buttons);
		$("#notification-message-title").val(title);
		$("#notification-message").val(message);
		$("#edit-notification-message-dialog").dialog('open');
	}

	this.editNotificationEvents = function(id, events) {
		if (!that.editNotificationEventsDialogInit) {
			that.editNotificationEventsDialogInit = true;

			$("#notification-events-list").jqGrid({        
				datatype: "local",
				multiselect: true,
				autowidth: true,
				height: '150px',
			   	colNames:['Selected'],
			   	colModel:[
				   	{name:'name',index:'name',width:250, sortable:true},
			   	],
			   	sortname:'name',
			   	sortorder:'desc'
			});
	
			$("#notification-events-available-list").jqGrid({        
				datatype: "local",
				autowidth: true,
				multiselect: true,
			   	colNames:['Available'],
			   	colModel:[
				   	{name:'name',index:'name',width:250, sortable:true},
			   	],
			   	sortname:'name',
			   	sortorder:'desc'
			});
		
			$("#edit-notification-events-dialog").dialog({
				autoOpen: false,
				bgiframe: true,
				height: 'auto',
				width: 600,
				modal: true,
				resizable: true,
				title: "Events",
				buttons: {}
			});
		}
		
		var buttons = {
			OK: function() {
				var ids = $("#notification-events-list").jqGrid('getDataIDs');
				
				editNotification(id, {events: ids}, function() {
					$("#edit-notification-events-dialog").dialog('close');
					that.onNotificationSelectionChanged();
				}, onServerError);
			},
			Cancel: function() {
				$(this).dialog('close');
			}
		}
		
		var addRemoveEvent = function(ids, remove) {
			var selected = $("#notification-events-list");
			var available = $("#notification-events-available-list");

			for (var i=0; i < ids.length; i++) {
				var t = ids[i];
				
				if (remove) {
					selected.jqGrid('delRowData', t);
				} else {
					if (!inArray(t, selected.jqGrid('getDataIDs')))
						selected.jqGrid('addRowData', t, {"id": t, "name": that.types[t]});
				}
			}
		}
		
		$("#button-add-notification-event").click(function(){
			var sel = $("#notification-events-available-list").getGridParam("selarrrow");
			if (sel.length < 1) return;
			addRemoveEvent(sel, false);
		});

		$("#button-remove-notification-event").click(function(){
			var sel = $("#notification-events-list").getGridParam("selarrrow");
			if (sel.length < 1) return;
			addRemoveEvent(sel, true);
		});
		
		$("#edit-notification-events-dialog").dialog('option', 'buttons', buttons);
		
		var selected = $("#notification-events-list");
		selected.jqGrid('clearGridData');
		
		var available = $("#notification-events-available-list");
		available.jqGrid('clearGridData');
		
		for (var t in that.types) {
			var o = {"id": t, "name": that.types[t]};

			if (inArray(t,events))
				selected.jqGrid('addRowData', t, o);
			else
				available.jqGrid('addRowData', t, o);
		}
		$("#edit-notification-events-dialog").dialog('open');
	}
	
	this.onRemoveNotification = function() {
		var id = that.getSelectedNotification();
		if (id == null) return;
		removeNotification(id, that.refresh, onServerError);
	}
}

function getNotifications(success, fail) {
	request("GET", 'notificator/list/', success, fail);
}

function getNotificationDetails(id, success, fail) {
	request("GET", 'notificator/list/'+id, success, fail);
}

function addNotification(name, success, fail) {
	var data = JSON.stringify({name:name});
	request("POST", 'notificator/list/', success, fail, data);
}

function editNotification(id, prop, success, fail) {
	var data = JSON.stringify(prop);
	request("PUT", 'notificator/list/'+id, success, fail, data);
}

function removeNotification(id, success, fail) {
	request("DELETE", 'notificator/list/'+id, success, fail, data);
}