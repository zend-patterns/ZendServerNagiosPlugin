Zend Server Nagios Plugin
=======================

Introduction
------------
This PHP ZF2 application can be used as a Nagios plugin to monitor main Zend Server metrics.
The plugin is based on a command line tool which returns the severity level of the given probe. 
It also yields a message that will be recorded into Nagios.
For example, the probe "clusterstatus" will return a warning if 33% of the nodes in your cluster are down.

Command Line Operations and Probe Definitions
----------------------------------------------
A script for Windows and Linux is provided in the /bin directory.
To invoke the Nagios plugin command, simply use:
    php /path_to_plugin/index.php nagiospluging <probe> [parameters]

__clusterstatus__ (no parameter) : 
Returns the status of your cluster. Severity depends on the number of nodes that are up or down.

__audittrail [--delay=] [--limit=]__ :
Returns the severity of the last audit trail.
--delay : the time period (in seconds) for which the probe looks for audit trails. It should be synchronized with the check_interval value of Nagios.
--limit : the maximum number of items to be checked out.
The severity level is based on the severity of the most critical collected item.

__notifications (no parmeters)__ :
This probe look into the current notifications sent by the cluster.
The severity level is based on the severity of the most critical notification.

__licence (no parameter)__ :
Checks licence validity.
The severity level is based on the licence time validity remaining.

__events [--delay=] [--limit=]__ :
Returns the severity of the last monitor events.
--delay : the time period (in seconds) for which the probe looks for events. It should be synchronized with the check_interval value of Nagios.
--limit : the maximum number of items to be checked out.
The severity level is based on the severity of the most critical collected event.


Setting Nagios Threshold
------------------------
All probes manage thresholds in order to define the severity level.
The thresholds are set in the /config/zendservernagiosplugin.config.php file.
The way to setting a theshold is quite simple:
<thresholdValue> => Nagios severilty level (NAGIOS_CRITICAL, NAGIOS_WARNING, NAGIOS_NOTICE or NAGIOS_OK)


Installation
------------

Using Git Submodules
--------------------
Clone the code from Github:

    git clone git://github.com/zendtech/ZendServerNagiosPlugin.git --recursive
    
Install dependencies with Composer: 

	composer install

How to use it within Nagios?
---------------------------
Define the command  into your /usr/local/nagios3/commands.cfg file: 

    define command {
	    command_name : zend-server-<probe>
	    command_line : php /path_to_nagios_pluigin/index.php nagiosplugin <probe> [parameters]
    }
    
Define the service into your host configuration (ie : /usr/local/nagios3/conf.d/local-xxx.cfg): 

    define service {
        use                             generic-service 
        host_name                       localhost
        service_description             Zend_Server_Error
        check_command                   zend-server-<probe>
    }   
    
Then restart Nagios:

    /etc/init.d/nagios3 restart
    
    
    
