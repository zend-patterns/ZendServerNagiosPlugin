<?php
namespace ZendDevops\DepH\Configuration\Apache;

use ZendDevops\DepH\Deployment\Context;

class ConfigurationHelper
{
    protected $serviceManager;
    
    
	/**
     * @return the $context
     */
    public function getServiceManager ()
    {
        return $this->serviceManager;
    }

	/**
     * @param field_type $context
     */
    public function setServiceManager ($serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }
    
    /**
     * Return the deployment context
     */
    public function getContext()
    {
        return $this->getServiceManager()->get('deployment_context');
    }
    
    /**
     * Return the template manager
     */
    public function getTemplateManager()
    {
        return $this->getServiceManager()->get('template_manager');
    }

    /**
     * get the main vhost directory
     * @return string
     */
    public function getVhostDirectory()
    {
        $vhostPath = trim($this->getContext()->getServerDirectory(),DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $vhostPath .= 'etc' . DIRECTORY_SEPARATOR;
        $vhostPath .= 'sites.d' . DIRECTORY_SEPARATOR;
        return $vhostPath;
    }
    
    /**
     * Get the path of the Vhost file that have been build by Zend deployment
     *
     * If you deply an application given a host Zend Server will build
     * an additionnal vhost.
     * @return string
     */
    public function getVhostFile($host = null)
    {
        if ( ! $host) $host = $this->getContext()->getDeploymentHost();
        if ($host == Context::HOST_DEFAULT_SERVER) return null;
        $vhostPath = $this->getVhostDirectory();
        $vhostPath .= 'vhost_http_' . $host . '_80.conf';
        return $vhostPath;
    }
    
    /**
     * get the directories where alias files will be set
     * @return string
     */
    public function getAliasDirectory($host = null, $port = '80')
    {
        if ( ! $host) $host = $this->getContext()->getDeploymentHost();
        if ($host == Context::HOST_DEFAULT_SERVER) {
            $host = '__default__';
            $port = '0';
        }
        $aliasPath = trim($this->getContext()->getServerDirectory(),DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $aliasPath .= 'etc' . DIRECTORY_SEPARATOR;
        $aliasPath .= 'sites.d' . DIRECTORY_SEPARATOR . 'http' . DIRECTORY_SEPARATOR;
        $aliasPath .= $host . DIRECTORY_SEPARATOR . $port;
        return $aliasPath;
    }
    
    /**
     * Create an set credential to the Apche alias file diretctory
     * @param string $host
     * @param string $port
     */
    public function prepareAliasDirectory($host = null, $port = '80')
    {
        $aliasDirectory = $this->getAliasDirectory($host,$port);
        if ( ! is_dir($aliasDirectory)) mkdir($aliasDirectory,'0755', true);
    }
    
    /**
     * Get the path of the http conf file that have been build by Zend deployment
     * to manage Zen server aliasing
     *
     * @return string
     */
    public function getDefaultAliasFile()
    {
        $aliasPath = $this->getAliasDirectory();
        foreach (scandir($aliasPath) as $file)
        {
            if (pathinfo($file,PATHINFO_EXTENSION) == 'conf')
                return $aliasPath . DIRECTORY_SEPARATOR . $file;
        }
    }
    
    /**
     * Return the default Alias file template
     */
    public function getDefaultAliasTemplate()
    {
        $templateFile  = $this->getContext()->getDeploymentSrc();
        $templateFile .= '/ZendDevops/DepH/template/apache/alias.phtml';
        return $this->getTemplateManager()->getTemplate($templateFile);
    }
    
    /**
     * Return the default vhost template
     */
    public function getDefaultVhostTemplate()
    {
        $templateFile  = $this->getContext()->getDeploymentSrc();
        $templateFile .= '/ZendDevops/DepH/template/apache/vhost.phtml';
        return $this->getTemplateManager()->getTemplate($templateFile);
    }
    
    /**
     * Create a new Alias file based on the default template
     * @param string $fileName: file name (relative to the default alias file directory)
     * @param string $url
     * @param string $directory
     */
    public function createNewAliasFile($fileName, $url, $directory, $host = null, $port = '80')
    {
        $aliasFilename  = trim($this->getAliasDirectory($host, $port),DIRECTORY_SEPARATOR);
        $aliasFilename .= DIRECTORY_SEPARATOR . $fileName;
        $aliasTemplate = $this->getDefaultAliasTemplate();
        $aliasTemplate->url = '/' . ltrim($url,'/');
        $aliasTemplate->directory = trim($directory,'/') . '/';
        $aliasTemplate->render($aliasFilename);
    }
    
    /**
     * Create a new vhost file based on the default template
     * @param string $fileName: file name (relative to the default alias file directory)
     */
    public function createNewVhostFile( $host = null)
    {
        $vhostFilename = $this->getVhostFile($host);
        $vhostTemplate = $this->getDefaultVhostTemplate();
        $vhostTemplate->host = $host;
        $vhostTemplate->render($vhostFilename);
    }
    
    
    
}