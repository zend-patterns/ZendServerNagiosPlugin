<?php
namespace ZendDevops\DepH\Configuration;

Class Template
{
    /**
     * Template file name (full path)
     * @var string
     */
    protected $fileName;
    
    /**
     * Inner template variables
     * @var array
     */
    protected $var = array();
    
    /**
     * The template manager
     * @var TemplateManager
     */
    protected $templateManager;
    
    /**
     * Constructor
     * @param string $fileName
     */
    public function __construct($fileName)
    {
        $this->setFileName($fileName);
    }
    
    /**
     * Rendering the template
     */
    public function renderAsString()
    {
        ob_start();
        include $this->fileName;
        $renderString = ob_get_contents();
        ob_end_clean();
        $renderString = str_replace("\r", '', $renderString);
        return $renderString;
    }
    
    /**
     * Rendering the template
     * @param string $renderFile
     */
    public function render($renderFile)
    {
        file_put_contents($renderFile, $this->renderAsString());
        $this->getTemplateManager()->addRenderedTemplate($renderFile);
    }
    
    /**
     * Magical get
     * @param string $name
     * @return multitype:
     */
    public function __get($name)
    {
        return $this->var[$name];
    }
    
    /**
     * Magical setter
     * @param string $name
     * @param mixed $value
     */
    public function __set($name,$value)
    {
        $this->var[$name] = $value;
    }
    
	/**
     * @return the $fileName
     */
    public function getFileName ()
    {
        return $this->fileName;
    }

	/**
     * @param string $fileName
     */
    public function setFileName ($fileName)
    {
        $this->fileName = $fileName;
    }
	/**
     * @return the $templateManager
     */
    public function getTemplateManager ()
    {
        return $this->templateManager;
    }

	/**
     * @param \ZendDevops\DepH\Configuration\TemplateManager $templateManager
     */
    public function setTemplateManager ($templateManager)
    {
        $this->templateManager = $templateManager;
    }
    
    /**
     * Getter for Depolyment Listener
     */
    public function getContext()
    {
        return $this->getTemplateManager()->getContext();
    }

}