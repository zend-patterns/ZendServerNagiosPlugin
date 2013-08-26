<?php
namespace ZendDevops\DepH\Deployment;

/**
 * Class to manage the Zend Server Context
 * 
 *
 */
abstract class Context {
    
    const WEBSERVER_TYPE_APACHE = 'APACHE';
    const WEBSERVER_TYPE_NGINX = 'NGINX';
    const WEBSERVER_TYPE_IIS = 'IIS';
    
    const HOST_DEFAULT_SERVER = '<default-server>';
    
    /**
     * Check if the deployment process wil be run once
     * 
     * @var Boolean
     */
    protected $runOnce = true;
    
    /**
     * Web server type
     * 
     * IIS or APACHE (NGINX ?)
     * @var string
     */
    protected $webserverType;

    /**
     * Web server version
     * @var string
     */
    protected $webserverVersion;
    
    /**
     * Web server uid
     * 
     * @var string
     */
    protected $webserverUid;
     
    /**
     * Web server gid
     * 
     * @var string
     */
    protected $webserverGid;
    
    /**
     * Path where the application will be deployed
     * 
     * @var string
     */
    protected $applicationBaseDir;
    
    /**
     * Current application version
     * 
     * @var string
     */
    protected $currentAppVersion;
    
    /**
     * Previsou application version
     * 
     * @var string
     */
    protected $previousAppVersion;
    
    /**
     * Deployment parameters and variables
     * @var array
     */
    protected $params = array();
    
    /**
     * PHP Version
     * 
     * @var string
     */
    protected $phpVersion;
    
    /**
     * Application base url
     * 
     * @var string
     */
    protected $baseUrl;
    
    /**
     * Application ID
     * 
     * @var int
     */
    protected $applicationId;
    
    /**
     * Application Name
     * @var string
     */
    protected $applicationName;
    
    /**
     * Zend server directory
     * @var string
     */
    protected $serverDirectory;
    
    /**
     * The Deployment source directory
     * @var string
     */
    protected $deploymentSrc;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setApplicationBaseDir(getenv('ZS_APPLICATION_BASE_DIR'));
        $this->setCurrentAppVersion(getenv('ZS_CURRENT_APP_VERSION'));
        $this->setPreviousAppVersion(getenv('ZS_PREVIOUS_APP_VERSION'));
        $this->setRunOnce(getenv('ZS_RUN_ONCE_NODE'));
        $this->setWebserverGid(getenv('ZS_WEBSERVER_GID'));
        $this->setWebserverType(getenv('ZS_WEBSERVER_TYPE'));
        $this->setWebserverUid(getenv('ZS_WEBSERVER_UID'));
        $this->setWebserverVersion(getenv('ZS_WEBSERVER_VERSION'));
        $this->setPhpVersion(getenv('ZS_PHP_VERSION'));
        $this->setBaseUrl(getenv('ZS_BASE_URL'));
        $this->setApplicationId(getenv('ZS_APPLICATION_ID'));
        $this->setDeploymentSrc(getenv('ZS_DEPLOYMENT_SRC'));
        //Server directory
        $appDir = $this->getApplicationBaseDir();
        $serverDirectoryPath = explode(DIRECTORY_SEPARATOR, $appDir);
        foreach ($serverDirectoryPath as $path){
            if ($path == 'data') break;
            if ( ! isset($serverDirectory)) $serverDirectory = $path . DIRECTORY_SEPARATOR;
            else $serverDirectory .= $path . DIRECTORY_SEPARATOR;
        }
        $this->setServerDirectory($serverDirectory);
    }
    
    /**
     * Configure context
     * @param array $config
     */
    public function setConfig($config)
    {
        if (isset($config['application_base_dir'])) $this->setApplicationBaseDir($config['application_base_dir']);
        if (isset($config['current_app_version'])) $this->setCurrentAppVersion($config['current_app_version']);
        if (isset($config['previous_app_version'])) $this->setPreviousAppVersion($config['previous_app_version']);
        if (array_key_exists('run_once', $config)) $this->setRunOnce($config['run_once']);
        if (isset($config['webserver_gid'])) $this->setWebserverGid($config['webserver_gid']);
        if (isset($config['webserver_type'])) $this->setWebserverType($config['webserver_type']);
        if (isset($config['webserver_uid'])) $this->setWebserverUid($config['webserver_uid']);
        if (isset($config['webserver_version'])) $this->setWebserverVersion($config['webserver_version']);
        if (isset($config['php_version'])) $this->setPhpVersion($config['php_version']);
        if (isset($config['base_url'])) $this->setBaseUrl($config['base_url']);
        if (isset($config['application_id'])) $this->setApplicationId($config['application_id']);
        if (isset($config['application_name'])) $this->setApplicationName($config['application_name']);
        if (isset($config['server_directory'])) $this->setServerDirectory($config['server_directory']);
        if (isset($config['deployment_src'])) $this->setDeploymentSrc($config['deployment_src']);
    }
    
    /**
     * Return a custom parameter 
     * 
     * @param string $param
     * @return string|NULL
     */
    public function getParam($param)
    {
        $envVarName = 'ZS_' . strtoupper($param);
        $value = getenv($envVarName);
        if ($value) return $value;
        elseif (array_key_exists($param,$this->params)) return $this->params[$param];
        return null;
    }
    
    /**
     * Setteing parameter
     * 
     * @param string $param
     * @param mixed $value
     */
    public function setParam($param,$value)
    {
        $this->params[$param] = $value;
    }
	/**
     * @return the $runOnce
     */
    public function getRunOnce ()
    {
        return $this->runOnce;
    }

	/**
     * @return the $webserverType
     */
    public function getWebserverType ()
    {
        return $this->webserverType;
    }

	/**
     * @return the $webserverVersion
     */
    public function getWebserverVersion ()
    {
        return $this->webserverVersion;
    }

	/**
     * @return the $webserverUid
     */
    public function getWebserverUid ()
    {
        return $this->webserverUid;
    }

	/**
     * @return the $webserverGid
     */
    public function getWebserverGid ()
    {
        return $this->webserverGid;
    }

	/**
     * @return the $applicationBaseDir
     */
    public function getApplicationBaseDir ()
    {
        return $this->applicationBaseDir;
    }

	/**
     * @return the $currentAppVersion
     */
    public function getCurrentAppVersion ()
    {
        return $this->currentAppVersion;
    }

	/**
     * @return the $previousAppVersion
     */
    public function getPreviousAppVersion ()
    {
        return $this->previousAppVersion;
    }

	/**
     * @param \Zend\Filter\Boolean $runOnce
     */
    public function setRunOnce ($runOnce)
    {
        $this->runOnce = $runOnce;
    }

	/**
     * @param string $webserverType
     */
    public function setWebserverType ($webserverType)
    {
        $this->webserverType = $webserverType;
    }

	/**
     * @param string $webserverVersion
     */
    public function setWebserverVersion ($webserverVersion)
    {
        $this->webserverVersion = $webserverVersion;
    }

	/**
     * @param string $webserverUid
     */
    public function setWebserverUid ($webserverUid)
    {
        $this->webserverUid = $webserverUid;
    }

	/**
     * @param \Zx\DepH\ZendServer\unknown $webserverGid
     */
    public function setWebserverGid ($webserverGid)
    {
        $this->webserverGid = $webserverGid;
    }

	/**
     * @param string $applicationBaseDir
     */
    public function setApplicationBaseDir ($applicationBaseDir)
    {
        $this->applicationBaseDir = $applicationBaseDir;
    }

	/**
     * @param string $currentAppVersion
     */
    public function setCurrentAppVersion ($currentAppVersion)
    {
        $this->currentAppVersion = $currentAppVersion;
    }

	/**
     * @param string $previousAppVersion
     */
    public function setPreviousAppVersion ($previousAppVersion)
    {
        $this->previousAppVersion = $previousAppVersion;
    }
    
	/**
     * @return the $phpVersion
     */
    public function getPhpVersion ()
    {
        return $this->phpVersion;
    }

	/**
     * @param string $phpVersion
     */
    public function setPhpVersion ($phpVersion)
    {
        $this->phpVersion = $phpVersion;
    }
	/**
     * @param string $baseUrl
     */
    public function setBaseUrl ($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }
    
    /**
     * @return string $baseUrl
     */
    public function getBaseUrl ()
    {
        return $this->baseUrl;
    }
	/**
     * @param number $applicationId
     */
    public function setApplicationId ($applicationId)
    {
        $this->applicationId = $applicationId;
    }
    
    /**
     * @return number $applicationId
     */
    public function getApplicationId ()
    {
        return $this->applicationId;
    }
    
	/**
     * @return the $applicationName
     */
    public function getApplicationName ()
    {
        return $this->applicationName;
    }

	/**
     * @param string $applicationName
     */
    public function setApplicationName ($applicationName)
    {
        $this->applicationName = $applicationName;
    }
	/**
     * @return the $serverDirectory
     */
    public function getServerDirectory ()
    {
        return $this->serverDirectory;
    }

	/**
     * @param string $serverDirectory
     */
    public function setServerDirectory ($serverDirectory)
    {
        $this->serverDirectory = $serverDirectory;
    }
    
    /**
     * Get the deployment host
     * @return string
     */
    public function getDeploymentHost()
    {
        $baseUrl = $this->getBaseUrl();
        if (empty($baseUrl)) return null;
        $tmp = array_reverse(explode('/', $baseUrl));
        return $tmp[1];
    }
    
    /**
     * Get the deployment path
     * @return string
     */
    public function getDeploymentPath()
    {
        $baseUrl = $this->getBaseUrl();
        if (empty($baseUrl)) return null;
        $tmp = array_reverse(explode('/', $baseUrl));
        return $tmp[0];
    }
    
    /**
     * Epxport at an arry
     * @return array
     */
    public function toArray()
    {
        $result = array();
        $result['application_base_dir'] = $this->getApplicationBaseDir();
        $result['current_app_version'] = $this->getCurrentAppVersion();
        $result['previous_app_version'] = $this->getPreviousAppVersion();
        $result['run_once'] = $this->getRunOnce();
        $result['webserver_gid'] = $this->getWebserverGid();
        $result['webserver_type'] = $this->getWebserverType();
        $result['webserver_uid'] = $this->getWebserverUid();
        $result['webserver_version'] = $this->getWebserverVersion();
        $result['php_version'] = $this->getPhpVersion();
        $result['base_url'] = $this->getBaseUrl();
        $result['application_id'] = $this->getApplicationId();
        $result['application_name'] = $this->getApplicationName();
        $result['server_directory'] = $this->getServerDirectory();
        return $result;
    }
	/**
     * @return the $deploymentSrc
     */
    public function getDeploymentSrc ()
    {
        return $this->deploymentSrc;
    }

	/**
     * @param string $deploymentSrc
     */
    public function setDeploymentSrc ($deploymentSrc)
    {
        $this->deploymentSrc = $deploymentSrc;
    }

    




}