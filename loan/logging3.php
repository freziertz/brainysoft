<?php





// Before: composer require monolog/monolog
// composer autoloader
require '../lib/vendor/autoload.php';
// Shortcuts for simpler usage

// Common logger
$log = new Monolog\Logger('files');
// Line formatter without empty brackets in the end
$formatter = new Monolog\Formatter\LineFormatter(null, null, false, true);
// Debug level handler
$debugHandler = new Monolog\Handler\StreamHandler('debug.log', Monolog\Logger::DEBUG);
$debugHandler->setFormatter($formatter);
// Error level handler
$errorHandler = new Monolog\Handler\StreamHandler('error.log', Monolog\Logger::ERROR);
$errorHandler->setFormatter($formatter);
// This will have both DEBUG and ERROR messages
$log->pushHandler($debugHandler);
// This will have only ERROR messages
$log->pushHandler($errorHandler);
// The actual logging
$log->addDebug('I am debug');
$log->addDebug("\n");
$log->addError('I am error', array('productId' => 123));

?>