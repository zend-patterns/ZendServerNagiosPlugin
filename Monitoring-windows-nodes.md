# How to monitor Windows server #

This tutorial explain how to use Zend Server Nagios PLugin to monitor Zend Server  on a Windows environment.

## Setting the windows Nagios client ##

To have an overview of the windows monitoring process look at the Nagios documentation :
[http://nagios.sourceforge.net/docs/3_0/monitoring-windows.html](http://nagios.sourceforge.net/docs/3_0/monitoring-windows.html)

** Install a monitoring agent **
To monitor Windows servcies you have to install a moinitoring agent. For this exemple we will install a NSC++ client.

Download it and install it on the Windows server: 
[http://www.nsclient.org/nscp/dowloads](http://www.nsclient.org/nscp/dowloads)

During the installation process you will be asked for the Nagios server IP, a password for the agent and if you want to install other modules. nrpe server and check_nt are mandatory for our purpose.

** Install Zend Server 6.x **
Upload and run the msi installer.


## Configure the Nagios server ##