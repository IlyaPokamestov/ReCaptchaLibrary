<?php

namespace DS\Library\ReCaptcha;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Psr\Log\InvalidArgumentException;
use Psr\Log\NullLogger;

class ReCaptchaLogger implements LoggerAwareInterface
{
    private $logger;
    
    public function __construct()
    {
        $this->logger = new NullLogger();
    }

    public function setLogger(LoggerInterface $logger = null) {
        $this->logger = $logger;
    }

    public function emergency($message, array $context = array()) {
        $this->logger->fatal($this->interpolate($message, $context));
    }

    /**
     * @param $message
     * @param array $context
     */
    public function alert($message, array $context = array()) {
        $this->logger->fatal($this->interpolate($message, $context));
    }

    /**
     * @param $message
     * @param array $context
     */
    public function critical($message, array $context = array()) {
        $this->logger->fatal($this->interpolate($message, $context));
    }

    /**
     * @param $message
     * @param array $context
     */
    public function error($message, array $context = array()) {
        $this->logger->error($this->interpolate($message, $context));
    }

    /**
     * @param $message
     * @param array $context
     */
    public function warning($message, array $context = array()) {
        $this->logger->warn($this->interpolate($message, $context));
    }

    /**
     * @param $message
     * @param array $context
     */
    public function notice($message, array $context = array()) {
        $this->logger->info($this->interpolate($message, $context));
    }

    /**
     * @param $message
     * @param array $context
     */
    public function info($message, array $context = array()) {
        $this->logger->info($this->interpolate($message, $context));
    }

    /**
     * @param $message
     * @param array $context
     */
    public function debug($message, array $context = array()) {
        $this->logger->debug($this->interpolate($message, $context));
    }

    /**
     * @param $level
     * @param $message
     * @param array $context
     */
    public function log($level, $message, array $context = array()) {
        switch ($level) {
            case LogLevel::EMERGENCY :
                $this->logger->emergency($message, $context);
                break;
            case LogLevel::ALERT :
                $this->logger->alert($message, $context);
                break;
            case LogLevel::CRITICAL :
                $this->logger->critical($message, $context);
                break;
            case LogLevel::ERROR :
                $this->logger->error($message, $context);
                break;
            case LogLevel::WARNING :
                $this->logger->warning($message, $context);
                break;
            case LogLevel::NOTICE :
                $this->logger->notice($message, $context);
                break;
            case LogLevel::INFO :
                $this->logger->info($message, $context);
                break;
            case LogLevel::DEBUG :
                $this->logger->debug($message);
                break;
            default:
                throw new InvalidArgumentException(
                    "Unknown severity level"
                );
        }
    }

    /**
     * @param $message
     * @param array $context
     * @return string
     */
    protected function interpolate($message, array $context = array()) {
        // build a replacement array with braces around the context
        // keys
        $replace = array();
        foreach ($context as $key => $val) {
            $replace['{' . $key . '}'] = $val;
        }

        // interpolate replacement values into the message and return
        return strtr($message, $replace);
    }
}