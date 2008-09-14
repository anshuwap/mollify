/**
 * Copyright (c) 2008- Samuli Järvelä
 *
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html. If redistributing this code,
 * this entire header must remain intact.
 */

package org.sjarvela.mollify.client.ui.filelist;

import org.sjarvela.mollify.client.data.Directory;
import org.sjarvela.mollify.client.data.File;

public interface SimpleFileListListener {
	void onFileRowClicked(File file, Column column);

	void onDirectoryRowClicked(Directory directory, Column column);

	void onDirectoryUpRowClicked(Column column);
}
