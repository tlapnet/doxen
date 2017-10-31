<?php

namespace Tlapnet\Doxen;


use Nette\InvalidArgumentException;
use Nette\Utils\Image;
use Nette\Utils\Strings;
use Tlapnet\Doxen\DocumentationMiner\IDocumentationMiner;

class Doxen
{


	/**
	 * @var \Psr\Log\LoggerInterface
	 */
	private $logger;

	/**
	 * @var IDocumentationMiner
	 */
	private $documentationMiner;

	/**
	 * @var null | array
	 */
	private $docTree = null;

	/**
	 * @var string
	 */
	private $urlSeparator = '/';


	/**
	 * @return array
	 */
	public function getDocTree()
	{
		if (is_null($this->docTree)) {
			$this->docTree = $this->documentationMiner->getDocTree();
		}

		return $this->docTree;
	}


	/**
	 * @param string $page
	 * @return array
	 */
	public function getPageBreadcrumb($page)
	{
		$pathParts  = explode($this->urlSeparator, $page);
		$docTree    = $this->getDocTree();
		$breadcrumb = [];

		// parse path from URL and find content based on given path
		foreach ($pathParts as $pathPart) {
			if (isset($docTree[$pathPart]['data'])) {
				$breadcrumb[] = $docTree[$pathPart];
				$docTree      = $docTree[$pathPart]['data'];
			}
			else {
				return [];
			}
		}

		return $breadcrumb;
	}


	/**
	 * @param string $file path do documentation file
	 * @return string file content
	 */
	public function loadFileContent($file)
	{
		return file_get_contents($file);
	}


	/**
	 * @param string $page
	 * @param string $imageLink
	 * @return \Nette\Utils\Image
	 */
	public function getImage($page, $imageLink)
	{
		try {
			$breadcrumb  = $this->getPageBreadcrumb($page);
			$docFilename = array_pop($breadcrumb)['data'];
			if ($docFilename && is_file($docFilename)) {

				// check if image path is part of original doc file content
				if (strpos(file_get_contents($docFilename), $imageLink) === false) {
					throw new InvalidArgumentException("Image path '$imageLink' is not a part of doc file '$docFilename' content");
				}

				$dirname   = pathinfo($docFilename, PATHINFO_DIRNAME);    // /doxen/docs/04_Komponenty/00_ACL
				$imagePath = $dirname . DIRECTORY_SEPARATOR . $imageLink; // /doxen/docs/04_Komponenty/00_ACL/images/database.png

				// check if image file exists
				if (!file_exists($imagePath)) {
					throw new InvalidArgumentException("Image file not found '$imagePath'");
				}

				// check if image path is under documentation file path (image path outside documentation file folder is not allowed for security reasons)
				$imagePath = realpath($imagePath); // /doxen/docs/04_Komponenty/01_ElForm/../00_ACL/images/database.png => /doxen/docs/04_Komponenty/00_ACL/images/database.png
				if (!Strings::startsWith($imagePath, $dirname)) {
					throw new InvalidArgumentException("Image path '$imagePath' is not a part of doc file path '$dirname'");
				}

				$image = Image::fromFile($imagePath); // UnknownImageFileException if file is not image

			}
			else {
				throw new InvalidArgumentException("Image doc file not found '$docFilename'.");
			}
		}
		catch (\Exception $e) {
			if ($this->logger) {
				$msg = sprintf("Image load error for page '%s' (%s): %s", $page, $e->getCode(), $e->getMessage());
				$this->logger->critical($msg);
			}
			$image = Image::fromBlank(400, 100, Image::rgb(250, 140, 140));
			$image->string(5, 20, 40, 'Image load problem, see log for details.', imagecolorallocate($image, 0, 255, 255));
		}

		return $image;
	}


	/**
	 * @param string $page
	 * @return string
	 */
	public function normalizePagename($page)
	{
		// remove .md suffix (fixes in doc links)
		if (Strings::endsWith($page, '.md')) {
			$page = substr($page, 0, -3);
		}

		return $page;
	}


	/**
	 * @return string
	 */
	public function getHomepageTitle()
	{
		return $this->documentationMiner->getHomepageTitle();
	}


	/**
	 * @return string
	 */
	public function getHomepageContent()
	{
		return $this->documentationMiner->getHomepageContent();
	}


	/**
	 * @return string
	 */
	public function getHomepagePath()
	{
		$homepage = $this->documentationMiner->getHomepage();

		return $homepage ? $homepage['path'] : '';
	}


	/**
	 * @param string $urlSeparator
	 */
	public function setUrlSeparator($urlSeparator)
	{
		$this->urlSeparator = $urlSeparator;
	}


	/**
	 * @param \Psr\Log\LoggerInterface $logger
	 */
	public function setLogger($logger)
	{
		$this->logger = $logger;
	}


	/**
	 * @param IDocumentationMiner $documentationMiner
	 */
	public function setDocumentationMiner($documentationMiner)
	{
		$this->documentationMiner = $documentationMiner;
	}

}