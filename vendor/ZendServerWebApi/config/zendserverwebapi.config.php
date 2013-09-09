<?php
return array (
  'controllers' => 
  array (
    'invokables' => 
    array (
      'webapi-api-controller' => 'ZendServerWebApi\\Controller\\ApiController',
      'webapi-target-controller' => 'ZendServerWebApi\\Controller\\TargetController',
      'webapi-app-controller' => 'ZendServerWebApi\\Controller\\AppController',
    ),
  ),
  'console' => 
  array (
    'router' => 
    array (
      'routes' => 
      array (
        'addTarget' => 
        array (
          'options' => 
          array (
            'route' => 'addTarget --target= [--zsurl=] --zskey= --zssecret=',
            'defaults' => 
            array (
              'controller' => 'webapi-target-controller',
              'action' => 'add',
              'no-target' => true,
              'zsurl' => 'http://localhost:10081',
            ),
            'info' => 
            array (
              0 => 'This command has to be executed first if you do not want to pass always the zskey zssecret and zsurl.',
              1 => 
              array (
                0 => '--target',
                1 => 'The unique name of the target',
              ),
              2 => 
              array (
                0 => '--zsurl',
                1 => 'The Zend Server URL. If not specified then it will be http://localhost:10081',
              ),
              3 => 
              array (
                0 => '--zskey',
                1 => 'The name of the API key',
              ),
              4 => 
              array (
                0 => '--zssecret',
                1 => 'The hash of the API key',
              ),
            ),
          ),
        ),
        'installApp' => 
        array (
          'options' => 
          array (
            'route' => 'installApp --zpk= --baseUri= [--userParams=] [--userAppName=] [--target=] [--zsurl=] [--zskey=] [--zssecret=]',
            'defaults' => 
            array (
              'controller' => 'webapi-app-controller',
              'action' => 'install',
            ),
            'info' => 
            array (
              0 => 'This command installs or updates an application',
              1 => 
              array (
                0 => '--zpk',
                1 => 'The zpk package file',
              ),
              2 => 
              array (
                0 => '--baseUri',
                1 => 'The baseUri of where the application will be installed',
              ),
              3 => 
              array (
                0 => '--userParams',
                1 => 'User parameters that have to formated as a query string',
              ),
              4 => 
              array (
                0 => '--userAppName',
                1 => 'Name of the application',
              ),
              5 => 
              array (
                0 => '--target',
                1 => 'The unique name of the target',
              ),
              6 => 
              array (
                0 => '--zsurl',
                1 => 'The Zend Server URL. If not specified then it will be http://localhost:10081',
              ),
              7 => 
              array (
                0 => '--zskey',
                1 => 'The name of the API key',
              ),
              8 => 
              array (
                0 => '--zssecret',
                1 => 'The hash of the API key',
              ),
            ),
            'arrays' => 
            array (
              0 => 'userParams',
            ),
          ),
        ),
      ),
    ),
  ),
  'service_manager' => 
  array (
    'factories' => 
    array (
      'zend_server_api' => 'ZendServerWebApi\\Service\\ApiManagerFactory',
    ),
    'invokables' => 
    array (
      'zpk' => 'ZendServerWebApi\\Service\\ZpkInvokable',
    ),
  ),
  'zsapi' => 
  array (
    'file' => '/.zsapi.ini',
    'default_target' => 
    array (
      'zsurl' => 'http://localhost:10082',
      'zskey' => 'admin',
      'zssecret' => 'd2cc2bd7a8252700b51867ce1c7195ba97300ab42e3a400465c495fa91546200',
    ),
    'client' => 
    array (
      'adapter' => '\\ZendServerWebApi\\Model\\Http\\Adapter\\Socket',
    ),
  ),
);
