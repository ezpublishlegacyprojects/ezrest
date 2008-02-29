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

/*! \file ezrestloginhandler.php
*/

/**
 *
 */
class eZRESTLoginHandler extends eZRESTBaseHandler
{
    /**
     * @reimp
     */
    public function initialize()
    {
        $moduleDefinition = new eZRESTModuleDefinition();
        $moduleDefinition->addView( 'login', array( 'method' => 'loginUser',
                                                    'functions' => 'login',
                                                    'params' => array( 'login', 'password' ) ) );
        $moduleDefinition->addFunction( 'login', array() );
        return $moduleDefinition;
    }

    /**
     * Login user, using username and password.
     *
     * @param username
     * @param password
     *
     * @return DOMElement DOMElement with Session ID. The session ID will be null
     * if the login fails.
     */
    public function loginUser( $login, $password )
    {
        $user = eZUser::loginUser( $login, $password );
        $sessionID = $user ? session_name() . '=' . session_id() : null;

        $domDocument = new DOMDocument( '1.0' );
        return $domDocument->createElement( 'SessionID', $sessionID );
    }
}

?>
