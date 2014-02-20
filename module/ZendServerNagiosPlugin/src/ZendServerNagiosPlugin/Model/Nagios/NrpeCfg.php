<?php
namespace ZendServerNagiosPlugin\Model\Nagios;

class NrpeCfg
{
	/**
	 * Configuration
	 * 
	 * @var array
	 */
	protected $config = array();
	
	/**
	 * File path
	 * 
	 * @var string
	 */
	protected $filePath ='';
	
	/**
	 * Constructor
	 * 
	 * @param string $file Path to nrpe.cfg file
	 */
	public function __construct($file = null)
	{
		if ($file) {
			$this->setConfig($file);
		}
		
	}
	
	/**
	 * Set configuration from nrpe.cfg file
	 * 
	 * @param string $configFile Path to nrpe.cfg file
	 */
	public function setConfig($configFile)
	{
		$this->filePath = $configFile;
		$file = fopen($configFile,'r');
		while ($line = fgets($file))
		{
			if (substr($line, 0,1) == '#') continue;
			$result = preg_match_all('@([^=]*)=([^=]*)@', $line, $tmp);
			if ( ! $result) continue;
			$var = trim($tmp[1][0]);
			if ($var == '') continue;
			$value = trim($tmp [2][0]);
			preg_match_all('@([a-z]*)\[([^\[\]]*)\]@', $var, $tmp2);
			if (count($tmp2[1]) != 0){
				$array = trim($tmp2[1][0]);
				$key = trim($tmp2[2][0]);
				if ( ! array_key_exists($array, $this->config)) $this->config[$array] = array();
				$this->config[$array][$key] = $value;
			}
			else $this->config[$var] = $value;
		}
	}
	
	/**
	 * Add command
	 * 
	 * @param string $id
	 * @param string $command ,
	 */
	public function addCommand($id, $command)
	{
		$this->config['command']['zs-' . $id] = $command;
	}
	
	/**
	 * Write the config to a file
	 */
	public function toFile()
	{
		if ($this->filePath == '') return ;
		$content = '';
		foreach ($this->config as $var => $value){
			if (is_array($value)) {
				foreach ($value as $key => $v){
					$content .= $var . '[' . $key . ']=' . $v . "\n";
				}
			}
			else $content .= $var .'=' . $value ."\n";
		}
		file_put_contents($this->filePath, $content);
	}
	
	/**
	 * Remove all commands related to Zend Server
	 */
	public function removeZsCommands()
	{
		foreach ($this->config['command'] as $command => $value){
			if (substr($command, 0, 3) == 'zs-') unset($this->config['command'][$command]);
		}
	}
}