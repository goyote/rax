<?php

namespace Rax\Http;

use Mobile_Detect;

/**
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @copyright Copyright (c) Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class ClientSniffer
{
    /**
     * @var array
     */
    protected $info;

    /**
     * @var Mobile_Detect
     */
    protected $mobileDetect;

    /**
     * [browscap]
     * browscap = path/to/browscap.ini
     *
     * @link http://php.net/manual/en/function.get-browser.php
     * @link http://tempdownloads.browserscap.com
     */
    public function __construct($ua = null, Mobile_Detect $mobileDetect)
    {
        $this->info = get_browser($ua, true);
        $this->mobileDetect = $mobileDetect;
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($key = null, $default = null)
    {
        if (null === $key) {
            return $this->info;
        }

        return isset($this->info[$key]) ? $this->info[$key] : $default;
    }

    /**
     * Gets the OS code name.
     *
     * @return string
     */
    public function getOsCodeName()
    {
        return $this->get('platform');
    }

    /**
     * Gets the OS name.
     *
     * @return string
     */
    public function getOs()
    {
        $os = $this->getOsCodeName();

        switch ($os) {
            case 'MacOSX':
                $os = 'OS X';
                break;
            case 'WinNT':
                $os = 'Windows NT';
                break;
            case 'Win2000':
                $os = 'Windows 2000';
                break;
            case 'WinXP':
                $os = 'Windows XP';
                break;
            case 'WinVista':
                $os = 'Windows Vista';
                break;
            case 'Win7':
                $os = 'Windows 7';
                break;
            case 'Win8':
                $os = 'Windows 8';
                break;
        }

        return $os;
    }

    /**
     * Gets the browser name.
     *
     * @return string
     */
    public function getBrowser()
    {
        $browser = $this->get('browser');

        switch ($browser) {
            case 'IE':
                $browser = 'Internet Explorer';
                break;
        }

        return $browser;
    }

    /**
     * Gets the browser version.
     *
     * @return string
     */
    public function getBrowserVersion()
    {
        return $this->get('majorver');
    }

    /**
     * @return string
     */
    public function getDevice()
    {
        $device = '';

        if ($this->mobileDetect->isMobile()) {
            if ($this->mobileDetect->isTablet()) {
                $devices = $this->mobileDetect->getTabletDevices();
            } else {
                $devices = $this->mobileDetect->getPhoneDevices();
            }
            foreach ($devices as $name => $regex) {
                if ($this->mobileDetect->is($name)) {
                    $device = $name;
                    break;
                }
            }
        } else {
            switch (substr($this->getOsCodeName(), 0, 3)) {
                case 'Mac':
                    $device = 'Mac';
                    break;
                case 'Win':
                    $device = 'PC';
                    break;
                default:
                    $device = 'Computer';
            }
        }

        return $device;
    }
}
