<?php
class subviewsView extends View {
	/**
	 * Renders a piece of PHP with provided parameters and returns HTML, XML, or any other string.
	 *
	 * This realizes the concept of Elements, (or "partial layouts")
	 * and the $params array is used to send data to be used in the
	 * Element.
	 *
	 * @link
	 * @param string $name Name of template file in the/app/views/elements/ folder
	 * @param array $params Array of data to be made available to the for rendered view (i.e. the Element)
	 * @return string Rendered output
	 */
	 function renderSubview($name, $params = array(), $loadHelpers = false) {
		$file = $plugin = $key = null;

		if (isset($params['plugin'])) {
			$plugin = $params['plugin'];
		}

		if (isset($this->plugin) && !$plugin) {
			$plugin = $this->plugin;
		}

		$paths = $this->_paths($plugin);

		foreach ($paths as $path) {
			if (file_exists($path . $this->viewPath . DS . $name . $this->ext)) {
				$file = $path . $this->viewPath . DS . $name . $this->ext;
				break;
			} elseif (file_exists($path . $this->viewPath . DS . $name . '.thtml')) {
				$file = $path . $this->viewPath . DS . $name . '.thtml';
				break;
			}
		}

		if (is_file($file)) {
			$params = array_merge_recursive($params, $this->loaded);
			$element = $this->_render($file, array_merge($this->viewVars, $params), $loadHelpers);
			if (isset($params['cache']) && isset($cacheFile) && isset($expires)) {
				cache('views' . DS . $cacheFile, $element, $expires);
			}
			return $element;
		}
		$file = $paths[0] . 'elements' . DS . $name . $this->ext;

		if (Configure::read() > 0) {
			return "Not Found: " . $file;
		}
	}
}
?>