<?php
require_once "./vendor/autoload.php";

use DiDom\Document;
use DiDom\Element;

class ParserPrinters
{

    private $lang;

    private $printers;

    public function __construct($lang = 'ua')
    {
        $this->lang = $lang;
        $this->parse();
    }

    private function parse()
    {
        $linksTypesPrinters = $this->getBrandsLinks();
        $this->getPrintersWithLinkMaterials($linksTypesPrinters);
        $this->getLinksCartridgesAndInk();
    }

    private function getBrandsLinks()
    {
        $document = new Document('https://prote.ua/ua/brands/', true);
        $printType = [
            'ua' => ['лазерні', 'матирчні', 'струменеві'],
            'ru' => ['лазерные', 'матричные', 'струйные']
        ];
        $findLinks = [];
        foreach ($printType[$this->lang] as $type) {
            $query = 'a[title^=' . $type . ']';

            $query = $document->find($query);
            if (!empty($query)) {
                $findLinks[] = $query;
            }
        }

        $links = [];
        foreach ($findLinks as $link) {
            foreach ($link as $domElement) {
                $links[] = $domElement->attr('href');
            }
        }
        return $links;
    }

    private function getPrintersWithLinkMaterials(array $links)
    {
        $printersLinks = [];
        foreach ($links as $link) {
            $document = new Document($link, true);
            $printersLinks[] = $document->find('.plist-list a');
        };

        foreach ($printersLinks as $link) {
            foreach ($link as $domElement) {
                $this->printers[] = [
                    'title' => $domElement->text(),
                    'materials' => $domElement->attr('href')
                ];
            }
        }
        // var_dump($this->printers);
    }
    private function getLinksCartridgesAndInk()
    {
        $types = [
            'ua' => ['картриджі', 'чорнила'],
            'ru' => ['картриджи', 'чернила']
        ];
        foreach ($this->printers as $key => $printer) {
            print_r($printer);
            $document = new Document($printer->materials, true);
            foreach ($types as $type) {
                $link = $document->first('.cats ' + 'a[title^=' . $type . ']');
                $this->printers[$key][$type] = $document->find('.cats ' + 'a[title^=' . $type . ']');
            }
        }
    }
}

$parserPrinters = new ParserPrinters('ua');
