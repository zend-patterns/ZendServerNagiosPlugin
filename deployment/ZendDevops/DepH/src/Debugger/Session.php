<?php
namespace ZendDevops\DepH\Debugger;

class Session
{
    /**
     * Debug session configuration
     * @var array
     */
    protected $config = array(
        'start_debug' => 1,
        'use_remote' => 1,
        'debug_port' => 10137,
        'debug_fastfile' => 1,
        'debug_stop' => 1,
    );
    
    /**
     * The path to the debugged script
     * @var string
     */
    protected $baseScript = '';
    
    /**
     * Constructor

     * @param array $config
     */
    public function __construct(array $config = array())
    {
        $this->config = array_merge($this->config,$config);
        $this->setDebugSessionId(rand(100000, 999999));
    }
    
    /**
     * Lauch a debug session
     */
    public function launch()
    {
        $queryString = 'set QUERY_STRING="' . http_build_query($this->config) .'"';
        $command ='php "' . $this->baseScript .'"';
        exec($queryString);
        exec($command);
        exit();
    }
    
    /**
     * Set the script path
     * @param unknown $script
     */
    public function  setBaseScript($script)
    {
        $this->baseScript = $script;
    }
    
	/**
     * @return the $debugHost
     */
    public function getDebugHost ()
    {
        return $this->config['debug_host'];
    }

	/**
     * @return the $useRemote
     */
    public function getUseRemote ()
    {
        return $this->config['use_remote'];
    }

	/**
     * @return the $debugPort
     */
    public function getDebugPort ()
    {
        return $this->config['debug_port'];
    }

	/**
     * @return the $debugSessionId
     */
    public function getDebugSessionId ()
    {
        return $this->config['debug_session_id'];
    }

	/**
     * @return the $debugFastFile
     */
    public function getDebugFastFile ()
    {
        return $this->config['debug_fastfile'];
    }

	/**
     * @param field_type $debugHost
     */
    public function setDebugHost ($debugHost)
    {
        $this->debugHost = $debugHost;
    }

	/**
     * @param boolean $useRemote
     */
    public function setUseRemote ($useRemote)
    {
        $this->config['use_remote'] = (int)$useRemote;
    }

	/**
     * @param string $debugPort
     */
    public function setDebugPort ($debugPort)
    {
        $this->config['debug_port'] = $debugPort;
    }

	/**
     * @param field_type $debugSessionId
     */
    public function setDebugSessionId ($debugSessionId)
    {
        $this->config['debug_session_id'] = $debugSessionId;
    }

	/**
     * @param boolean $debugFastFile
     */
    public function setDebugFastFile ($debugFastFile)
    {
        $this->config['debug_fastfile'] = (int)$debugFastFile;
    }

}