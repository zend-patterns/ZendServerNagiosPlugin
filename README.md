Zend Server Nagios plugin
=======================

Introduction
------------
This PHP ZF2 application can be used as a Nagios plugins to monitor main Zend Server metrics.
The plugin is based on a command line tool which returns the severity level of the given probe. 
It also yield a message that will be recorded into Nagios.
By exemple the probe "clusterstatus" will return a warning if 33% of nodes are down within your cluster.

Command line operations and probes definitions
----------------------------------------------
A scripts for Windows and Linux is provide in the /bin directory.
To invoke the Nagios plugin command simply use :
    ZSNagiosPlugin <probe> [parameters]

Clusterstatus (no parameter) : 
Return the status of your cluster. Severity depends on the number of nodes that are up or down.

Audittrail [--delay=] [--limit=] :
Return the severity of the last audit trail.
--delay : the time period (in second) in which the probe will looking for audit trails. It should be synchronized with the check_interval value of Nagios.
--limit : the maximal number of item that will be checked out.
The severity level is based on the severity of the most critical item that have been collected.

Notifications (no parmeters) :
This probe look into the current notifications sent by the cluster.
The severity level is based on the severity of the most critical notification.

Licence (no parameter) :
This probe check the licence validity.
The severity level is based on the licence time validity remaining

Events [--delay=] [--limit=] :
Return the severity of the last monitor events.
--delay : the time period (in second) in which the probe will looking for events.It should be synchronized with the check_interval value of Nagios.
--limit : the maximal number of item that will be checked out.
The severity level is based on the severity of the most critical event that have been collected.


Setting Nagios threshold
------------------------
All probe manage thresholds in order to define the severity level.
Thes thresholds are set in the /config/zendservernagiosplugin.config.php file.
The way to setting theshold is very easy :
<thresholdValue> => Nagios severilty level (NAGIOS_CRITICAL, NAGIOS_WARNING, NAGIOS_NOTICE or NAGIOS_OK)


Installation
------------

Using Git submodules
--------------------
Clone the code from Github :
    git clone git://github.com/zendtech/ZendServerNagiosPlugin.git --recursive
Install dependencies with Composer : 
	composer install

How to use it within Nagios ?
---------------------------
Define the command  into your commands.cfg file: 
    define command {
	    command_name : zend-server-<probe>
	    command_line : ZSNagiosPlugin <probe> [parameters]
    }
    
Define the service into your host configuration : 
    define service {
        use                             local-service ; Name of service template to use
        host_name                       localhost
        service_description             Zend_Server_Error
        check_command                   zend-server-<probe>
        check_interval                  5 ; check every 5 minutes (if you havent changed your time units)
        register                        1
        active_checks_enabled           1
        retry_interval                  1 ; retry every minute when in an error state
        max_check_attempts              4 ; test 4 times before deciding the hard state
        contact_groups                  admins ; contact the admin with errors	
    }   
    
    
    
    