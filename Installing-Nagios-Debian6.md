## Introduction ##

This tutorial as been designed to show you how to use Zend Server Nagios Plugin.
Through this tutorial you will see how to install a basic Nagios system and monitoring some Zend Server metric.

## Install your system ##

**Install Zend Server 6.x**

Install Zend Server 6.x as usual.
If you need help for this step see : 
	
	http://files.zend.com/help/Zend-Server-6/zend-server.htm#installation_guide.htm

Don't forget to set a timezone.

**Use apt-get to install Nagios:**

	apt-get install nagios3 nagios-plugins nagios-nrpe-plugin nagios-nrpe-server

In the process of installing you get asked for samba workgroup and WINS Settings just let these set on default.

You will also asked to set the nagiosadmin password.
    
After this you should be able to login to: http://myhost/nagios3/ with the username nagiosadmin and the password you just set before.

If you go to the service detail site you will see that Nagios provides already a basic configuration for the localhost.

**Deploy the plugin application**

Download the .zpk file and deploy the plugin with Zend Server Deployment Component.

**Set up the plugin configuration**

The Nagios plugin will use the Zend Server Web API.

Identity the directory where the plugin has been deployed. It will be setted in a subdirectory of */usr/local/zend/var/apps/http* depending of the plugin version and the way you've deployed it.


Look for the *zendserverwebapi.config.php* file the *vendor/zendserverwebapi/zendserverwebapi/config* directory and add or modifiy the Zend Server Web Api 'zsapi' key definition :

	//  Zend Server API specific Settings
    'zsapi' => array (
        // Default Zend Server Target
        'default_target' => array(
           'zsurl' => 'http://localhost:10081',
            'zskey' => 'admin',
            'zssecret' => '[admin api secret key]',
            'zs-version' => '6.1',
        ),

Now your able to reach the plugin in the command line :

	php [application_directory]/index.php nagiosplugin clustserstatus

## Configure Nagios ##

**Set up the Nagios command**

In order to monitor your system Nagios use commands and these command , which are some kind of alias, have to be setted in the commands.cfg file.
To let Nagios know about our plugin, edit the commands.cfg file. It is generally placed in the */etc/nagios3* directory.

Edit the commands.cfg file and add the new command :

	define command{
        command_name    clusterstatus
        command_line    php [application_directory]/index.php nagiosplugins clusterstatus
    }

This command will ask Zend Server to return the percentage of nodes that are down in the cluster.





