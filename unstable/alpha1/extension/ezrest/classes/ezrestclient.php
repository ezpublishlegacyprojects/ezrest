<?php
//
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ Find
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

/*! \file ezrestclient.php
*/

/**
 * The eZRESTClient contains functionality for connecting to REST
 * web services, and perform operations.
 *
 * Example usage:
 * <code>
 * $client = new eZRESTClient( 'http://example.com/ezrest/login' );
 * $client->addParameter( 'login', 'admin' );
 * $client->addParameter( 'password', 'publish' );
 * $domLogin = new DOMDocument( '1.0' );
 * $domLogin->loadXML( $client->send() ); // parameters are reset when send is invoked.
 * $sessionID1 = $domLogin->getElementsByTagName( 'SessionID' )->item( 0 )->nodeValue;
 *
 *
 * $client2 = new eZRESTClient( 'http://example.com/ezrest/user_create', $sessionID1 );
 * $client2->addParameter( 'username', 'balle' );
 * $client2->addParameter( 'password', 'klorin' );
 * $client2->addParameter( 'email', 'balle@klorin.no' )
 *
 * echo $client2->send();
 * </code>
 */
class eZRESTClient
{
    /**
     * Constructor. Creates a new eZRESTClient.
     *
     * @param string URL
     * @param string Session ID ( optional ).
     */
    function eZRESTClient( $url, $sessionID = null )
    {
        $this->URL = $url;
        $this->SessionID = $sessionID;
        $this->RestINI = eZINI::instance( 'ezrest.ini' );
        $this->reset();
    }

    /**
     * Send eZRESTClient request. The request
     *
     */
    public function send()
    {
        $url = $this->URL;
        if ( !empty( $this->GetParameterList ) )
        {
            $url .= '?';
            foreach( $this->GetParameterList as $parameter )
            {
                $url .= $parameter['name'] . '=' . urlencode( $parameter['value'] ) . '&';
            }
        }

        $postData = null;
        if ( !empty( $this->PostParameterList ) )
        {
            $postData = '';
            foreach( $this->PostParameterList as $parameter )
            {
                $postData .= $parameter['name'] . '=' . urlencode( $parameter['value'] ) . '&';
            }
        }

        if ( !empty( $this->PostData ) )
        {
            $postData = $this->PostData;
        }

        return $this->sendHTTPRequest( $url, $postData );
    }

    /**
     * Send HTTP request. This code is based on eZHTTPTool::sendHTTPRequest, but contains
     * Some improvements. Will use Curl, if curl is present.
     *
     * @param string URL
     * @param string Post data ( optional )
     *
     * @return string  HTTP result ( without headers ), null if the request fails.
    */
    protected function sendHTTPRequest( $url, $postData = null )
    {
        $connectionTimeout = $this->RestINI->variable( 'RESTClientSettings', 'ConnectionTimeout' );

        if ( extension_loaded( 'curl' ) )
        {
            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_URL, $url );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $connectionTimeout );
            if ( $postData !== null )
            {
                curl_setopt( $ch, CURLOPT_POST, 1 );
                curl_setopt( $ch, CURLOPT_POSTFIELDS, $postData );
                if ( $contentType != '' )
                {
                    curl_setopt( $ch, CURLOPT_HTTPHEADER, array ( 'Content-Type: ' . $contentType ) );
                }
                if ( $this->SessionID )
                {
                    curl_setopt( $ch, CURLOPT_HTTPHEADER, array ( 'Cookie: ' . $this->SessionID ) );
                }
            }

            $data = curl_exec( $ch );
            $errNo = curl_errno( $ch );
            curl_close( $ch );
            if  ( $errNo )
            {
                eZDebug::writeError( 'curl error: ' . $errNo, 'eZSolr::sendHTTPRequest()' );
                return null;
            }
            else
            {
                return $data;
            }
        }
        else
        {
            preg_match( "/^((http[s]?:\/\/)([a-zA-Z0-9_.]+))?:?([0-9]+)?([\/]?[~]?(\.?[^.]+[~]?)*)/i", $url, $matches );
            $protocol = $matches[2];
            $host = $matches[3];
            $port = $matches[4];
            $path = $matches[5];
            if ( !$path )
            {
                $path = '/';
            }

            if ( $protocol == 'https://' )
            {
                $filename = 'ssl://' . $host;
            }
            else
            {
                $filename = 'tcp://' . $host;
            }

            // make sure we have a valid hostname or call to fsockopen() will fail
            $parsedUrl = parse_url( $filename );
            $ip = isset( $parsedUrl[ 'host' ] ) ? gethostbyname( $parsedUrl[ 'host' ] ) : '';
            $checkIP = ip2long( $ip );
            if ( $checkIP == -1 or $checkIP === false )
            {
                eZDebug::writeDebug( 'Could not find hostname: ' . $parsedUrl['host'], 'eZSolr::sendHTTPRequest()' );
                return null;
            }

            $errorNo = 0;
            $errorStr = '';
            $fp = @fsockopen( $filename, $port, $errorNo, $errorStr, $connectionTimeout );
            if ( !$fp )
            {
                eZDebug::writeDebug( 'Could not open connection to: ' . $filename . ':' . $port . '. Error: ' . $errorStr,
                                     'eZSolr::sendHTTPRequest()' );
                return null;
            }

            $method = 'GET';
            if ( $postData !== null )
            {
                $method = 'POST';
            }

            $request = $method . ' ' . $path . ' ' . 'HTTP/1.1' . "\r\n";
            $request .= "Host: $host\r\n" .
                "Accept: */*\r\n" .
                'Content-type: ' . $this->ContentType . "\r\n" .
                "Content-length: " . strlen( $postData ) . "\r\n" .
                "User-Agent: eZ REST\r\n" .
                "Pragma: no-cache\r\n";
            if ( $this->SessionID )
            {
                $request .= 'Cookie: ' . $this->SessionID . "\r\n";
            }
            $request .= "Connection: close\r\n\r\n";
            fputs( $fp, $request );
            if ( $method == 'POST' )
            {
                fputs( $fp, $postData );
            }

            $header = true;
            while( $header )
            {
                while ( !feof( $fp ) )
                {
                    $character = fgetc( $fp );
                    if ( $character == "\r" )
                    {
                        fgetc( $fp );
                        $character = fgetc( $fp );
                        if ( $character == "\r" )
                        {
                            fgetc( $fp );
                            $header = false;
                        }
                        break;
                    }
                }
            }

            $buf = '';
            while ( !feof( $fp ) )
            {
                $buf .= fgets( $fp, 128 );
            }
            fclose($fp);

            return $buf;
        }
    }

    /**
     * Reset parameters
     */
    public function reset()
    {
        $this->GetParameterList = array();
        $this->PostParameterList = array();
        $this->PostData = null;
    }

    /**
     * Add parameter to next client request.
     *
     * @sa eZRESTClient::addGetParameter
     *
     * @param string Parameter name
     * @param string Parameter value
     */
    public function addParameter( $name, $value )
    {
        $this->addGetParameter( $name, $value );
    }

    /**
     * Add GET parameter to next client request
     *
     * @param string Parameter name
     * @param string Parameter value
     */
    public function addGetParameter( $name, $value )
    {
        $this->GetParameterList[] = array( 'name' => $name,
                                           'value' => $value );
    }

    /**
     * Add POST parameter to next client request
     *
     * @param string Parameter name
     * @param string Parameter value
     */
    public function addPostParameter( $name, $value )
    {
        $this->PostParameterList[] = array( 'name' => $name,
                                            'value' => $value );
    }

    /**
     * Add HTTP Post data directly
     *
     * @param strinf Post data
     */
    public function setPostData( $postData )
    {
        $this->PostData = $postData;
    }

    /**
     * Set request content type
     *
     * @param string HTTP resquest content type
     */
    public function setContentType( $contentType )
    {
        $this->ContentType = $contentType;
    }

    /**
     * Set sessionID
     *
     * @param string SessionID
     */
    public function setSessionID( $sessionID )
    {
        $this->SessionID = $sessionID;
    }

    var $URL;
    var $SessionID;
    var $GetParameterList;
    var $PostParameterList;
    var $PostData;
    var $ContentType;
    var $RestINI;
}

?>
