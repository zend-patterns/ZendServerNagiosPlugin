<?php
/**
 * 
 */

namespace ZendDevops\DepH\Configuration;

use Zend\ServiceManager\ServiceManager;
use ZendDevops\DepH\Deployment\Context;

class TemplateManager extends ServiceManager
{
    /**
     * The parent service locator (may be DeploymentListner)
     * @var Context
     */
    protected $context;
    
    /**
     * List of templates
     * @var array
     */
    protected $template = array();
    
    /**
     * Liste of files created by template rendering.
     * 
     * Will be used by the rollback events
     * @var array
     */
    protected $renderedTemplate = array();
    
	/**
     * @return the $serviceLocator
     */
    public function getContext ()
    {
        return $this->context;
    }

	/**
     * @param field_type $deploymentLister
     */
    public function setContext ($context)
    {
        $this->context = $context;
    }

    /**
     * Return a template
     * @param unknown $templateFile
     * @param string $name
     * @param string $templateNamespace
     * @return multitype:|unknown
     */
    public function getTemplate($templateFile)
    {
        if (array_key_exists($templateFile, $this->template) 
            && is_a($this->template[$templateFile], 'ZendDevops\DepH\Configuration\Template')) {
            return $this->template[$templateFile];
        }
        $template = new Template($templateFile);
        $template->setTemplateManager($this);
        $this->template[$templateFile] = $template;
        return $template;
    }
    
    /**
     * Add a rendered template to the list
     * @param string $fileName
     */
    public function addRenderedTemplate($fileName)
    {
        array_push($this->renderedTemplate, $fileName);
    }
    
    /**
     * Export rendered tempalte list as a file
     */
    public function exportRenderedTemplate()
    {
        $path = $this->getContext()->getApplicationBaseDir();
        $str = '<?php return ';
        $str .= var_export($this->renderedTemplate, true);
        $str .= ';';
        file_put_contents($path . DIRECTORY_SEPARATOR . 'template.php', $str);
    }
    
    /**
     * Load the rendereed template from the file
     */
    public function loadRenderedTemplate()
    {
        $path = $this->getContext()->getApplicationBaseDir();
        $this->renderedTemplate = include_once $path . DIRECTORY_SEPARATOR . 'template.php';
    }
    
    /**
     * Clean the manual rendered template
     */
    public function cleanRenderedTemplate()
    {
        $this->loadRenderedTemplate();
        foreach ($this->renderedTemplate as $file)
        {
            @unlink($file);
        }
        //@todo : delete prepared directories to....
    }
}