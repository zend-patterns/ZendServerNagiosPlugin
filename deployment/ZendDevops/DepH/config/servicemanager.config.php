<?php
/**
 * This file override the configuration of the deployment manager and the deployment context
 */
return array(
        //Deployment manager configuration
        'invokables' => array(
                /*'SharedEventManager' => 'Zend\EventManager\SharedEventManager',
                'Deployment' => 'Zx\DepH\Deployment\Deployment',
                'Shell' => 'Zx\DepH\SystemCall\Shell'*/
        ),
        'factories' => array(
                'deployment_context' => 'ZendDevops\DepH\Service\ContextFactory',
                'log' => 'ZendDevops\DepH\Service\LogFactory',
                'debugger' => 'ZendDevops\DepH\Service\DebuggerFactory',
                'template_manager' => 'ZendDevops\DepH\Service\TemplateManagerFactory',
                'configuration_helper' => 'ZendDevops\DepH\Service\ConfigurationHelperFactory',
        ),
);