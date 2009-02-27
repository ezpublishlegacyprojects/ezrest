<?php

/**
 * Implementation of the DOMDocument which wraps to eZXML
 *
 */

include_once( 'lib/ezxml/classes/ezdomdocument.php');
include_once( 'lib/ezxml/classes/ezxml.php');

/**
 *
 * Use of references
 *
 * Always assign by referance when using the function createElement()
 *
 * Example:
 * $element =& $domDocument->createElement( 'Fish' );
 *
 *
 * Known issues:
 *
 * PHP5 code:
 * $sessionID1 = $domLogin->getElementsByTagName( 'SessionID' )->item( 0 )->nodeValue;
 *
 * PHP 4 code
 * $sessionID1 = $domLogin->getElementsByTagName( 'SessionID' );
 * $sessionID1 = $sessionID1->item( 0 );
 * $sessionID1 = $sessionID1->Children[ 0 ]->content;
 *
 **/

class DOMDocumentPHP4
{
    var $version;
    var $charset;
    var $eZDOMDocument;
    var $childNodes; // new DOMNodeList();
    var $root; // holds the root node of eZDOMDocument

    function DOMDocumentPHP4( $version, $charset = 'UTF-8' )
    {
        // eZDOMDocument does not support version and charset
        // charset will still be used then saving XML to a string
        $this->version = $version;
        $this->charset = $charset;

        $this->childNodes = new DOMNodeList();

        // Create document and create a root element
        // Root element is required by eZDOMDocument
        $doc = new eZDOMDocument();
        $root = $doc->createElementNode( "RootDOMDocument" );
        $doc->setRoot( $root );

        // Assign local variables as reference
        $this->eZDOMDocument =& $doc;
        $this->root =& $doc->root();

        return $doc;
    }

    /** Wrapper for eZDOMDocument's createElement function
     *
     * $value needs to be implemented
     */
    function &createElement( $name, $value = false )
    {
        $element = $this->eZDOMDocument->createElement( $name );

        if ( $value != false )
        {
            //echo "DOMDocumentPHP4 ->createElement Parameter $value is not supported yet\n";
            $valueElement = $this->eZDOMDocument->createTextNode( $value );
            $element->appendChild( $valueElement );
        }

        return $element;
    }

    /* Wrapper for eZDOMDocument's root appendChild function */
    function &appendChild( &$element )
    {
        $child = $this->root->appendChild( $element );

        // Update childNode ( class DOMNodeList )
        $this->childNodes->append( $child );

        return $child;
    }


    /* Wrapper for eZDOMDocument's ?? function
     * importNode does not exists in eZDOMDocument
     * The elementes was imported with appendChild directly
     */
    function importNode( $element, $deep = true )
    {
        return $element;
    }

    /* Wrapper for eZDOMDocument's createTextNode function */
    function createTextNode( $text )
    {
        $textNode = $this->eZDOMDocument->createTextNode( $text );
        return $textNode;
    }

    /* Wrapper for eZDOMDocument's elementsByName function */
    function getElementsByTagName( $name )
    {
        $elements =& $this->eZDOMDocument->elementsByName( $name );

        $nodeList = new DOMNodeList();

        foreach( $elements as $element )
        {
            $nodeList->append( $element );
        }
        return $nodeList;
    }

    /* Wrapper for eZDOMDocument's toString function */
    function saveXML()
    {
        $xml = '<?xml version="' . $this->version . '" encoding="' . $this->charset . '"?>' . "\n";
        foreach ( $this->root->Children as $child )
        {
            $xml .= $child->toString( 0 ) . "\n";
        }

        return $xml;
    }


    /* Wrapper for eZDOMDocument's eZXML->domTree function which loads XML information from strings */
    function loadXML( $text )
    {
        $xml = new eZXML();
        $xmlDoc = $text;
        $this->eZDOMDocument =& $xml->domTree( $xmlDoc );
        $this->root =& $this->eZDOMDocument->Root;

        $currentRoot =& $this->eZDOMDocument->Root;

        // As a workaround for the root element problem, a standard root node is created
        // This root node is skipped when using saveXML

        // Create new root node
        $root = $this->eZDOMDocument->createElementNode( "RootDOMDocument" );
        $this->eZDOMDocument->setRoot( $root );
        $this->root =& $root;
        $root->appendChild( $currentRoot );

        return $this->eZDOMDocument;
    }
}


/**
 *
 * NOTE: elements in the list will be eZDomNode and not DOMElement as in PHP5
 *
 */
class DOMNodeList
{
    var $nodelist;
    var $length;

    function DOMNodeList()
    {
        $this->nodelist = array();
        $this->length = 0;
    }


    function &item( $index )
    {
        return $this->nodelist[ $index ];
    }


    /**
     * Extra function for adding new nodes
     */
    function append( &$child )
    {
        $this->nodelist[] = $child;
        $this->length++;
    }
}

/*
class DOMElementPHP4
{
    var $nodelist;

    function DOMElementPHP4()
    {
        $nodelist = array();
    }

    function item( $index )
    {

    }
}
*/
