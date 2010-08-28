/**
 * Copyright (c) 2008- Samuli Järvelä
 *
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html. If redistributing this code,
 * this entire header must remain intact.
 */

package org.sjarvela.mollify.client.ui.fileitemcontext.filecontext;

import java.util.Arrays;
import java.util.Collections;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import org.sjarvela.mollify.client.ResourceId;
import org.sjarvela.mollify.client.filesystem.File;
import org.sjarvela.mollify.client.filesystem.FileDetails;
import org.sjarvela.mollify.client.filesystem.FileSystemAction;
import org.sjarvela.mollify.client.filesystem.FileSystemItem;
import org.sjarvela.mollify.client.filesystem.handler.FileItemDescriptionHandler;
import org.sjarvela.mollify.client.filesystem.handler.FileSystemActionHandler;
import org.sjarvela.mollify.client.filesystem.handler.FileSystemPermissionHandler;
import org.sjarvela.mollify.client.filesystem.provider.FileDetailsProvider;
import org.sjarvela.mollify.client.js.JsObj;
import org.sjarvela.mollify.client.localization.TextProvider;
import org.sjarvela.mollify.client.service.Callback;
import org.sjarvela.mollify.client.service.ExternalService;
import org.sjarvela.mollify.client.service.ServiceError;
import org.sjarvela.mollify.client.service.request.listener.ResultListener;
import org.sjarvela.mollify.client.session.SessionInfo;
import org.sjarvela.mollify.client.ui.action.ActionListener;
import org.sjarvela.mollify.client.ui.dropbox.DropBox;
import org.sjarvela.mollify.client.ui.fileitemcontext.FileItemContextComponent;
import org.sjarvela.mollify.client.ui.fileitemcontext.FilePreviewListener;
import org.sjarvela.mollify.client.ui.fileitemcontext.ItemContextComponent;
import org.sjarvela.mollify.client.ui.fileitemcontext.ItemContextProvider;

import com.google.gwt.event.logical.shared.CloseEvent;
import com.google.gwt.event.logical.shared.CloseHandler;
import com.google.gwt.i18n.client.DateTimeFormat;
import com.google.gwt.user.client.ui.PopupPanel;

public class FileContextPresenter implements ActionListener,
		FilePreviewListener {
	private final FileItemContextComponent popup;
	private final FileDetailsProvider fileDetailsProvider;
	private final TextProvider textProvider;
	private final DateTimeFormat dateTimeFormat;
	private final SessionInfo session;
	private final ExternalService service;
	private final DropBox dropBox;
	private final ItemContextProvider itemContextProvider;

	private FileSystemActionHandler fileSystemActionHandler;
	private FileSystemPermissionHandler permissionHandler;
	private FileItemDescriptionHandler descriptionHandler;

	private File file = File.Empty;
	private FileDetails details;
	private List<ItemContextComponent> components;
	private boolean previewInitalized = false;

	private enum Details implements ResourceId {
		Accessed, Modified, Changed
	}

	public FileContextPresenter(FileItemContextComponent popup,
			SessionInfo session, FileDetailsProvider fileDetailsProvider,
			TextProvider textProvider, ExternalService service,
			DropBox dropBox, ItemContextProvider itemDetailsProvider) {
		this.popup = popup;
		this.session = session;
		this.fileDetailsProvider = fileDetailsProvider;
		this.textProvider = textProvider;
		this.service = service;
		this.dropBox = dropBox;
		this.itemContextProvider = itemDetailsProvider;
		this.dateTimeFormat = com.google.gwt.i18n.client.DateTimeFormat
				.getFormat(textProvider.getStrings().shortDateTimeFormat());

		popup.addPreviewListener(this);
		popup.addCloseHandler(new CloseHandler<PopupPanel>() {
			@Override
			public void onClose(CloseEvent<PopupPanel> event) {
				for (ItemContextComponent c : components)
					c.onDispose();
			}
		});
		initializeDetails();
	}

	private void initializeDetails() {
		List<ResourceId> order = (List<ResourceId>) Arrays.asList(
				(ResourceId) Details.Modified, (ResourceId) Details.Changed,
				(ResourceId) Details.Accessed);
		Map<ResourceId, String> headers = new HashMap();
		headers.put(Details.Accessed, textProvider.getStrings()
				.fileDetailsLabelLastAccessed());
		headers.put(Details.Changed, textProvider.getStrings()
				.fileDetailsLabelLastChanged());
		headers.put(Details.Modified, textProvider.getStrings()
				.fileDetailsLabelLastModified());

		this.popup.initializeDetailsSection(order, headers);
	}

	public void setFileActionHandler(FileSystemActionHandler actionHandler) {
		this.fileSystemActionHandler = actionHandler;
	}

	public void setFileItemDescriptionHandler(
			FileItemDescriptionHandler descriptionHandler) {
		this.descriptionHandler = descriptionHandler;
	}

	public void setPermissionHandler(
			FileSystemPermissionHandler permissionHandler) {
		this.permissionHandler = permissionHandler;
	}

	public File getFile() {
		return file;
	}

	public void setFile(File file) {
		this.file = file;

		popup.getDetails().setOpen(false);
		popup.getName().setText(file.getName());
		updateDetails(null);

		fileDetailsProvider.getFileDetails(file,
				new ResultListener<FileDetails>() {
					public void onFail(ServiceError error) {
						popup.getDescription().setText(
								error.getType().getMessage(textProvider));
					}

					public void onSuccess(FileDetails details) {
						updateDetails(details);
					}
				});
	}

	private void updateDetails(FileDetails details) {
		this.previewInitalized = false;
		this.popup.reset();

		this.components = Collections.EMPTY_LIST;
		if (details != null) {
			components = popup.createComponents(itemContextProvider
					.getItemContext(file));
		}

		this.details = details;
		this.updateDescription();

		if (details != null) {
			this.popup.setDetailValue(Details.Accessed, dateTimeFormat
					.format(details.getLastAccessed()));
			this.popup.setDetailValue(Details.Modified, dateTimeFormat
					.format(details.getLastModified()));
			this.popup.setDetailValue(Details.Changed, dateTimeFormat
					.format(details.getLastChanged()));
		}

		boolean writable = (details == null ? false : details
				.getFilePermission().canWrite());
		boolean isPreview = session.getFeatures().filePreview()
				&& details != null && details.getFilePreview() != null;
		boolean isView = session.getFeatures().fileView() && details != null
				&& details.getFileView() != null;

		popup.update(writable, isPreview, isView);

		for (ItemContextComponent c : components)
			c.onInit(file, details);
	}

	private void updateDescription() {
		boolean descriptionDefined = isDescriptionDefined();
		String visibleDescription = descriptionDefined ? details
				.getDescription() : "";

		popup.setDescription(visibleDescription);
		popup.setDescriptionEditable(false, descriptionDefined);
	}

	private boolean isDescriptionDefined() {
		return (details != null && details.getDescription() != null);
	}

	protected void onStartEditDescription() {
		popup.setDescriptionEditable(true, isDescriptionDefined());
	}

	protected void onApplyDescription() {
		final String description = popup.getDescription().getText();
		if (!this.descriptionHandler.validateDescription(description))
			return;

		popup.setDescriptionEditable(false, isDescriptionDefined());

		this.descriptionHandler.setItemDescription(file, description,
				new Callback() {
					public void onCallback() {
						details.setDescription(description);
						updateDescription();
					}
				});
	}

	protected void onCancelEditDescription() {
		popup.setDescriptionEditable(false, isDescriptionDefined());
		updateDescription();
	}

	protected void onRemoveDescription() {
		this.descriptionHandler.removeItemDescription(file, new Callback() {
			public void onCallback() {
				details.removeDescription();
				updateDescription();
			}
		});
	}

	public void onAction(ResourceId action, Object o) {
		if (FileSystemAction.class.equals(action.getClass())) {
			Object param = null;
			if (action.equals(FileSystemAction.view))
				param = details.getFileView();
			fileSystemActionHandler.onAction(file, (FileSystemAction) action,
					popup, param);
			popup.hide();
			return;
		}

		if (FileItemContextComponent.Action.addToDropbox.equals(action))
			onAddToDropbox();
		else if (FileItemContextComponent.Action.addDescription.equals(action))
			onStartEditDescription();
		else if (FileItemContextComponent.Action.editDescription.equals(action))
			onStartEditDescription();
		else if (FileItemContextComponent.Action.cancelEditDescription
				.equals(action))
			onCancelEditDescription();
		else if (FileItemContextComponent.Action.applyDescription
				.equals(action))
			onApplyDescription();
		else if (FileItemContextComponent.Action.removeDescription
				.equals(action))
			onRemoveDescription();
		else if (FileItemContextComponent.Action.editPermissions.equals(action)) {
			popup.hide();
			permissionHandler.onEditPermissions(file);
		}
	}

	private void onAddToDropbox() {
		dropBox.addItems(Arrays.asList((FileSystemItem) file));
	}

	@Override
	public void onPreview() {
		if (!session.getFeatures().filePreview() || previewInitalized)
			return;

		previewInitalized = true;
		service.get(details.getFilePreview(), new ResultListener<JsObj>() {
			@Override
			public void onFail(ServiceError error) {
				popup.setFilePreview(error.getType().getMessage(textProvider));
			}

			@Override
			public void onSuccess(JsObj result) {
				popup.setFilePreview(result.getString("html"));
			}
		});
	}
}