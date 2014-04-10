<?php
namespace Tx\CzSimpleCal\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Christian Zenker <christian.zenker@599media.de>, 599media GmbH
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * A file
 */
class File {

	/**
	 * the property file
	 *
	 * @var string file
	 */
	protected $file;

	/**
	 * Public path and filename of the file.
	 *
	 * @var string
	 */
	protected $pathAndFilename = NULL;

	/**
	 * getter for file
	 *
	 * @return string
	 */
	public function getFile() {
		return $this->file;
	}

	/**
	 * setter for file
	 *
	 * @param string $file
	 * @return File
	 */
	public function setFile($file) {
		$this->file = $file;
		return $this;
	}

	/**
	 * the property path
	 *
	 * @var string path
	 */
	protected $path;

	/**
	 * getter for path
	 *
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * setter for path
	 *
	 * @param string $path
	 * @return File
	 */
	public function setPath($path) {
		if($path && substr($path, -1) !== '/') {
			$path = $path.'/';
		}
		$this->path = $path;
		return $this;
	}

	/**
	 * get the full path to the file
	 *
	 * @return string
	 */
	public function getFilePath() {
		if (isset($this->pathAndFilename)) {
			$path = $this->pathAndFilename;
		} else {
			$path = $this->path . $this->file;
		}
		return $path;
	}

	/**
	 * the property alternateText
	 *
	 * @var string alternateText
	 */
	protected $alternateText;

	/**
	 * getter for alternateText
	 *
	 * @return string
	 */
	public function getAlternateText() {
		return $this->alternateText;
	}

	/**
	 * setter for alternateText
	 *
	 * @param string $alternateText
	 * @return File
	 */
	public function setAlternateText($alternateText) {
		$this->alternateText = $alternateText;
		return $this;
	}

	/**
	 * the property caption
	 *
	 * @var string caption
	 */
	protected $caption;

	/**
	 * getter for caption
	 *
	 * @return string
	 */
	public function getCaption() {
		return $this->caption;
	}

	/**
	 * setter for caption
	 *
	 * @param string $caption
	 * @return File
	 */
	public function setCaption($caption) {
		$this->caption = $caption;
		return $this;
	}

	/**
	 * If the path and filename is set it will not be generated
	 * automatically by concatenating the path and the filename.
	 *
	 * @param string $pathAndFilename
	 */
	public function setPathAndFilename($pathAndFilename) {
		$this->pathAndFilename = $pathAndFilename;
	}
}
?>