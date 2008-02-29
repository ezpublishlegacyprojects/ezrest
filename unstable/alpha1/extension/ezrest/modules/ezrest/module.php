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

$Module = array( 'name' => 'eZREST' );

$ViewList = array();
$FunctionList = array();

$restINI = eZINI::instance( 'ezrest.ini' );

// Loop through REST handlers, and initialize them.
foreach( $restINI->variable( 'RESTSettings', 'HandlerList' ) as $handlerName )
{
    $handler = new $handlerName();
    $handlerResult = $handler->initialize();
    $handlerViewList = $handlerResult->getViewList();

    foreach( $handlerViewList as $view => $definition )
    {
        $handlerViewList[$view]['handler'] = $handler;
    }
    $ViewList = array_merge( $ViewList, $handlerViewList );
    $FunctionList = array_merge( $FunctionList, $handlerResult->getFunctionList() );
}

foreach( $ViewList as $view => $definition )
{
    $ViewList[$view]['script'] = 'rest.php';
}

?>
