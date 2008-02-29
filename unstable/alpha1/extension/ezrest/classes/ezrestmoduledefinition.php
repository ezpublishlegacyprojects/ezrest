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

/*! \file ezrestmoduledefinition.php
*/

/**
 * eZRESTModuleDefinition contains a definition of $ViewList and $FunctionList for
 * module.php files.
 *
 * Example usage:
 * <code>
 * $moduleDefinition = new eZRESTModuleDefinition();
 * $moduleDefinition->addView( 'item', array( 'functions' => array( 'admin' ),
 *                                            'method' => 'handleItem',
 *                                            'params' => array(), // params are required parameters.
 *                                            'options' => array( 'offset' => 0, 'limit' => 10 ) ) ); // options are optional parameters, and default values must be provided.
 * $moduleDefinition->addFunction( 'admin', array() );
 * </code>
 *
 * Note, the 'script' parameter normally used in module.php is replaced by the
 * handler, and the method name.
 */
class eZRESTModuleDefinition
{
    /**
     * Constructor
     */
    function eZRESTModuleDefinition()
    {
        $this->FunctionList = array();
        $this->ViewList = array();
    }

    /**
     * Add veiw to module definition.
     *
     * @param string View name
     * @param array View definition.
     */
    public function addView( $name, array $definition )
    {
        $this->ViewList[$name] = $definition;
    }

    /**
     * Add function to module definition.
     *
     * @param string Function name.
     * @param array Function definition.
     */
    public function addFunction( $name, array $definition )
    {
        $this->FunctionList[$name] = $definition;
    }

    /**
     * Get function list
     *
     * @return array Function list
     */
    public function getFunctionList()
    {
        return $this->FunctionList;
    }

    /**
     * Get view list
     *
     * @return array View list
     */
    public function getViewList()
    {
        return $this->ViewList;
    }

    protected $FunctionList;
    protected $ViewList;
}

?>
