<?php

/** eZ Publish script which runs the test on DOMDocument
  *
  * Run it from the eZ Publish root directory.
  *
  * php ./extension/domdocument-php4/bin/domdocument-php5-tests.php
  **/


error_reporting( E_ALL | E_NOTICE );

require_once( 'lib/ezdb/classes/ezdb.php' );
require_once( 'lib/ezutils/classes/ezcli.php' );
require_once( 'lib/ezutils/classes/ezsys.php' );
require_once( 'kernel/classes/ezscript.php' );
//require_once( 'kernel/classes/ezclusterfilehandler.php' );
include_once( 'lib/ezxml/classes/ezxml.php' );

$cli =& eZCLI::instance();
$script =& eZScript::instance( array( 'description' => false,
                                      'use-session'    => false,
                                      'use-modules'    => false,
                                      'use-extensions' => true ) );

$script->startup();


$options = $script->getOptions( "",
                                "",
                                array() );
$script->initialize();

require_once( 'extension/domdocument-php4/classes/domdocumenttest.php' );
require_once( 'extension/domdocument-php4/classes/domdocument-php4.php' );

$DOMDocumentTest = new DOMDocumentTest();
$DOMDocumentTest->run();




/* DOMDocument with subElement */
/*
$doc = new eZDOMDocument();
$root = $doc->createElementNode( "DOMDocument" );
//var_dump( $root );
$doc->setRoot( $root );

$element = $doc->createElement( 'eZRest' );
$element->setAttribute( 'function', "sitecount" );
$root->appendChild( $element );

$element2 = $doc->createElement( 'Rootnode' );
$element2->setAttribute( 'function', "sitecount" );
$element->appendChild( $element2 );

$element3 = $doc->createElement( 'Error' );
$element3->appendChild( $doc->createTextNode( "Error message" ) );
$element2->appendChild( $element3 );
*/
//die();

//echo $doc->toString() . "\n";


$script->shutdown();

?>