<?php
require_once "./vendor/autoload.php";

use DiDom\Document;

class ParserPrinters {

    private $lang; 

public function __construct($lang = 'ua')
{
    $this->lang = $lang;
    $this->parse();
}

protected function parse(){
  $linksTypesPrinters = $this->getBrandsLink();
  $this->getPrintersNames($linksTypesPrinters);
}

protected function getBrandsLink()
{
    $document = new Document(`https://prote.ua/{$this->lang}/brands/`, true);
    $printType = [
        'urk' => ['лазерні', 'матирчні','струменеві'], 
        'rus' => ['лазерные', 'матричные', 'струйные']
    ];

  return $document->find('a[title^=лазерні]');
}

protected function getPrintersNames(array $links){
    foreach ($links as $link) {
        $document = new Document($link->attr('href'), true);
        $printers = $document->find('.plist-list ul li');
        var_dump($printers);

    }
}
}

$parserPrinters = new ParserPrinters('ua');