<?php

namespace App\Helpers;

class LoggingHelper
{
    /**
     * If logging is disabled
     *
     * @var bool
     */
    private static bool $disabled = false;

    /**
     * Where the logfile resides
     *
     * @var string|null
     */
    private static ?string $logFile = null;

    /**
     * Where the access logfile resides
     *
     * @var string|null
     */
    private static ?string $accessLogFile = null;

    /**
     * If register_shutdown_function has been set yet
     *
     * @var bool
     */
    private static bool $shutdownRegistered = false;

    /**
     * The log entries that get saved to the log file on shutdown
     *
     * @var array
     */
    public static array $entries = [];

    /**
     * microtime() of the last log entry
     *
     * @var int|null
     */
    private static ?int $lastTime = null;

    /**
     * If this is a simple access log
     *
     * @var bool
     */
    private static bool $isAccessLog = true;

    /**
     * Disables logging
     */
    public static function disable(): void
    {
        self::$disabled = true;
    }

    /**
     * Creates a log entry
     *
     * @param string $msg
     */
    public static function log(string $msg)
    {
        if (self::$disabled) {
            return;
        }

        if (php_sapi_name() === 'cli') {
            echo $msg . PHP_EOL;

            return;
        }

        self::init();

        if ($msg !== 'ACCEPTED') {
            self::$isAccessLog = false;
        }

        self::registerShutdown();

        $trace     = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 4);
        $classInfo = '[internal:internal]';
        if (sizeof($trace) > 1) {
            $classInfo = basename($trace[0]['file']) . ':' . $trace[0]['line'];
        }

        $time = '';
        if (self::$lastTime) {
            $now = round(microtime(true) * 1000);

            $time = '(' . ($now - self::$lastTime) . 'ms) ';
        }
        self::$lastTime = round(microtime(true) * 1000);

        if (strlen($msg) > 300) {
            $msg = substr($msg, 0, length: 300) . '...';
        }

        $msg = [$classInfo, $time . $msg . PHP_EOL];

        self::$entries[] = $msg;
    }

    /**
     * Initializes the logger
     */
    private static function init()
    {
        if (self::$logFile === null) {
            self::$logFile = rtrim(storage_path('logs'), '/\\') . '/' . CLUSTER . '_op-fw-' . date('Y-m-d') . '.log';
        }
        if (self::$accessLogFile === null) {
            self::$accessLogFile = rtrim(storage_path('logs'), '/\\') . '/' . CLUSTER . '_op-fw-access-' . date('Y-m-d') . '.log';
        }

        self::registerShutdown();
    }

    /**
     * Writes the log header for a request
     */
    private static function header()
    {
        $timestamp = date(\DateTimeInterface::RFC3339);
        $method    = str_pad($_SERVER['REQUEST_METHOD'] ?? '???', 7, ' ');
        $ip        = $_SERVER['REMOTE_ADDR'] ?? '???';
        $path      = explode('?', $_SERVER['REQUEST_URI'] ?? '???');
        $get       = isset($path[1]) ? $path[1] : null;
        $path      = $path[0];

        $msg = '[' . $timestamp . '] ' .
            '[' . $ip . '] ' .
            '[' . $method . '] ' .
            $path;

        if (in_array($path, [
            '/logs',
        ])) {
            if (empty($get)) {
                $msg .= '?';
            } else {
                $msg .= PHP_EOL . '    ?' . $get;
            }
        }
        $msg .= PHP_EOL;

        self::$entries[] = $msg;
    }

    /**
     * Adds a separator to the log file
     */
    public static function endRequest()
    {
        $max = 0;
        foreach (self::$entries as $entry) {
            if (is_array($entry) && strlen($entry[0]) > $max) {
                $max = strlen($entry[0]);
            }
        }

        $logs = array_map(function ($entry) use ($max) {
            if (is_array($entry)) {
                return '    [' . str_pad($entry[0], $max, ' ') . '] ' . $entry[1];
            }
            return $entry;
        }, self::$entries);

        $time = '';
        if (self::$lastTime) {
            $now = round(microtime(true) * 1000);

            $time = '(' . ($now - self::$lastTime) . 'ms)';
        }

        $logs[] = '--- CLOSED ' . $time;

        put_contents(self::$isAccessLog ? self::$accessLogFile : self::$logFile, implode('', $logs) . PHP_EOL, FILE_APPEND);
    }

    /**
     * Registers a shutdown that adds a separator to the log file
     */
    private static function registerShutdown()
    {
        if (!self::$shutdownRegistered) {
            self::header();

            register_shutdown_function(function () {
                LoggingHelper::endRequest();
            });

            self::$shutdownRegistered = true;
        }
    }

    /**
     * Cleans the log files by removing old ones
     */
    public static function cleanup()
    {
        $dir = storage_path('logs');

        $files = array_diff(scandir($dir), ['.', '..', '.gitignore']);

        foreach ($files as $file) {
            if (strpos($file, '.log') === false) continue;

            $path = $dir . '/' . $file;

            // Keep logs for 7 days
            if (filemtime($path) < time() - 60 * 60 * 24 * 7) {
                unlink($path);
            }
        }
    }
}
