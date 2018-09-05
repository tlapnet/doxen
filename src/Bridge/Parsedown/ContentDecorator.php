<?php declare(strict_types = 1);

namespace Tlapnet\Doxen\Bridge\Parsedown;

use InvalidArgumentException;
use Nette\Application\Responses\CallbackResponse;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Nette\Utils\Image;
use Nette\Utils\Strings;
use Throwable;
use Tlapnet\Doxen\Event\AbstractEvent;
use Tlapnet\Doxen\Event\NodeEvent;
use Tlapnet\Doxen\Event\SignalEvent;
use Tlapnet\Doxen\Listener\IListener;
use Tlapnet\Doxen\Tree\AbstractNode;
use Tlapnet\Doxen\Tree\FileNode;
use Tlapnet\Doxen\Tree\TextNode;

class ContentDecorator implements IListener
{

	public const SIGNAL_PARSEDOWN_IMAGE = 'parsedown2image';

	public function listen(AbstractEvent $event): void
	{
		if ($event->getType() === AbstractEvent::TYPE_NODE) {
			$this->decorateNode($event);
		} elseif ($event->getType() === AbstractEvent::TYPE_SIGNAL) {
			$this->decorateSignal($event);
		}
	}

	private function decorateNode(NodeEvent $event): void
	{
		/** @var TextNode $node */
		$node = $event->getNode();

		if ($node->getType() !== AbstractNode::TYPE_LEAF) {
			return;
		}

		if (!($node instanceof TextNode)) {
			return;
		}

		$parsedown = new DoxenParsedown($event->getControl());
		$content = $parsedown->text($node->getContent());
		$node->setContent($content);
	}

	private function decorateSignal(SignalEvent $event): void
	{
		if ($event->getSignal() === self::SIGNAL_PARSEDOWN_IMAGE) {
			$this->processImage($event);
		}
	}

	private function processImage(SignalEvent $event): void
	{
		$docTree = $event->getDocTree();
		$control = $event->getControl();

		$imageNode = $control->page ? $docTree->getNode($control->page) : $docTree->getHomepage();
		$imageLink = $control->getParameter('imageLink', false);

		// prepare image
		if ($imageNode
			&& ($imageNode instanceof FileNode)
			&& $imageNode->getType() === AbstractNode::TYPE_LEAF
			&& $imageLink
		) {
			$image = $this->getImage($imageNode, $imageLink);
		} else {
			$image = $this->getErrorImage();
		}

		// prepare and send image reponse
		$response = new CallbackResponse(function (IRequest $httpRequest, IResponse $httpResponse) use ($image): void {
			$httpResponse->addHeader('Content-Type', 'image/jpeg');
			echo $image->toString(Image::JPEG, 94);
		});

		$control->getPresenter()->sendResponse($response);
	}

	private function getImage(FileNode $node, string $imageLink): Image
	{
		try {
			// check if image path is part of original doc file content
			if (strpos($node->getContent(), $imageLink) === false) {
				throw new InvalidArgumentException(sprintf("Image path %s is not a part of doc file '%s' content", $imageLink, $node->getFilename()));
			}

			$dirname = pathinfo($node->getFilename(), PATHINFO_DIRNAME);    // /doxen/docs/04_Komponenty/00_ACL
			$imagePath = $dirname . DIRECTORY_SEPARATOR . $imageLink; // /doxen/docs/04_Komponenty/00_ACL/images/database.png

			// check if image file exists
			if (!file_exists($imagePath)) {
				throw new InvalidArgumentException(sprintf('Image file not found %s', $imagePath));
			}

			// check if image path is under documentation file path (image path outside documentation file folder is not allowed for security reasons)
			$imagePath = realpath($imagePath); // /doxen/docs/04_Komponenty/01_ElForm/../00_ACL/images/database.png => /doxen/docs/04_Komponenty/00_ACL/images/database.png
			if (!Strings::startsWith($imagePath, $dirname)) {
				throw new InvalidArgumentException(sprintf('Image path %s is not a part of doc file path %s', $imagePath, $dirname));
			}

			$image = Image::fromFile($imagePath); // UnknownImageFileException if file is not image
		} catch (Throwable $e) {
			$image = $this->getErrorImage();
		}

		return $image;
	}


	private function getErrorImage(): Image
	{
		$image = Image::fromBlank(400, 100, Image::rgb(250, 140, 140));
		$image->string(5, 20, 40, 'Image load problem.', imagecolorallocate($image->getImageResource(), 0, 255, 255));

		return $image;
	}

}
