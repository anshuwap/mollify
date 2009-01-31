/**
 * Copyright (c) 2008- Samuli Järvelä
 *
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html. If redistributing this code,
 * this entire header must remain intact.
 */

package org.sjarvela.mollify.client.ui.mainview;

import org.sjarvela.mollify.client.data.Directory;
import org.sjarvela.mollify.client.data.FileSystemItem;
import org.sjarvela.mollify.client.ui.ActionDelegator;
import org.sjarvela.mollify.client.ui.ActionHandler;
import org.sjarvela.mollify.client.ui.ViewListener;
import org.sjarvela.mollify.client.ui.filelist.Column;
import org.sjarvela.mollify.client.ui.filelist.FileListListener;
import org.sjarvela.mollify.client.ui.mainview.MainView.Action;

public class MainViewGlue implements FileListListener, ViewListener {
	private final MainViewPresenter presenter;
	private final ActionDelegator actionDelegator;
	private final MainView view;

	public MainViewGlue(MainView view, final MainViewPresenter presenter,
			ActionDelegator actionDelegator) {
		this.view = view;
		this.presenter = presenter;
		this.actionDelegator = actionDelegator;

		view.addFileListListener(this);
		view.addViewListener(this);

		initializeActions();
	}

	public void onViewLoad() {
		presenter.initialize();
	}

	private void initializeActions() {
		actionDelegator.setActionHandler(Action.logout, new ActionHandler() {
			public void onAction() {
				presenter.logout();
			}
		});

		actionDelegator.setActionHandler(Action.refresh, new ActionHandler() {
			public void onAction() {
				presenter.reload();
			}
		});

		actionDelegator.setActionHandler(Action.parentDir, new ActionHandler() {
			public void onAction() {
				presenter.moveToParentDirectory();
			}
		});

		actionDelegator.setActionHandler(Action.addFile, new ActionHandler() {
			public void onAction() {
				presenter.openUploadDialog();
			}
		});

		actionDelegator.setActionHandler(Action.addDirectory,
				new ActionHandler() {
					public void onAction() {
						presenter.openNewDirectoryDialog();
					}
				});
	}

	public void onRowClicked(FileSystemItem item, Column column) {
		presenter.onFileSystemItemSelected(item, column);
	}

	public void onIconClicked(FileSystemItem item) {
		if (item.isFile())
			return;
		view.showDirectoryContext((Directory) item);
	}
}
