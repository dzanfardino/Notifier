<?php
namespace Craft;

class NotifierPlugin extends BasePlugin
{
    function init()
    {
        require CRAFT_PLUGINS_PATH .'/notifier/vendor/autoload.php';
        parent::init();

        if (craft()->request->isSiteRequest())
        {
            craft()->notifier->registerErrorHandler();
        }

    }

    function getName()
    {
        return Craft::t('Notifier');
    }

    function getVersion()
    {
        return '1.1';
    }

    function getDeveloper()
    {
        return 'Paramore | The digital agency + Electric Putty Digital';
    }

    function getDeveloperUrl()
    {
        return 'http://paramoredigital.com';
    }

    protected function defineSettings()
    {
        return array(
            'defaultNotifier'     => array(AttributeType::String),
            'airbrakeApiEndpoint' => array(AttributeType::String),
            'airbrakeApiKey'      => array(AttributeType::String),
            'airbrakeProjectId' => array(AttributeType::String),
            'airbrakeProjectKey' => array(AttributeType::String)
        );
    }

    public function getSettingsHtml()
    {
        return craft()->templates->render('notifier/settings', array('settings' => $this->getSettings()));
    }

    public function sendToNotifier($message)
    {
        craft()->notifier->sendString($message);
    }
}

/* End of NotifierPlugin.php */ 