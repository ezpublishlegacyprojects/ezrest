<?php
//
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ REST
// SOFTWARE RELEASE: 1.0.x
// COPYRIGHT NOTICE: Copyright (C) 2008 eZ Systems AS
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

/**
 * eZRESTDefaultErrorHandler creates a default xml reply which will be
 * used to return info back to client in case of an error.
 * It is possible to create your own error replys by inheriting from this
 * class, overload the createDom() function and changing the ini
 * setting 'ErrorHandler'.
 */

class eZRESTDefaultErrorHandler
{
    /**
     * Constructor. Creates a new eZRESTDefaultErrorHandler.
     *
     * @param string errorMessage
     */
    public function __construct( $errorMessage )
    {
        $this->domDocument = new DOMDocument( '1.0' );

        $this->createDOM( $errorMessage );
    }

    /**
     *  Create a dom document with the following structure :
     * <Error>This is an error message</Error>
     *
     * $param string errorMessage
     *
     */
    protected function createDOM( $errorMessage )
    {
        $domElement = $this->domDocument->createElement( 'Error' );
        $domElement->appendChild( $this->domDocument->createTextNode( $errorMessage ) );
        $this->domDocument->appendChild( $domElement );

    }

    /**
     * Returns all the elements which should be included in the error reply
     *
     * @return DOMNodeList May also only return a DOMElement
     */
    public function getResponse()
    {
        return $this->domDocument->childNodes;
    }
}


?>