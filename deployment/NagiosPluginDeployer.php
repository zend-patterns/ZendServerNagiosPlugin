<?php
use Zend\EventManager\Event;
use ZendDevops\DepH\Deployment\Context;
use ZendDevops\DepH\Deployment\DeploymentListener;

class NagiosPluginDeployer extends DeploymentListener
{
    public function postStageAction(Event $e)
    {
        //Create shell command shortcut
        switch ($this->getContext()->getWebServerType())
        {
            case Context::WEBSERVER_TYPE_APACHE:
                $bashPath = '/usr/local/zend/./nagiosplugin.sh';
                $templateDir = $this->getContext()->getDeploymentSrc() . '/template/';
                $template = $this->getTemplateManager()->getTemplate($templateDir . 'linuxBash.phtml');
                $template->appDir = $this->getContext()->getApplicationBaseDir();
                $template->render($bashPath);
            break;
        }
    }
}