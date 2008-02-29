<?php
//
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ REST
// SOFTWARE RELEASE: 1.0.x
// COPYRIGHT NOTICE: Copyright (C) 2007 eZ Systems AS
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
//
//
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//

$http = eZHTTPTool::instance();
$hasError = false;
$errorMessage = '';

$functionName = $Params['FunctionName'];
$functionDefinition = $Params['Module']->Functions[$functionName];

// Check if handler exists.
if ( empty( $functionDefinition['handler'] ) )
{
    $errorMessage = 'Empty handler provided for: ' . $functionName;
    eZDebug::writeError( $errorMessage,
                         'ezrest/' . $functionName );
    $hasError = true;
}

// Check if method exists.
if ( empty( $functionDefinition['method'] ) )
{
    $errorMessage = 'No method is specified for: ' . $functionName;
    eZDebug::writeError( $errorMessage,
                         'ezrest/' . $functionName );
    $hasError = true;
}

// Get parameters.
$getParamArray = array();
if ( !empty( $functionDefinition['getParams'] ) )
{
    foreach( $functionDefinition['getParams'] as $paramName )
    {
        if ( !$http->hasGetVariable( $paramName ) )
        {
            $errorMessage = 'Required GET parameter not provided: ' . $paramName;
            eZDebug::writeError( $errorMessage,
                                 'ezrest/' . $functionName );
            $hasError = true;
        }
        $getParamArray[$paramName] = $http->getVariable( $paramName );
    }
}

// Get options.
$getOptionsArray = array();
if ( !empty( $functionDefinition['getOptions'] ) )
{
    foreach( $functionDefinition['getOptions'] as $name => $value )
    {
        if ( $http->hasVariable( $name ) )
        {
            $getOptionsArray[$name] = $http->variable( $name );
        }
        else
        {
            $getOptionsArray[$name] = $value;
        }
    }
}


// Post parameters.
$postParamArray = array();
if ( !empty( $functionDefinition['postParams'] ) )
{
    foreach( $functionDefinition['postParams'] as $postParamName )
    {
        if ( !$http->hasPostVariable( $postParamName ) )
        {
            $errorMessage = 'Required POST parameter not provided: ' . $postParamName;
            eZDebug::writeError( $errorMessage,
                                 'ezrest/' . $functionName );
            $hasError = true;
        }
        $postParamArray[$postParamName] = $http->postVariable( $postParamName );
    }
}

// Post options.
$postOptionsArray = array();
if ( !empty( $functionDefinition['postOptions'] ) )
{
    foreach( $functionDefinition['postOptions'] as $name => $value )
    {
        if ( $http->hasPostVariable( $name ) )
        {
            $postOptionsArray[$name] = $http->postVariable( $name );
        }
        else
        {
            $postOptionsArray[$name] = $value;
        }
    }
}

$domDocument = new DOMDocument( '1.0', 'utf-8' );
$rootElement = $domDocument->createElement( 'eZREST' );
$rootElement->setAttribute( 'function', $functionName );
$domDocument->appendChild( $rootElement );

if ( !$hasError )
{
    try
    {
        $result = call_user_func_array( array( $functionDefinition['handler'], $functionDefinition['method'] ),
                                        array( 'getParameters' => $getParamArray,
                                               'getOptions' => $getOptionsArray,
                                               'postParameters' => $postParamArray,
                                               'postOptions' => $postOptionsArray ) );
        if ( $result instanceof DOMNodeList )
        {
            for ( $i = 0; $i < $result->length; ++$i)
            {
                $resultElement = $result->item($i);
                $resultElement = $domDocument->importNode( $resultElement, true );
                $rootElement->appendChild( $resultElement );
            }
        }
        else
        {
            // Assume class is DOMElement
            $resultElement = $domDocument->importNode( $result, true );
            $rootElement->appendChild( $resultElement );
        }
    }
    catch ( Exception $e )
    {
        $hasError = true;
        $errorMessage = $e->getMessage();
    }
}

if ( $hasError )
{
    // Create Error reply
    $restINI = eZINI::instance( 'ezrest.ini' );
    $errorHandlerClass = $restINI->variable( 'RESTSettings', 'ErrorHandler' );
    $errorHandler = new $errorHandlerClass( $errorMessage );
    $errorReply = $errorHandler->getResponse();

    if ( $errorReply instanceof DOMNodeList )
    {
        for ( $i = 0; $i <= $errorReply->length; ++$i)
        {
            $resultElement = $errorReply->item($i);
            $resultElement = $domDocument->importNode( $resultElement, true );
            $rootElement->appendChild( $resultElement );
        }
    }
    else
    {
        // Assume class is DOMElement
        $resultElement = $domDocument->importNode( $errorReply, true );
        $rootElement->appendChild( $resultElement );
    }
}

// If output buffer is different from only whitespaces, output it.
if ( trim( ob_get_contents() ) != '' )
{
    ob_end_flush();
}
else
{
    ob_clean(); // will remove any debug info etc...
}

header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-cache, must-revalidate' );
header( 'Pragma: no-cache' );
header( 'X-Powered-By: eZ Publish' );
header( 'Content-Type: text/xml; charset=utf-8' );
header( 'Served-by: $_SERVER["SERVER_NAME"]' );

echo $domDocument->saveXML();

eZExecution::cleanExit();

?>