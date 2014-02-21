Zend Server Nagios plugin 2.0
=============================

Introduction
------------

Nagios Core is an Open Source system and network monitoring application.
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
By exemple the probe "clusterstatus" will return a warning if more than 10% of nodes are down within your cluster.

Probes definitions
------------------

Zend Server Nagios Plugin provides Nagios command to be used by nrpe-server to return information to Nagios 3 server. These probes are also available as PHP cli scripts using :

	php /nagiosplugin/directory/index.php nagiosplugin [command]

Each probe return a severity level and a comment which is displayed in Nagios console. Severity threshold can be modify.
Zend Server Nagios Plugin 2.0 probes are able to remember the last time they have been hit by a Nagios Server request. So, when an alert has already been sent on the previous hit it will not be sent again.

These probes are :

- __clusterstatus__: Check the number of available nodes in cluster. Severity level is based on the rate of down servers.

- __audittrail__ : Check last audit events (Zend Server 6 Enterprise edition only). Severity level is based on the highest severity level found among the returned events.

- __event-cluster__ : check last events detected by Zend Monitor on the whole cluster. Severity level is based on the highest severity level found among the returned events.

- __event-node__ : check last events detected by Zend Monitor on the current node. Severity level is based on the highest severity level found among the returned events.

- __notifications__ : Check last notifications content. Severity level is based on the notification type.

- __license__ : Throw an alert if the license is about to expire. Severity level is based on the remaining time.

- __jobqueue__ : Check failed jobs. Severity level depends on the number of failed jobs.

- __daemonsprobe-node__ : Check if some daemons are in a bad status on the current node. Severity is based on daemon status.

- __daemonsprobe-cluster__ : Check if some daemons are in a bad status on the whole cluster. Severity is based on daemon status.

- __processingtime-cluster__ : Check average processing time. Severity level depends of average processing time.


Setting Nagios threshold
------------------------
All probe manage thresholds in order to define the severity level.
These thresholds are set in the /config/autoload/thresholds.local.php file.
The way to setting theshold is very easy :

	<thresholdValue> => Nagios severity level (NAGIOS_CRITICAL, NAGIOS_WARNING, NAGIOS_NOTICE or NAGIOS_OK)

Installation
------------
__Using Zend Deployment__

This is the easyest way to deploy Zend Server Nagios Plugin because it will deploy it on every nodes in one operation. Deployment will also start to configure the plugin.

In order to install Zend Server Nagios Plugin :

- Grab or create the plugin zpk package.
- Create a new web api key. Call it "nagios" by example. It has to be an "admin" key. 
- Copy the key hash in your clipboard
- Deploy the package. You do not care about the url.
- Fill missing data in the deployment form (now you understand why I asked you to copy the hash in the clipboard...)
- Launch deployment.

To finish, run as root following installation command on each node :

	php /usr/local/zend/var/app/http[..depends on deployment vhost...]/index.php nagiosplugin install-node

This command will set up the database (if needed) and create the nrpe-server configuration (/etc/nagios/nrpe.cfg).

Now you're ready to monitor your cluster !

By the way, this installation process can be easily automated.


__Using composer__

Install Zend Server Nagios Plugin sources using :

	php composer.phar create-project zend-patterns/zendservernagiosplugin path 2.0.*

To be efficient, generate a zpk package from this code and follow the previous instructions.


How to use it within Nagios 3 ?
-------------------------------

The best practices are :

- attach node-related commands to each host you monitor (each node).
- define a Nagios host for the whole cluster as well and attach "cluster" related commands to it (Commands which have no "node" within their name).

Doing that you can monitor cluster and nodes separately.

Define the command  into your /usr/local/nagios3/commands.cfg file. You will find the command_line value into /etc/nagios/nrpe.cfg (on monitored node). This file has been created during deployment process.

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
    
    
    