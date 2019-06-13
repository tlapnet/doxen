# Tlapnet Doxen

Nette komponenta DoxenControl umoznuje snadny render markdown dokumentace ve vasi aplikaci. Vyhodou je snadne pouziti a moznost komplexni konfigurace kde a jak se ma dokumentace hledat a jak se bude
prezentovat uzivateli. V dokumentaci lze mit obrazky a odkazy na dalsi podstranky, ktere mohou byt libovolne zanorene v adresarove strukture. Relativni odkazy jsou upraveny a funkcni, absolutni URL adresy
zustaveji nezmenene.

## Content

- [Setup](#setup)
- [Usage](#usage)

## Setup

```bash
composer require tlapnet/doxen
```

## Usage

### Latte

**default.latte**
```
{control documentation}
```

### MVC

**DocumentationPresenter**

```php
<?php declare(strict_types = 1);

namespace App\Presenters;

use Nette\Application\UI\Presenter;
use Tlapnet\Doxen\Bridge\Parsedown\ContentDecorator;
use Tlapnet\Doxen\Component\Config;
use Tlapnet\Doxen\Component\DoxenControl;
use Tlapnet\Doxen\Miner\FileDocumentationMiner;

class DocumentationPresenter extends Presenter
{

    public function createComponentDocumentation(): DoxenControl
    {
        $config = [
            'home' => [
                'title' => 'Uživatelská dokumentace',
                'content' => 'Obsah úvodní stránky uživatelské dokumentace.',
                // Lze předat i cestu k .md souboru
                // 'content' => '/path/to/md/files/some/readme.md',
            ],
            'doc' => [ // Lze předat přímo cestu nebo pojmenované sekce, které lze libovolně zanořit
                'Uživatelský manuál' => [
                    __DIR__ . '/../docs/',
                ],
            ],
        ];

        // Přes implementaci IDocumentationMiner získáme DocTree
        $miner = new FileDocumentationMiner($config);

        $control = new DoxenControl($miner->createTree(), $this->createConfig());

        // Pomocí dekorátorů lze modifikovat dokumentaci před vypsáním
        // Minimální konfigurací je ContentDecorator, který převádí markdown do html formátu
        $control->registerListener(new ContentDecorator());

        return $control;
    }

    private function createConfig(): Config
    {
        $config = new Config();

        // Přes objekt Config lze nastavit šablony, jestliže výchozí nevyhovují
        // $config->setLayoutTemplate($layoutTemplate);              // nastaveni cesty k sablone layoutu dokumentace
        // $config->setDocTemplate($docTemplate);                    // nastaveni cesty k sablone obsahu dokumentace
        // $config->setListTemplate($listTemplate);                  // nastaveni cesty k sablone seznamu stranek dokumentace
        // $config->setMenuTemplate($menuTemplate);                  // nastaveni cesty k sablone hlavniho menu dokumentace
        // $config->setBreadcrumbTemplate($breadcrumbTemplate);      // nastaveni cesty k sablone drobeckove navigace dokumentace
        // $config->setCssStyleFile($cssStyleFile);                  // nastaveni cesty k souboru se styly dokumentace

        // Skrytí menu
        // $config->setShowBreadcrumb(false);
        // $config->setShowMenu(false);

        return $config;
    }

}
```
