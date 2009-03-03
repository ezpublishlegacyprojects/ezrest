<?php /*

[RESTSettings]
# List of eZ REST handlers. The handlers listed are
# class names extending the eZRestHandler class.
#
# eZ REST handlers must be registered under restserver/_handler_name_.php
# HandlerList[]

# Login handler is provided by this extension.
HandlerList[]=eZRESTLoginHandler

# Define a class which should generate the reply in case of an error. 
# Class needs to implement function getResponse()
ErrorHandler=eZRESTDefaultErrorHandler

# List of extensions including eZ REST handlers.
# Extensions for eZ REST handlers.
# eZ REST handlers must be registered under restserver/_handler_name_.php
RESTExtensions[]

[RESTClientSettings]
# REST client connection timeout
ConnectionTimeout=30
# Verify host and certificate
VerifyHost=enabled

*/ ?>
