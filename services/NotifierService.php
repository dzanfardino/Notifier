<?php
namespace Craft;

use Airbrake\Notifier;
use Airbrake\Instance;
use \Airbrake\ErrorHandler;

class NotifierService extends BaseApplicationComponent
{
    static protected $settings;
    protected $notifier;

    public function __construct($settings = null)
    {
        self::$settings = (is_null($settings)) ? craft()->plugins->getPlugin('Notifier')->getSettings() : $settings;

        // Create new Notifier instance.
        $this->notifier = new Notifier(array(
            'host' => self::$settings->airbrakeApiEndpoint,
            'apiKey' => self::$settings->airbrakeApiKey,
            'projectId' => self::$settings->airbrakeProjectId,
            'projectKey' => self::$settings->airbrakeProjectKey,
        ));

        // Set global notifier instance.
        Instance::set($this->notifier);

        // Register error and exception handlers.
        $handler = new ErrorHandler($this->notifier);
        $handler->register();

    }

    public function sendString($errorMessage)
    {
        // Throw an Exception
        try
        {
            throw new Exception($errorMessage);
        }
        catch(Exception $e)
        {
            Instance::notify($e);
        }
    }
}

/* End of NotifierService.php */ 