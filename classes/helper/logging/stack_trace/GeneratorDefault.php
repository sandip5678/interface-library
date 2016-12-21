<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2014 Shopgate GmbH
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
*/

/**
 * Generates a stack trace with obfuscation of arguments and flattening of objects or arrays.
 *
 * In the stack trace, function calls will be presented with arguments, unless those are obfuscated or filtered by the
 * Shopgate_Helper_Logging_Obfuscator passed in the constructor.
 *
 * An argument that is an object will be converted to 'Object'.
 * An argument that is an array will be converted to 'Array'.
 * An argument that is boolean true / false will be converted to 'true' / 'false'.
 */
class Shopgate_Helper_Logging_Stack_Trace_GeneratorDefault
    implements Shopgate_Helper_Logging_Stack_Trace_GeneratorInterface
{
    /** @var Shopgate_Helper_Logging_Obfuscator */
    protected $obfuscator;
    
    /** @var Shopgate_Helper_Logging_Stack_Trace_NamedParameterProviderInterface */
    protected $namedParameterProvider;
    
    /** @var array [string, string[]] */
    protected $functionArgumentsCache;
    
    public function __construct(
        Shopgate_Helper_Logging_Obfuscator $obfuscator,
        Shopgate_Helper_Logging_Stack_Trace_NamedParameterProviderInterface $namedParameterProvider
    ) {
        $this->obfuscator             = $obfuscator;
        $this->namedParameterProvider = $namedParameterProvider;
        $this->functionArgumentsCache = array();
    }
    
    public function generate($e, $maxDepth = 10)
    {
        $msg = array($this->generateFormattedHeader($e) . "\n" . $this->generateFormattedTrace($e->getTrace()));
        
        $depthCounter = 1;
        $previous     = $e->getPrevious();
        while ($previous !== null && $depthCounter < $maxDepth) {
            $msg[] =
                $this->generateFormattedHeader($previous, false) . "\n" .
                $this->generateFormattedTrace($previous->getTrace());
            
            $previous = $previous->getPrevious();
            $depthCounter++;
        }
        
        return implode("\n\n", $msg);
    }
    
    /**
     * @param Exception|Throwable $e
     * @param bool                $first
     *
     * @return string
     */
    private function generateFormattedHeader($e, $first = true)
    {
        $prefix = $first
            ? ""
            : "caused by ";
        
        $exceptionClass = get_class($e);
        
        return "{$prefix}{$exceptionClass}: {$e->getMessage()}\n\nthrown from {$e->getFile()} on line {$e->getLine()}";
    }
    
    /**
     * @param array $traces
     *
     * @return string
     */
    private function generateFormattedTrace(array $traces)
    {
        $formattedTraceLines = array();
        $traces              = array_reverse($traces);
        foreach ($traces as $trace) {
            $arguments = $this->namedParameterProvider->get($trace['class'], $trace['function'], $trace['args']);
            $arguments = $this->obfuscator->cleanParamsForLog($arguments);
            
            array_walk($arguments, array($this, 'flatten'));
            $arguments = implode(', ', $arguments);
            
            $formattedTraceLines[] =
                "at {$trace['class']}{$trace['type']}{$trace['function']}({$arguments}) " .
                "called in {$trace['file']}:{$trace['line']}";
        }
        
        return implode("\n", $formattedTraceLines);
    }
    
    /**
     * Function to be passed to array_walk(); will remove sub-arrays or objects.
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @post $value contains 'Object' if it was an object before.
     * @post $value contains 'Array' if it was an array before.
     * @pist $value contains 'true' / 'false' if it was boolean true / false before.
     * @post $value is left untouched if it was any other simple type before.
     */
    private function flatten(
        &$value,
        /** @noinspection PhpUnusedParameterInspection */
        $key
    ) {
        if (is_object($value)) {
            $value = 'Object';
        }
        
        if (is_array($value)) {
            $value = 'Array';
        }
        
        if (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        }
    }
}