<?php

class DOMDocumentTest
{
    var $DOMDocumentClassName;
    var $DOMDocument;

    /*!
     Constructor
    */
    function DOMDocumentTest()
    {
/*
        $doc = new eZDOMDocument();
        $doc->setName( "FishCatalogue" );

        $root = $doc->createElementNode( "FishCatalogue" );
        $doc->setRoot( $root );

        $freshWater = $doc->createElementNode( "FreshWater" );
        $root->appendChild( $freshWater );

        $saltWater = $doc->createElementNode( "SaltWater" );
        $root->appendChild( $saltWater );

        $cod = $doc->createElementNode( "Cod" );
        $saltWater->appendChild( $cod );
*/




        // If PHP5
        if ( version_compare( PHP_VERSION, '5','>=' ) )
        {
            $this->DOMDocumentClassName = 'DOMDocument';
        }
        else
        {
            $this->DOMDocumentClassName = 'DOMDocumentPHP4';
        }

    }

    /* Function which runs all the tests */
    function run()
    {
        $test = new DOMDocumentTest();
        $DOMDocument = $this->createDocument();

        // Tests
        //echo $test->createElement();
        //echo $test->createElements();
        //echo $test->createDocumentAndSave();
        echo $test->loadXMLappendElementdAndSave();
        //echo $test->createMultipleRootElements();

        //echo $test->importNode();
        //echo $test->subElements();
        //echo $test->childNodes();
}

    /* Returns a DOMDocument
     * Used by all the tests
     */
    function createDocument()
    {
        $DOMDocumentClassName = $this->DOMDocumentClassName;

        $DOMDocument = new $DOMDocumentClassName( '1.0', 'utf-8' );
        return $DOMDocument;
    }


    function createElement()
    {
        $domDocument = $this->createDocument();
        $rootElement = $domDocument->createElement( 'eZREST' );
        $rootElement->setAttribute( 'function', 'This is the functionName' );
        $domDocument->appendChild( $rootElement );

        $resultElement = false;
        $resultElement = $domDocument->importNode( $resultElement, true );
        $rootElement->appendChild( $resultElement );

        return $domDocument->saveXML();

    }


    function createElements()
    {
        $domDocument = $this->createDocument();
        $rootElement =& $domDocument->createElement( 'eZREST' );
        $rootElement->setAttribute( 'function', 'This is the functionName' );
        $domDocument->appendChild( $rootElement );

        $element =& $domDocument->createElement( 'Error' );
        $element->setAttribute( 'function', "sitecount" );
        $rootElement->appendChild( $element );

        $element =& $domDocument->createElement( 'Error2' );
        $element->setAttribute( 'function', "sitecount" );
        $rootElement->appendChild( $element );

        return $domDocument->saveXML();

    }


    function childNodes()
    {
        $domDocument = $this->createDocument();

        $element =& $domDocument->createElement( 'Error' );
        $element->setAttribute( 'function', "sitecount" );
        $domDocument->appendChild( $element );

        $element2 =& $domDocument->createElement( 'Error2' );
        $element2->setAttribute( 'function', "sitecount" );
        $domDocument->appendChild( $element2 );

        // Returns a domnodelist object
        //return $domDocument->saveXML();

    }

    /* Creates a document and saves it
     */
    function createDocumentAndSave()
    {
        $doc = $this->createDocument();
        return $doc->saveXML();
    }

    /**
     * Loads some XML from a string, appends a child, and then saves the XML
     *
     */
    function loadXMLappendElementdAndSave()
    {
        $strXML =
'<?xml version="1.0" encoding="utf-8"?>
<eZREST function="This is the functionName"/>
<eZREST function="This is the functionName2"/>
';

        $domDocument = $this->createDocument();
        $domDocument->loadXML( $strXML );
        $rootElement =& $domDocument->createElement( 'eZREST2' );
        $rootElement->setAttribute( 'function', 'This is the functionName added 2' );
        $domDocument->appendChild( $rootElement );
        return $domDocument->saveXML();
    }


    /**
     *
     */
    function createMultipleRootElements()
    {
        $doc = $this->createDocument();

        $domElement =& $doc->createElement( 'Error' );
        $domElement->appendChild( $doc->createTextNode( "Error message" ) );
        $doc->appendChild( $domElement );

        $domElement2 =& $doc->createElement( 'Error' );
        $domElement2->appendChild( $doc->createTextNode( "Error message2" ) );
        $doc->appendChild( $domElement2 );

        return $doc->saveXML();
    }

    /**
     * Creates a DOMDocument with a few nodes, and then imports these nodes into another document
     *
     */
    function importNode()
    {
        // Create document with nodes
        $doc = $this->createDocument();

/*
        $domElement = $doc->createElement( 'Error' );
        $domElement->appendChild( $doc->createTextNode( "Error message" ) );
        $doc->appendChild( $domElement );

//        $domElement = $doc->createElement( 'Error2' );
//        $domElement->appendChild( $doc->createTextNode( "Error message2" ) );
//        $doc->appendChild( $domElement );

        // Create a new document, and import the nodes retrived by DOMDocument->childNodes under a new root node
        $result = $doc->childNodes;

        $doc = $this->createDocument();
        $rootElement = $doc->createElement( 'eZREST' );
        $rootElement->setAttribute( 'function', 'MYeZRestFunction' );
        $doc->appendChild( $rootElement );

        // - Import nodes under the root node
        $resultElement = $result->item( 0 );

//        var_dump( $resultElement );
        $resultElement = $doc->importNode( $resultElement, true );
//        $doc->appendChild( $resultElement );

//        echo $doc->eZDOMDocument->toString();
//        die();
*/


        // Create document with nodes
        $domDocument = $this->createDocument();

        $domElement =& $domDocument->createElement( 'Error' );
        $domElement->appendChild( $domDocument->createTextNode( "Error message" ) );
        $domDocument->appendChild( $domElement );

        $domElement =& $domDocument->createElement( 'Error2' );
        $domElement->appendChild( $domDocument->createTextNode( "Error message2" ) );
        $domDocument->appendChild( $domElement );


        // Create a new document, and import the nodes retrived by DOMDocument->childNodes under a new root node
        $domDocument2 = $this->createDocument();
        $rootElement =& $domDocument2->createElement( 'eZREST' );
        $rootElement->setAttribute( 'function', "sitecount" );
        $domDocument2->appendChild( $rootElement );


        // - Import start
        $result = $domDocument->childNodes;
        $resultElement =& $result->item( 0 );
        $resultElement = $domDocument2->importNode( $resultElement, true );
        $rootElement->appendChild( $resultElement );

        $resultElement =& $result->item( 1 );
        $resultElement = $domDocument2->importNode( $resultElement, true );
        $rootElement->appendChild( $resultElement );

        // Add sub-element
        $domElement =& $domDocument2->createElement( 'Sub-Error' );
        $domElement->setAttribute( 'function', "sitecount" );
        //$domElement->appendChild( $domDocument->createTextNode( "sub Error message" ) );
        $rootElement->appendChild( $domElement );

        return $domDocument2->saveXML();
    }


    function subElements()
    {
        // Create document with nodes
        $domDocument = $this->createDocument();

        $root =& $domDocument->createElement( "My-new-root" );
        $domDocument->appendChild( $root );


        $element =& $domDocument->createElement( 'eZRest' );
        $element->setAttribute( 'function', "sitecount" );
        $root->appendChild( $element );

        $element2 =& $domDocument->createElement( 'Error' );

        $element2->appendChild( $domDocument->createTextNode( "Error message" ) );
        $element->appendChild( $element2 );

        return $domDocument->saveXML();
    }

/*
        TODO: Test cases

        $sessionID = "eZSESSID=asdf";
        $domDocument->createElement( 'SessionID', $sessionID );

        $domLogin = new DOMDocument( '1.0' );
        $domLogin->loadXML( $client->send() ); // parameters are reset when send is invoked.


PHP5 domdocument

$domDocument = new DOMDocument( '1.0', 'utf-8' );
$rootElement = $domDocument->createElement( 'eZREST' );
$rootElement->setAttribute( 'function', $functionName );
$domDocument->appendChild( $rootElement );
$resultElement = $domDocument->importNode( $resultElement, true );
$rootElement->appendChild( $resultElement );

// toString 
 print $xml->saveXML();

 class ezsfRESTRequestReceiver
    public function getResponse()
    {
        return $this->domDocument->childNodes ;
    }




$xml = new eZXML();
$dom = $xml->domTree( file_get_contents( $fileName ) );

$xml = new eZXML();
$xmlDoc = $order->attribute( 'data_text_1' );
$dom = $xml->domTree( $xmlDoc );

 

  Example of using the DOM document to create a node structure.
  \code
  $doc = new eZDOMDocument();
  $doc->setName( "FishCatalogue" );

  $root = $doc->createElementNode( "FishCatalogue" );
  $doc->setRoot( $root );

  $freshWater = $doc->createElementNode( "FreshWater" );
  $root->appendChild( $freshWater );

  $saltWater = $doc->createElementNode( "SaltWater" );
  $root->appendChild( $saltWater );

  $guppy = $doc->createElementNode( "Guppy" );
  $guppy->appendChild( $doc->createTextNode( "Guppy is a small livebreeder." ) );

  $freshWater->appendChild( $guppy );

  $cod = $doc->createElementNode( "Cod" );
  $saltWater->appendChild( $cod );




    */

}
?>
