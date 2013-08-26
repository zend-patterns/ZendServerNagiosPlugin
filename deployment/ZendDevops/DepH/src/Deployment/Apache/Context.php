<?php
/**
 * Apache deployment context
 * @author sophie.b
 *
 */
namespace ZendDevops\DepH\Deployment\Apache;

use ZendDevops\DepH\Deployment as Deployment;
class Context extends Deployment\Context
{
    /**
     * get the main vhost directory
     * @return string
     */
    public function getVhostDirectory()
    {
        $vhostPath = trim($this->getServerDirectory(),DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
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
        if ( ! $host) $host = $this->getDeploymentHost();
        if ($host == self::HOST_DEFAULT_SERVER) return null;
        $vhostPath = $this->getVhostDirectory();
        $vhostPath .= 'vhost_http_' . $host . '_80.conf';
        return $vhostPath;
    }
    
    /**
     * get the directories where alias files will be set
     * @return string
     */
    public function getAliasFileDirectory($host = null, $port = '80')
    {
        if ( ! $host) $host = $this->getDeploymentHost();
        if ($host == self::HOST_DEFAULT_SERVER) {
            $host = '__default__';
            $port = '0';
        }
        $vhostPath = trim($this->getServerDirectory(),DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $vhostPath .= 'etc' . DIRECTORY_SEPARATOR;
        $vhostPath .= 'sites.d' . DIRECTORY_SEPARATOR . 'http' . DIRECTORY_SEPARATOR;
        $vhostPath .= $host . DIRECTORY_SEPARATOR . $port;
        return $vhostPath;
    }
    
    /**
     * Create an set credential to the Apche alias file diretctory
     * @param string $host
     * @param string $port
     */
    public function prepareAliasFileDirectory($host = null, $port = '80')
    {
        $aliasDirectory = $this->getAliasFileDirectory($host,$port);
        if ( ! is_dir($aliasDirectory)) mkdir($aliasDirectory,'0755', true);
    }
    
    /**
     * Get the path of the http conf file that have been build by Zend deployment
     * to manage Zen server aliasing
     *
     * @return string
     */
    public function getAliasFile()
    {
        $vhostPath = $this->getAliasFileDirectory();
        foreach (scandir($vhostPath) as $file)
        {
            if (pathinfo($file,PATHINFO_EXTENSION) == 'conf')
                return $vhostPath . DIRECTORY_SEPARATOR . $file;
        }
    }
    
    /**
     * Create a new Alias file based on the default template
     * @param string $fileName: file name (relative to the default alias file directory)
     * @param string $url
     * @param string $directory
     */
    public function createNewAliasFile($fileName, $url, $directory, $host = null, $port = '80')
    {
        $aliasFilename  = trim($this->getAliasFileDirectory($host, $port),DIRECTORY_SEPARATOR);
        $aliasFilename .= DIRECTORY_SEPARATOR . $fileName;
        
        $aliasTemplate = $this->getTemplateManager()->getDefaultAliasTemplate('front_alias');
        $aliasTemplate->url = '/' . ltrim($context->getDeploymentPath(),'/');
        $aliasTemplate->directory = trim($aliasDir,"/") . DIRECTORY_SEPARATOR . 'front' . DIRECTORY_SEPARATOR . 'www/';
        $aliasTemplate->render($aliasFilename);
    }
}