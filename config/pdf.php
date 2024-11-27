<?php

return [
    /**
     * Options for browser creation.
     *
     * - connectionDelay: Delay to apply between each operation for debugging purposes (default: none)
     * - customFlags: An array of flags to pass to the command line.
     * - debugLogger: A string (e.g "php://stdout"), or resource, or PSR-3 logger instance to print debug messages (default: none)
     * - disableNotifications: Disable browser notifications (default: false)
     * - enableImages: Toggles loading of images (default: true)
     * - envVariables: An array of environment variables to pass to the process (example DISPLAY variable)
     * - headers: An array of custom HTTP headers
     * - headless: Enable or disable headless mode (default: true)
     * - ignoreCertificateErrors: Set Chrome to ignore SSL errors
     * - keepAlive: Set to `true` to keep alive the Chrome instance when the script terminates (default: false)
     * - noSandbox: Enable no sandbox mode, useful to run in a docker container (default: false)
     * - proxyServer: Proxy server to use. ex: `127.0.0.1:8080` (default: none)
     * - sendSyncDefaultTimeout: Default timeout (ms) for sending sync messages (default 5000 ms)
     * - startupTimeout: Maximum time in seconds to wait for Chrome to start (default: 30 sec)
     * - userAgent: User agent to use for the whole browser
     * - userDataDir: Chrome user data dir (default: a new empty dir is generated temporarily)
     * - userCrashDumpsDir: The directory crashpad should store dumps in (crash reporter will be enabled automatically)
     * - windowSize: Size of the window. ex: `[1920, 1080]` (default: none)
     * - excludedSwitches: An array of Chrome flags that should be removed from the default set (example --enable-automation)
     */
    'chrome_options' => [
        'keepAlive' => true,
        'noSandbox' => true,
    ],
    'chrome_timeout' => 60 * 1000,
];
