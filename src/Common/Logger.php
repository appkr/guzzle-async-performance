<?php

namespace Common;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;
use Psr\Log\LoggerInterface;

class Logger
{
    public static function create(string $loggerName, string $logPath): LoggerInterface
    {
        $logger = new \Monolog\Logger($loggerName);

//        $formatter = new class extends LineFormatter {
//            /**
//             * {@inheritdoc}
//             */
//            protected function toJson($data, $ignoreErrors = false)
//            {
//                $json = json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//
//                if ($json === false) {
//                    $json = parent::toJson($data, $ignoreErrors);
//                }
//
//                return $json;
//            }
//        };

        $handlers[] = new StreamHandler($logPath);
        $handlers[] = new ErrorLogHandler();

        /** @var HandlerInterface $handler */
        foreach ($handlers as $handler) {
//            $handler->setFormatter($formatter);
            $logger->pushHandler($handler);
        }

        return $logger;
    }
}