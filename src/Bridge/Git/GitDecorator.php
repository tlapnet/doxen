<?php

namespace Tlapnet\Doxen\Bridge\Git;

use DateTime;
use Nette\Bridges\ApplicationLatte\Template;
use Tlapnet\Doxen\Event\NodeEvent;
use Tlapnet\Doxen\Listener\AbstractNodeListener;
use Tlapnet\Doxen\Widget\WidgetManager;
use Tlapnet\Doxen\Widget\Widgets;

class GitDecorator extends AbstractNodeListener
{

	/**
	 * @param NodeEvent $event
	 * @return void
	 */
	public function decorateNode(NodeEvent $event)
	{
		if (!($node = $this->getFileNode($event))) return;

		if (!file_exists($node->getFilename())) return;

		$wm = new WidgetManager($node);
		$wm->get(Widgets::PAGE_MENU)->add('git', function (Template $template) use ($node) {
			$author = self::git($node, 'log -1 --format="%cn"', TRUE);
			$email = self::git($node, 'log -1 --format="%ce"', TRUE);
			$date = self::git($node, 'log -1 --format="%cd"', TRUE);
			$date = new DateTime($date);

			$topLevel = self::git($node, 'rev-parse --show-toplevel');
			$gitFileName = str_replace($topLevel . '/', NULL, $node->getFilename());
			$currentBranch = self::git($node, 'rev-parse --abbrev-ref HEAD');
			$originUrl = self::git($node, 'remote get-url origin');

			$node->setMetadataPart('git', [
				'lastCommiterName' => $author,
				'lastCommiterEmail' => $email,
				'lastCommiterDate' => $date,
				'topLevel' => $topLevel,
				'fileName' => $gitFileName,
				'currentBranch' => $currentBranch,
				'originUrl' => $originUrl,
			]);

			if (!$author && !$date)
				return NULL;

			$template->author = $author;
			$template->email = $email;
			$template->date = $date;
			$template->setFile(__DIR__ . '/templates/git.latte');

			return $template;
		});
	}

	/**
	 * @param FileNode $node
	 * @param string $command
	 * @param bool $appendFileName
	 * @return string
	 */
	private static function git(FileNode $node, $command, $appendFileName = FALSE)
	{
		$fileName = $node->getFilename();
		$dirName = dirname($fileName);
		$command = 'git -C ' . $dirName . ' ' . $command;
		if ($appendFileName) {
			$command .= ' -- ' . $fileName;
		}
		return exec($command);
	}

}
