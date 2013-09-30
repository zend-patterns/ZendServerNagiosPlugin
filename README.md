Zend Server Nagios plugin
=========================

Introduction
------------

Nagios� Core� is an Open Source system and network monitoring application.
It watches hosts and services that you specify, alerting you when things go bad and when they get better.

Some of the many features of Nagios Core include:

- Monitoring of network services (SMTP, POP3, HTTP, NNTP, PING, etc.)
- Monitoring of host resources (processor load, disk usage, etc.)
- Simple plugin design that allows users to easily develop their own service checks
- Parallelized service checks
- Ability to define network host hierarchy using "parent" hosts, allowing detection of and distinction between hosts that are down and those that are unreachable
- Contact notifications when service or host problems occur and get resolved (via email, pager, or user-defined method)
- Ability to define event handlers to be run during service or host events for proactive problem resolution
- Automatic log file rotation
- Support for implementing redundant monitoring hosts
- Optional web interface for viewing current network status, notification and problem history, log file, etc.

This PHP ZF2 application can be used as a Nagios plugins to monitor main Zend Server metrics.
The plugin is based on a command line tool which returns the severity level of the given probe. 
It also yield a message that will be recorded into Nagios.
By exemple the probe "clusterstatus" will return a warning if 33% of nodes are down within your cluster.

Command line operations and probes definitions
----------------------------------------------
A scripts for Windows and Linux is provide in the /bin directory.
To invoke the Nagios plugin command simply use :
    php /path_to_plugin/index.php nagiospluging <probe> [parameters]

__clusterstatus__ (no parameter) : 
Return the status of your cluster. Severity depends on the number of nodes that are up or down.

__audittrail [--delay=] [--limit=]__ :
Return the severity of the last audit trail.
--delay : the time period (in second) in which the probe will looking for audit trails. It should be synchronized with the check_interval value of Nagios.
--limit : the maximal number of item that will be checked out.
The severity level is based on the severity of the most critical item that have been collected.

__notifications (no parmeters)__ :
This probe look into the current notifications sent by the cluster.
The severity level is based on the severity of the most critical notification.

__license (no parameter)__ :
This probe check the license validity.
The severity level is based on the license time validity remaining

__events [--delay=] [--limit=]__ :
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
    
Then restart Nagio :

    /etc/init.d/nagios3 restart
    
    
    