<?php


/** PHP 5 script, eZ Publish is not a requirement for running this script
  *
  * Run it from the eZ Publish root directory.
  *
  * php5 ./extension/domdocument-php4/bin/domdocument-php5-tests.php
  *
  **/

require_once( 'extension/domdocument-php4/classes/domdocumenttest.php' );

$DOMDocumentTest = new DOMDocumentTest();
$DOMDocumentTest->run();

/*
$domDocument = new DOMDocument( '1.0', 'utf-8' );
$domElement = $domDocument->createElement( 'Error' );
$domElement->appendChild( $domDocument->createTextNode( "Error message" ) );
$domDocument->appendChild( $domElement );


$domDocument2 = new DOMDocument( '1.0', 'utf-8' );
$rootElement = $domDocument2->createElement( 'eZREST' );
$rootElement->setAttribute( 'function', "sitecount" );
$domDocument2->appendChild( $rootElement );

$result = $domDocument->childNodes;
$resultElement = $result->item( 0 );
$resultElement = $domDocument2->importNode( $resultElement, true );
$rootElement->appendChild( $resultElement );
*/

//echo $domDocument2->saveXML();


/*
$doc = new DOMDocument( '1.0', 'utf-8' );

$domElement = $doc->createElement( 'Error' );
$domElement->appendChild( $doc->createTextNode( "Error message" ) );
$doc->appendChild( $domElement );

$domElement = $doc->createElement( 'Error2' );
$domElement->appendChild( $doc->createTextNode( "Error message2" ) );
$doc->appendChild( $domElement );

//$domNode = new DOMNode( 'erro3' );
//$domNode->nodevalue = "hei";
//$doc->importNode( $domNode );

echo $doc->saveXML();
foreach ( $doc->childNodes as $a );
{
//    var_dump( $a );
}
*/

?>