## Introduction ##

This tutorial explains how to set up a Nagios-monitored Zend Server using the Zend Server Nagios plugin, and how to configure the thresholds to personalize your alert severity levels.

Nagios is an open source system and network monitoring application. It watches hosts and services that you specify, alerting you when things go bad and when they get better.

Nagios defines probes named "services" that forward any information about a specific operating system or application metrics to the Nagios server. Nagios comes with plenty of built-in services, but it is easy to plugin additional services.

The Zend Server Nagios plugin has been designed to allow Nagios to monitor the main Zend Server metrics, such as: cluster node status, monitoring events, notifications, etc.

**Client / Server Architecture**

Nagios architectures are generally based on a central Nagios server collecting information from services hosted on Nagios clients. In this tutorial, the Nagios clients and the Nagios server are hosted by the same machine.

**About the Zend Server Nagios Plugin**

This plugin is a ZF2-based PHP CLI application. You can use it directly from the command line, and manually check the health of your system: 
index.php nagiosplugin <command> arguments.

Available commands are : 

- *clusterstatus* : monitors the percentage of cluster nodes currently down 
- *audittrail* : monitors Zend Server Audit Trail records
- *notifications* : monitors Zend Server notifications
- *licence* : monitors licence expiration delay
- *events* : monitors Zend Server monitoring events

## 1. Installing your System ##

The first step in this tutorial is to install Zend Server 6.x, Nagios, and the plugin.

**Installing Zend Server 6.x**

For detailed instructions on installing Zend Server 6.x, see: 	

http://files.zend.com/help/Zend-Server-6/zend-server.htm#installation_guide.htm

Important!
Before continuing, you will need to set your timezone by editing the 'date.timezone' PHP directive. You can do this using the Zend Server UI, on the Configurations | PHP page. 

**Installing Nagios**

Use apt-get to install Nagios. Run the following command:

apt-get install nagios3 nagios-plugins nagios-nrpe-plugin nagios-nrpe-server

During the installation process, you will be asked for samba workgroup and WINS Settings.  Just leave the default settings.

You will also be asked to set the nagiosadmin password.
    
You should now be able to log in at: http://myhost/nagios3/ with the username nagiosadmin and the password you just set.

In the Service section, you will see that Nagios already provides a basic configuration for the localhost.

Note:
For this tutorial, we have to install packages for both the server and client side of Nagios on the same machine. In normal circumstances though: 

- nagios3 and nagios-nrpe-plugin are used by the Nagios server
- nagios-nrpe-server is used by the Nagios client
- nagios-plugins is used by both sides of Nagios

**Installing and Configuring the Plugin**

To install the Zend Server Nagios plugin: 

1. Unzip the application file in a directory. For example:
 /usr/local/nagiosplugin.

2. Open the '/usr/local/nagiosplugin/vendor/zendserverwebapi/zendserverwebapi/congif.zendserverwebapi.config.php' file, and modify the 'zsapi' part of the configuration as follows: 

	//Zend Server API specific Settings
    	'zsapi' => array (
    	// Default Zend Server Target
    		'default_target' => array(
    			'zsurl' => [Zend Server host],
    			'zskey' => '[API Key name]',
    			'zssecret' => '[API secret key]',
    			'zs-version' => '[Zedn Server version]',
    		),
    	),

3. You can now access the plugin from the command line. To do this, run:

php /usr/local/nagiosplugin/index.php nagiosplugin clustserstatus

## 2. Configuring the Nagios Client ##

The next step is to connect the Zend Server plugin to the Nagios server. The main operation is to define a command by adding a new definition into the nrpe-server configuration file. To do that simply edit the file */etc/nagios/nrpe.cfg*, and add a new command :

	# Zend server commands
	command[zs-cluster-status] = /usr/local/nagiosplugin/index.php nagiosplugin cluster status

Then restart the nrpe server :

	service nagios-nrpe-server restart

Now the client layer is ready to send informations to the main Nagios server. In this exemple the Nagios client can forward information about the cluster status using the command "zs-cluster-status". 

Now let see how this command will be used by the Nagios server.

# 3. Configuring the Nagios Server #

**Define command**

The command we've just defined on the client side has to be setted on the server side. For that, edit the commands.cfg file. It is generally placed in the */etc/nagios3* directory.

Edit the commands.cfg file and add the new command :

	define command{
        command_name    zs-cluster-status
		command_line $USER1$/check_nrpe -H $HOSTADDRESS$ -c zs-cluster-status
    }

As you can see, the command set on the client is used throught the nrpe plugin: The command setted on the server side is *nrpe_check* and not directly the zs-cluster-status.

**Define service**

Finally we have to define a service to use the command.
This service will be setted for the localhost, so, we have to edit the */etc/nagios3/conf.d/localhost_nagios3.cfg* and created a new service :

	define service {
		use	generic-service
		host_name	localhost
		service_description	ZS Cluster Status
		check_command	zs-cluster-status
	}

Then restart the nagios Server

	service nagios3 restart

The new service is now available. Check it on the Nagios web console : *http://myhost/nagios3/*. Nagios will chech its services evry 1O minutes. Be patient.

## 4. Setting Zend Server Nagios Plugin Thresholds ##

Nagios manage 3 levels of criticity :

- OK
- WARNING
- CRITICAL

The goal of this part is to show how Nagios criticity levels depend on plugin configurations.

As an exemple we will use the *Notifications* command of the plugin. This command is based on the notification system of Zend Server that have its own criticity level : 0,1,2 where 2 is the highest level of severity. By default the criticity level returned to Nagios is the one of the notification owning the highest severity level.
So, if one notification returned by Zend server reach the severity level 2, a critical alert will be display by Nagios.
This behaviour can be change by editing the *zendservernagiosplugin.config.php* file setted in */usr/local/nagiospluging/module/ZendServerNagiosPlugin/config*. If you look into this file you will find these lines :

	/*
     * Notifications
     * Threshold based on the inner notification severity level
     */
    'notifications' => array(
        '0' => 'NAGIOS_OK',
        '1' => 'NAGIOS_WARNING',
        '2' => 'NAGIOS_CRITICAL'
     ),

As you can see the "2" severity level of the plugin correspond to NAGIOS_CRITICAL level.


**Set the notification monitoring service**

Has you have formely do:

1. Define the command in the nrpe server by editing the file /etc/nagios/nrpe.cfg :

		command[zs-notifications]=/usr/local/nagiosplugin/index.php nagiosplugin notifications

2. Define the command in the /usr/nagios3/commands.cfg files :

		define command{
			command_name zs-notifications
			command_line	$USER1$/check_nrpe -H $HOSTADDRESS$ -c zs-notifications
		}

3. Define the service using this command in the /etc/nagios3/conf.d/localhost_nagios3.cfg :

		define service {
			use	generic-service
			host_name	localhost
			service_description	ZS Notifications center
			check_command	zs-notifications
		}

After restarted properly nrpe-server and nagios, the new service is fully available.

**Generate a notification**

In the Zend Server UI, modify and save a PHP directives value, but do not restart the server.

Then go on the Nagios UI, you will see a warning alert "Restart is required" attached to the ZS Notifications center service.

**Change the criticity level**

Now we are going to change the criticity level of the Nagios alert for this service.

Edit the zendservernagiosplugin.config.php. You will found it in /usr/local/nagiosplugin/module/ZednServerNagiosPlugin/config.
Look into the file and modify the "notifications" threshold parameters :

	/*
     * Notifications
     * Threshold based on the inner notification severity level
     */
    'notifications' => array(
            '0' => 'NAGIOS_OK',
            '1' => 'NAGIOS_WARNING', 
            '2' => 'NAGIOS_CRITICAL' 
    ),

Change the line 

	'1' => 'NAGIOS_WARNING',
into 

	'1' => 'NAGIOS_CRITICAL',

After the next Nagios check the criticity level of ZS Notifications center will be CRITICAL instead of WARNING. Check it on Nagios UI.



