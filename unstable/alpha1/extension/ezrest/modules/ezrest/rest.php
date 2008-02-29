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
$paramArray = array();
if ( !empty( $functionDefinition['params'] ) )
{
    foreach( $functionDefinition['params'] as $paramName )
    {
        if ( !$http->hasVariable( $paramName ) )
        {
            $errorMessage = 'Required parameter not provided: ' . $paramName;
            eZDebug::writeError( $errorMessage,
                                 'ezrest/' . $functionName );
            $hasError = true;
        }
        $paramArray[] = $http->variable( $paramName );
    }
}

// Get options.
if ( !empty( $functionDefinition['options'] ) )
{
    foreach( $functionDefinition['options'] as $name => $value )
    {
        if ( $http->hasVariable( $name ) )
        {
            $paramArray[] = $http->variable( $name );
        }
        else
        {
            $paramArray[] = $value;
        }
    }
}

$domDocument = new DOMDocument( '1.0', 'utf-8' );
$rootElement = $domDocument->createElement( 'eZREST' );
$rootElement->setAttribute( 'function', $functionName );
$domDocument->appendChild( $rootElement );

if ( !$hasError )
{
    $resultElement = call_user_func_array( array( $functionDefinition['handler'], $functionDefinition['method'] ),
                                           $paramArray );
    $resultElement = $domDocument->importNode( $resultElement, true );
    $rootElement->appendChild( $resultElement );
}
else
{
    $rootElement->appendChild( $domDocument->createElement( 'Error', $errorMessage ) );
}

ob_end_flush();

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
