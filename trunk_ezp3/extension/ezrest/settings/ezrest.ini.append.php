<?php /*


[RESTSettings]
# List of eZ REST handlers. The handlers listed are
# class names extending the eZRestHandler class.
# HandlerList[]

# Login handler is provided by this extension.
HandlerList[]=eZRESTLoginHandler

# Define a class which should generate the reply in case of an error. 
# Class needs to implement function getResponse()
ErrorHandler=eZRESTDefaultErrorHandler

[RESTClientSettings]
# REST client connection timeout
ConnectionTimeout=30


*/ ?>