<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

t3lib_extMgm::addStaticFile($_EXTKEY,'static/','CSS Styled IMGTEXT');

$tempColumns = Array (
    "tx_cssstyledimgtext_floathorizontal" => Array (        
        "exclude" => 1,        
        "label" => "LLL:EXT:css_styled_imgtext/locallang_db.php:tt_content.tx_cssstyledimgtext_floathorizontal",        
        "config" => Array (
            "type" => "check",
        )
    ),
);


t3lib_div::loadTCA("tt_content");
t3lib_extMgm::addTCAcolumns("tt_content",$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes("tt_content","tx_cssstyledimgtext_floathorizontal;;;;1-1-1",'textpic');

?>