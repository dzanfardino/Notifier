<?php
namespace Craft;

use Airbrake\Notifier;
use Airbrake\Instance;
use Airbrake\ErrorHandler;

class NotifierService extends BaseApplicationComponent implements \Twig_Extension_GlobalsInterface
{
    static protected $settings;
    static protected $notifier;

    public function __construct($settings = null)
    {
        self::$settings = (is_null($settings)) ? craft()->plugins->getPlugin('Notifier')->getSettings() : $settings;

        if (isset(self::$settings) && !is_null(self::$settings->airbrakeApiEndpoint) && !is_null(self::$settings->airbrakeApiKey) && !is_null(self::$settings->airbrakeProjectId) && !is_null(self::$settings->airbrakeProjectKey))
        {
            // Create new Notifier instance.
            self::$notifier = new Notifier(array(
                'host' => self::$settings->airbrakeApiEndpoint,
                'apiKey' => self::$settings->airbrakeApiKey,
                'projectId' => self::$settings->airbrakeProjectId,
                'projectKey' => self::$settings->airbrakeProjectKey,
                'environment' => craft()->config->get('devMode') ? 'dev' : 'production'
            ));
        }
    }

    /**
     *
     */
    public function registerErrorHandler()
    {
        // Set global notifier instance.
        if (isset(self::$notifier))
        {
            Instance::set(self::$notifier);
            // Register error and exception handlers.
            $handler = new ErrorHandler(self::$notifier);

            $handler->register();
            Craft::log('Error Handler registered successfully.', LogLevel::Info, false, 'notifier');
        }
        else
        {
            Craft::log('Notifier Instance not set. Error Handler not registered.', LogLevel::Warning, false, 'notifier');
        }
    }

    /**
     * @param $errorMessage
     */
    public function sendString($errorMessage)
    {
        if (isset(self::$notifier))
        {
            self::$notifier->notify(new \Airbrake\Exception($errorMessage));
        }
    }
}

/* End of NotifierService.php */ 