<?php
use Zend\EventManager\Event;
use ZendDevops\DepH\Deployment\Context;
use ZendDevops\DepH\Deployment\DeploymentListener;
use Zend\Config\Writer\PhpArray;
use Zend\Config\Config;

class NagiosPluginDeployer extends DeploymentListener
{
    public function postStageAction(Event $e)
    {
        $appRoot = $this->getContext()->getApplicationBaseDir();
        $configFile = $appRoot . '/vendor/zendserverwebapi/zendserverwebapi/config/zendserverwebapi.config.php';
        $configArray = include $configFile;
        if ( ! is_array($configArray['zsapi']))  $configArray['zsapi'] = array();
        $configArray['zsapi']['default_target'] = array(
            'zsurl' => $this->getContext()->getParam('target_host'),
            'zskey' => $this->getContext()->getParam('key'),
            'zssecret' => $this->getContext()->getParam('secret'),
            'zs-version' => '6.0',
        );
        $config = new Config($configArray);
        $configWritter = new PhpArray();
        $configWritter->toFile($configFile, $config);
    }
}