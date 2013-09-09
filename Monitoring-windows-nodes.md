# How to monitor Windows server #

This tutorial explain how to use Zend Server Nagios PLugin to monitor Zend Server  on a Windows environment.

## Setting the windows Nagios client ##

To have an overview of the windows monitoring process look at the Nagios documentation :
[http://nagios.sourceforge.net/docs/3_0/monitoring-windows.html](http://nagios.sourceforge.net/docs/3_0/monitoring-windows.html)

**Install a monitoring agent**

To monitor Windows servcies you have to install a moinitoring agent. For this exemple we will install a NSC++ client.

Download it and install it on the Windows server: 
[http://www.nsclient.org/nscp/dowloads](http://www.nsclient.org/nscp/dowloads)

During the installation process you will be asked for the Nagios server IP, a password for the agent and if you want to install other modules. nrpe server and check_nt are mandatory for our purpose.


**Install Zend Server 6.x**

Upload and run the msi installer.


## Configure the Nagios server ##

**Setting the windows host into Nagios hosts**

Create a file called /etc/nagios3/template.cfg and define a new template :

	define host {
		name windows-server
		use generic-host
	}

Update the Nagios configuration file : /etc/nagios3/nagios.cfg  Uncomment, modify or create the line 
	
	cfg_file=/etc/nagios3/template.cfg

Uncomment, modify or cretae the line

	cfg_file=/etc/nagios3/windows.cfg

Copy the sample file /usr/share/doc/nagios3-common/examples/template-object/windows.cfg into /etc/nagios3/

Restart the Nagios server

	service nagios3 restart

Now you should have a additional host available on the UI.

**Troubleshooting**

If you have some troubles check the commands define by the check_nt plugin. Edit the file /etc/nagios-plugins/config/nt.cfg by adding '$ARG2$' at the end of the command_line definition : 

	command_line	/usr/lib/nagios/plugins/check_nt -H $HOSTADDRESS$ -V $ARG1$ $ARG2$

If you still have trouble try to force the port value :

	-p [windows ncsp port]

If you have set a password to the NSC++ client add it to the command line as well :

	-s [password]

## Install Zend Server Nagios plugin on the windows client ##

**Install the plugin sources**

Go to github : [https://github.com/zendtech/ZendServerNagiosPlugin](https://github.com/zendtech/ZendServerNagiosPlugin "https://github.com/zendtech/ZendServerNagiosPlugin") and get the plugin as a .zip file.

Unzip it where ever you want on the windows client. By exemple C:\Program Files\ZSNagiosPlugin.

On windows system, PHP will not be able to found the ZF2 library of the Zedn Server. To do that, edit the file win.zf2.conf.php and set the path to the ZF2 library. By example :

	putenv('ZF2_PATH=C:\Program Files\Zend\ZendServer\share\ZendFramework2\library');



