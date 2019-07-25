<?php

namespace DS\Library\ReCaptcha;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Psr\Log\InvalidArgumentException;
use Psr\Log\NullLogger;

/**
 * ReCaptcha logger.
 *
 * @package DS\Library\ReCaptcha
 */
class ReCaptchaLogger implements LoggerInterface
{
    /**
     * @var LoggerInterface|NullLogger|null
     */
    private $logger;

    /**
     * ReCaptchaLogger constructor.
     *
     * @param LoggerInterface|null $logger
     */
    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = ($logger === null) ? new NullLogger() : $logger;
    }

    /**
     * Log EMERGENCY type notification.
     *
     * @param string $message
     * @param array $context
     */
    public function emergency($message, array $context = array()) {
        $this->logger->emergency($message, $context);
    }

    /**
     * Log ALERT type notification.
     *
     * @param $message
     * @param array $context
     */
    public function alert($message, array $context = array()) {
        $this->logger->alert($message, $context);
    }

    /**
     * Log CRITICAL type notification.
     *
     * @param $message
     * @param array $context
     */
    public function critical($message, array $context = array()) {
        $this->logger->critical($message, $context);
    }

    /**
     * Log ERROR type notification.
     *
     * @param $message
     * @param array $context
     */
    public function error($message, array $context = array()) {
        $this->logger->error($message, $context);
    }

    /**
     * Log WARNING type notification.
     *
     * @param $message
     * @param array $context
     */
    public function warning($message, array $context = array()) {
        $this->logger->warning($message, $context);
    }

    /**
     * Log NOTICE type notification.
     *
     * @param $message
     * @param array $context
     */
    public function notice($message, array $context = array()) {
        $this->logger->notice($message, $context);
    }

    /**
     * Log INFO type notification.
     *
     * @param $message
     * @param array $context
     */
    public function info($message, array $context = array()) {
        $this->logger->info($message, $context);
    }

    /**
     *
     * Log DEBUG type notification.
     * @param $message
     * @param array $context
     */
    public function debug($message, array $context = array()) {
        $this->logger->debug($message, $context);
    }

    /**
     * Additional logging functionality based on notificaton level.
     *
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
}