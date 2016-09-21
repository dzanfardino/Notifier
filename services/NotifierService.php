<?php
namespace Craft;

use Airbrake\Notifier;
use Airbrake\Instance;
use Airbrake\ErrorHandler;

class NotifierService extends BaseApplicationComponent
{
    static protected $settings;
    static protected $notifier;

    public function __construct($settings = null)
    {
        self::$settings = (is_null($settings)) ? craft()->plugins->getPlugin('Notifier')->getSettings() : $settings;

        // Create new Notifier instance.
        self::$notifier = new Notifier(array(
            'host' => self::$settings->airbrakeApiEndpoint,
            'apiKey' => self::$settings->airbrakeApiKey,
            'projectId' => self::$settings->airbrakeProjectId,
            'projectKey' => self::$settings->airbrakeProjectKey,
            'environment' => craft()->config->get('devMode') ? 'dev' : 'production'
        ));
    }

    public function registerErrorHandler()
    {
        // Set global notifier instance.
        Instance::set(self::$notifier);

        // Register error and exception handlers.
        $handler = new ErrorHandler(self::$notifier);
        // @todo: Add filters to ignore certains exceptions
//        self::$notifier->addFilter(function ($notice) {
//            if ($notice['errors'][0]['type'] == 'MyExceptionClass') {
//                // Ignore this exception.
//                return null;
//            }
//            return $notice;
//        });

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