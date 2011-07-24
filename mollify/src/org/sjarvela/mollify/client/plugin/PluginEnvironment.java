/**
 * Copyright (c) 2008- Samuli Järvelä
 *
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html. If redistributing this code,
 * this entire header must remain intact.
 */

package org.sjarvela.mollify.client.plugin;

import java.util.List;

import org.sjarvela.mollify.client.FileView;
import org.sjarvela.mollify.client.filesystem.FileSystemItem;
import org.sjarvela.mollify.client.plugin.filelist.NativeColumnSpec;
import org.sjarvela.mollify.client.ui.common.grid.GridComparator;
import org.sjarvela.mollify.client.ui.common.grid.SortOrder;
import org.sjarvela.mollify.client.ui.fileupload.FileUploadDialogFactory;

import com.google.gwt.core.client.JavaScriptObject;

public interface PluginEnvironment {

	JavaScriptObject getJsEnv(FileView filesystem, String pluginBaseUrl);

	FileUploadDialogFactory getCustomUploader();

	NativeColumnSpec getListColumnSpec(String id);

	GridComparator getListColumnComparator(String columnId, SortOrder sort);

	JavaScriptObject getDataRequest(FileSystemItem i);

	void onPluginsInitialized(List<Plugin> plugins);

}
