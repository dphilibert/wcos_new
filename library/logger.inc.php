<?php
/*
 * logger.inc.php
 * (c) 2007 NDCom digital, www.ndcom.de
 *
 *
 * The logger supports three loglevels: LOG_DEBUG, LOG_WARNING and LOG_ERROR.
 * Depending on the actual level that is set, messages get logged or not.
 * The most verbose level is LOG_DEBUG, the most restrictive one is LOG_ERROR.
 * 
 * Thomas Grahammer, tg@ndcom.de
 *
 */

// logelevel constants
define('LOGGER_DEBUG', 10);
define('LOGGER_WARNING', 20);
define('LOGGER_ERROR', 30);

define('BACKTRACE_OFF', 100);
define('BACKTRACE_ON', 110);

// the current loglevel
// update this value according to your needs
/*
if (array_key_exists ('SITE_TYPE', $_SERVER) && $_SERVER["SITE_TYPE"] != "online")
    define('LOGGER_LEVEL', LOGGER_DEBUG);
else
    define('LOGGER_LEVEL', LOGGER_ERROR);

*/
define ('LOGGER_LEVEL', LOGGER_DEBUG);
   
// Per default, backtracing is disabled
// Only activate backtracing when it's really necessary.
// Keep in mind, that backtracing greatly decreases system performance
// and also leads to large tracing logfiles.
define('BACKTRACE_LEVEL', BACKTRACE_OFF);

// resolve level names
static $levelNames = array(
    LOGGER_DEBUG   => "DEBUG",
    LOGGER_WARNING => "WARNING",
    LOGGER_ERROR   => "ERROR"
);

/**
 * log messages you need to test and develop your code.
 * 
 * @param string msg the log message
 * @param string filename name of the sourcefile (usally __FILE__)  
 * @param int line line number (usally __LINE__)
 * @param string user a developer handle
 * @var $args
 */ 
function logDebug() {
    $args = func_get_args();
    _log(LOGGER_DEBUG, $args);
}

/**
 * log anything that does go wrong, but your code can recover from.
 * 
 * @param string msg the log message
 * @param string filename name of the sourcefile (usally __FILE__)  
 * @param int line line number (usally __LINE__)
 * @param string user a developer handle
 */
function logWarning() {
    $args = func_get_args();
    _log(LOGGER_WARNING, $args);
}

/**
 * log fatal errors, which indicate that something is really broke
 * and needs immediate treatment. usally a sysops nightmare...
 * 
 * @param string msg the log message
 * @param string filename name of the sourcefile (usally __FILE__)  
 * @param int line line number (usally __LINE__)
 * @param string user a developer handle
 */
function logError() {
    $args = func_get_args();
    _log(LOGGER_ERROR, $args);
}

/**
 * Backtracing is a powerful method to find out, where a function
 * call originally came from. The output of the backtracing process
 * contains the function calls as a stack. That means, that the
 * most recent call is found at the top of the output, and the last
 * entry is the function call that started the chain.
 * 
 * @param boolean arguments if this flag evals to true, the argument list will be included.
 * @param boolean details if this flag evals to true, strings in the argument list will be expanded (max. 64 chars)
 */
function logBacktrace($arguments = false, $details = false) {
    if(BACKTRACE_LEVEL == BACKTRACE_ON){
        $debug_array = debug_backtrace();
        $message = "\n";
        foreach($debug_array as $key => $function_call){
            $args = '';
            if(0 == $key){
                $filename = $function_call['file'];
                $linenumber = $function_call['line'];
            } else {
                if(true == $arguments){
                    // process argument list
                    foreach ($function_call['args'] as $a) {
                        if (!empty($args)) {
                            $args .= ', ';
                        }
                        switch (gettype($a)) {
                            case 'integer':
                                $args .= 'int('.$a.')';
                                break;
                            case 'double':
                                $args .= 'double('.$a.')';
                                break;
                            case 'string':
                                if(true == $details){
                                    $args .= '"'.substr($a, 0, 64).((strlen($a) > 64) ? '...' : '').'"';
                                } else {
                                    $args .= 'String('.strlen($a).')';
                                }
                                break;
                            case 'array':
                                $args .= 'Array('.count($a).')';
                                break;
                            case 'object':
                                $args .= 'Object('.get_class($a).')';
                                break;
                            case 'resource':
                                $args .= 'Resource('.strstr($a, '#').')';
                                break;
                            case 'boolean':
                                $args .= $a ? 'True' : 'False';
                                break;
                            case 'NULL':
                                $args .= 'Null';
                                break;
                            default:
                                $args .= 'Unknown';
                                break;
                        }
                    }
                }
                // append function call to message
                $message .= $function_call['file'] . ':' . $function_call['line'] . ' >>> ' . $function_call['class'] . $function_call['type'] . $function_call['function'] . '(' . $args . ")\n";
            }
        }
        // abuse username for timestamp ;-)
        $date = date("j-M-Y H:i:s");
        logToFile($message, 'backtrace.log', $filename, $linenumber, $date);
    }
}


/**
 * dump messages to logfile depending on loglevel.
 */
function _log($level, $args) {
    global $levelNames;
      
    if ($level >= LOGGER_LEVEL) {
        $user = 'n/a';
        $trace = debug_backtrace();
        if (count($trace) > 1) {
            $trace = $trace[1];
            $filename = $trace['file'];
            $line = $trace['line'];
        }
        $count = count($args);
        if ($count < 1) die('Wrong number of arguments');
        $msg = $args[0];
        if ($count == 2) $user = $args[1];
        else {
            $filename = $args[1];
            $line = $args[2];
            if ($count >= 4) $user = $args[3];
        }
//        $filename = str_replace($_SERVER['SITE_ROOT'].'/', ' ', $filename);
        error_log("[".$levelNames[$level]."] [$user] $filename ($line): $msg");
    }
}

/**
 * Dumps the given variable to the debug log file.
 * @see //logDebug
 * @param mixed $var The variable to be dumped. Be careful passing objects to
 * avoid infinite recursion.
 * @param string $user The developer as to be passed as second argument to
 * //logDebug()
 */
function dumpVar($var, $user) {
    _log(LOGGER_DEBUG, array(var_export($var, true), $user));
}


/**
 * Sometimes functionality is too far from being reliable, so we always want some
 * debug information in order to track things down when it comes to it.
 * For this reason, this function offers a possibility to write into a custom
 * logfile that's located in the $SITE_ROOT/var/log/php/ directory.
 * Loglevels are not supported (we assume the lowest loglevel possible).
 * Since the output goes directly into a file and does not make use of the
 * ultra cool logging engine, I suggest to use this method only for rarely
 * occuring debug information, or else we'll run into both performance and disk space problems.
 * You've been warned, fella!
 * 
 * @param string msg the log message
 * @param string destinationFile path to the custom log-file, which is then appended to $SITE_ROOT/var/log/php/
 * @param string sourceFile name of the sourcefile (usally __FILE__)  
 * @param int line line number (usally __LINE__)
 * @param string user a developer handle
 */
function logToFile(){
    // log to /var/log/php/ because it's recommended by our admin.
    // (and because it's sym-linked to the shared area)
	$args = func_get_args();
	if (count($args) < 2) {
	    logWarning("not enough arguments to logToFile()", 'chl');
	} else {
	    $msg = $args[0];
	    $destinationFile = $args[1];
	    $destinationPath = $_SERVER['SITE_ROOT'] . '/var/log/php/';
        $logFile = $destinationPath . $destinationFile;
        $user = 'n/a';
        if (count($args) > 3) {
            $destinationFile = $args[2];
            $line = $args[3];
            if (count($args) > 4) {
                $user = $args[4];
            }
        } else {
            $trace = debug_backtrace();
            if (count($trace) > 1) {
                $trace = $trace[1];
            }
            $sourceFile = $trace['file'];
            $line = $trace['line'];
            if (count($args) > 2) {
                $user = $args[2];
            }
        }
        // error_log type = 3 (that's into a custom file)
        error_log("[$user] $sourceFile ($line): $msg\n", 3, $logFile);
	}
}

?>
