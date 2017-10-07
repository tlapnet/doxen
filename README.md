# Doxen lib

Nette komponenta DoxenControl umoznuje snadny render markdown dokumentace ve vasi aplikaci. Vyhodou je snadne pouziti a moznost komplexni konfigurace kde a jak se ma dokumentace hledat a jak se bude
prezentovat uzivateli. V dokumentaci lze mit obrazky a odkazy na dalsi podstranky, ktere mohou byt libovolne zanorene v adresarove strukture. Relativni odkazy jsou upraveny a funkcni, absolutni URL adresy
zustaveji nezmenene.

## Pouziti komponenty

Komponentu pouzijeme klasickym Nette zpousobem pres macro `{control}` v sablone na miste kde chceme zobrazit obsah dokumentace.

**default.latte**
```
{control documentation}
```

**DefaultPresenter**
```php
<?php

final class DefaultPresenter extends BasePresenter
{
	public function createComponentDocumentation()
	{
		// nejjednodussi pouziti, v konstruktoru uvedeme primo cestu k rootu dokumentace, pripadne lze do presenteru nastavit v neonu %appDir% a cestu uvest relativne
		return new DoxenControl('/some/path/to/documentation');
		
		// pripadne lze provest komplexnejsi konfiguraci nastavenim pole se strukturou uvedenou v prikladech nize
		$doxenControl = new DoxenControl('/some/path/to/documentation');
		$doxenControl->setConfig(array(/* configuration */));
		
		// lze provadet dalsi dodatecnou konfiguraci (vsechny uvedene parametry maji vychozi nastaveni, takze jejich nastaveni je nepovinne)
		$doxenControl->setLayoutTemplate($layoutTemplate);              // nastaveni cesty k sablone layoutu dokumentace
        $doxenControl->setDocTemplate($docTemplate);                    // nastaveni cesty k sablone obsahu dokumentace
        $doxenControl->setListTemplate($listTemplate);                  // nastaveni cesty k sablone seznamu stranek dokumentace
        $doxenControl->setMenuTemplate($menuTemplate);                  // nastaveni cesty k sablone hlavniho menu dokumentace
        $doxenControl->setBreadcrumbTemplate($breadcrumbTemplate);      // nastaveni cesty k sablone drobeckove navigace dokumentace
        $doxenControl->setCssStyleFile($cssStyleFile);                  // nastaveni cesty k souboru se styly dokumentace
        
        $doxenControl->showBreadcrumb($showBreadcrumb); // nastaveni zobrazeni drobeckova navigace (defaultne zapnuto)
        $doxenControl->showMenu($showMenu);             // nastaveni zobrazeni hlavniho menu (defaultne zapnuto)		
	}
}
```

## Moznosti konfigurace homepage

```yaml
home:                                               # pouzije se titulek a textovy obsah dle nastaveni
	title: titulek uvodni stranky
	content: textovy retezec na uvodni stranku
doc:
    - /path/to/md/files
```

```yaml
home:                                               # pouzije se titulek dle nastaveni a obsah se nacte z uvedeneho souboru
	title: titulek uvodni stranky
	content: /path/to/md/files/some/readme.md
doc:
    - /path/to/md/files
```

```yaml
home:                                               # jako titulek se pouzije nazev souboru (tedy 'readme'), obsah se nacte z uvedeneho souboru 
	content: /path/to/md/files/some/readme.md
doc:
    - /path/to/md/files
```

```yaml
home:                                               # jako obsah se pouzije uvedeny retezec, jako titulek se pouzije se vychozi retezec "Homepage"
	content: textovy retezec na uvodni stranku
doc:
    - /path/to/md/files
```

```yaml
doc:                                                # do home se nastavi prvni nalezeny soubor tj. titulek bude nazev souboru a obsah uvodni stranky se nacte z tohoto souboru
    - /path/to/md/files
```                         

```yaml
home:                                               # do home se nastavi prvni nalezeny soubor, titulek se pouzije uvedeny v nastaveni
    title: titulek uvodni stranky
doc:
    - /path/to/md/files
```

## Moznosti konfigurace obsahu

```yaml
home:
	title: Úvod                                           # nadpis uvodni stranky
	content: Zvolte prosím téma dokumentace v menu vlevo. # retezec nebo cesta k *.md souboru
doc:
    # je mozne uvest primo adresar bez nutnosti definovat sekce v hlavnim menu
    # sekce se vytvori z nazvu adresaru, pokud je pouzit format nazvu adresaru <cislo>_<nazev> tak je zobrazen jen <nazev> a polozky serazeny dle <cislo>
    - /path/to/md/files

    # nebo je mozne vytvorit strukturu dokumentace konkretni konfiguraci
	Nette:                              # sekce v hlavnim menu
		Uživatelský manuál:             # nadpis polozky v hlavnim menu v ramci sekce, lze na ni kliknout a zobrazit tak obsah
			- /path/to/md/files         # prohleda rekurzivne uvedenou slozku a zobrazi vsechny *.md soubory v ni
        Uživatelský manuál:
			- /path/to/md/files
            - /another/path/to/md/files # je mozne v ramci jedne sekce nechat prohledat vice slozek
        Uživatelský manuál:
			- /path/to/md/files/doc.md  # je mozne zadat cestu ke konkretnimu souboru
        Uživatelský manuál:
			- /path/to/md/files/doc1.md # nebo vice souborum
            - /path/to/md/files/doc2.md
        Uživatelský manuál:             # strukturu dokumentace lze libovolne clenit do dalsich sekci
			- /path/to/md/files/doc1.md
            Sekce 1:
                - /path/to/md/files
            Sekce 2:
                - /path/to/md/files/doc2.md
            Sekce 3:
                - /path/to/md/files/doc3.md

	PHP:                                # dalsi sekce v hlavnim menu
		- /path/to/md/files
```