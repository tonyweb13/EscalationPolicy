<?php
declare(strict_types=1);

namespace Tests;

use Laravel\Dusk\TestCase as BaseTestCase;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;

abstract class DuskTestCase extends BaseTestCase
{
    const DEBUG_WHICH_TEST = false;

    use CreatesApplication;

    public static function tearDownDuskClass()
    {
        parent::tearDownDuskClass();
    }

    public static function prepare()
    {
        static::startChromeDriver();
    }

    public function setUp()
    {
        parent::setUp();
        if (self::DEBUG_WHICH_TEST) {
            print "Setting up " . get_class($this) . "\n";
        }
    }

    protected function driver()
    {
        $debug = config('app.debug_dusk');

        $arguments = [
            '--disable-gpu',
            '--no-sandbox',
            '--window-size=1920,1080'
        ];

        $extra_args = $debug ? ['--enable-logging', '--v=1'] : ['--headless'];
        $arguments = array_merge($arguments, $extra_args);
        $options = (new ChromeOptions)->addArguments($arguments);
        $capabilities = DesiredCapabilities::chrome()->setCapability(ChromeOptions::CAPABILITY, $options);
        if ($debug) {
            // Make so the chrome console logs DEBUG and INFO (in addition to WARN and ERROR) which may be useful
            // for debugging
            $capabilities->setCapability('loggingPrefs', ['browser'=>'ALL']);
        }

        return RemoteWebDriver::create(env('APP_CHROME_DRIVER_URL'), $capabilities);
    }
}
