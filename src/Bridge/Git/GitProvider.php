<?php declare(strict_types = 1);

namespace Tlapnet\Doxen\Bridge\Git;

use DateTime;
use Tlapnet\Doxen\Event\NodeEvent;
use Tlapnet\Doxen\Listener\AbstractNodeListener;
use Tlapnet\Doxen\Tree\FileNode;

class GitProvider extends AbstractNodeListener
{

	public function decorateNode(NodeEvent $event): void
	{
		if (($node = $this->getFileNode($event)) === null)
			return;

		if (!file_exists($node->getFilename()))
			return;

		$author = self::git($node, 'log -1 --format="%cn"', true);
		$email = self::git($node, 'log -1 --format="%ce"', true);
		$date = self::git($node, 'log -1 --format="%cd"', true);
		$date = new DateTime($date);

		$topLevel = self::git($node, 'rev-parse --show-toplevel');
		$gitFileName = str_replace($topLevel . '/', '', $node->getFilename());
		$currentBranch = self::git($node, 'rev-parse --abbrev-ref HEAD');
		$originUrl = self::git($node, 'config --get remote.origin.url');
		$nameParts = explode(':', $originUrl);
		$projectName = array_pop($nameParts);
		assert($projectName !== null);
		$projectName = substr($projectName, 0, -4);

		$node->setMetadataPart('git', [
			'lastCommiterName' => $author,
			'lastCommiterEmail' => $email,
			'lastCommiterDate' => $date,
			'topLevel' => $topLevel,
			'fileName' => $gitFileName,
			'currentBranch' => $currentBranch,
			'originUrl' => $originUrl,
			'projectName' => $projectName,
		]);
	}

	private static function git(FileNode $node, string $command, bool $appendFileName = false): string
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
