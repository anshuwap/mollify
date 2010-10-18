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
		$("#button-add-notification").click(that.openAddNotification);
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
		
		$("#types-list").jqGrid({        
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

		$("#available-types-list").jqGrid({        
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
		});

		$("#users-list").jqGrid({        
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
		});
			
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
//			r.time = parseInternalTime(r.time);
			
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
		
		html += that.detailSection("id", "ID", that.detailValue('id', d.id));
		html += that.detailSection("name", "Name", that.detailValue('name', d.name));
		html += that.detailSection("message", "Message", 'foo');
		
		html += "</div>";
		$("#notification-details").html(html);
	}
	
	this.detailSection = function(id, title, html) {
		return $.template("<div id='notification-details-section-${id}' class='notification-details-section'><div class='title'>${title}</div><div class='content'>${html}</div></div>").apply({id:id, title:title, html:html});
	}

	this.detailValue = function(id, value) {
		return $.template("<div class='notification-details-value'>${value}</div>").apply({id:id, value:value});
	}
	
	function timeFormatter(time, options, obj) {
		return formatDateTime(time);
	}
	
	function notNullFormatter(o, options, obj) {
		if (o == null) return '';
		return o;
	}
	
	this.openAddNotification = function() {
		if (!that.addNotificationDialogInit) {
			that.addNotificationDialogInit = true;

			$("#add-notification-dialog").dialog({
				autoOpen: false,
				bgiframe: true,
				height: 'auto',
				width: 350,
				modal: true,
				resizable: false,
				title: "Add Notification",
				buttons: {}
			});
		}
		
		var buttons = {
			Cancel: function() {
				$(this).dialog('close');
			},
			Add: function() {
				var name = $("#notification-name").val();
				if (name.length == 0) return;
				
				addNotification(name, function() {
					$("#add-notification-dialog").dialog('close');
					that.onRefresh();
				}, onServerError);
			}
		}
		$("#add-notification-dialog").dialog('option', 'buttons', buttons);
		
		$("#notification-name").val("");
		
		/*var typesList = $("#types-list");
		typesList.jqGrid('clearGridData');
		
		var availableTypesList = $("#available-types-list");
		availableTypesList.jqGrid('clearGridData');
		
		for (var t in that.types) {
			availableTypesList.jqGrid('addRowData', t, {id: t, name: that.types[t]});
		}

		var usersList = $("#users-list");
		usersList.jqGrid('clearGridData');
		
		var availableUsersList = $("#available-users-list");
		availableUsersList.jqGrid('clearGridData');
		
		for(var i=0;i < that.users.length;i++) {
			var user = that.users[i];
			availableUsersList.jqGrid('addRowData', user.id, user);
		}*/
		
		$("#add-notification-dialog").dialog('open');
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

function removeNotification(id, success, fail) {
	request("DELETE", 'notificator/list/'+id, success, fail, data);
}