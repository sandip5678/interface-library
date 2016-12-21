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
 * Tries to get the argument names from a function and map the actual values it was called with to them using Reflection.
 */
class Shopgate_Helper_Logging_Stack_Trace_NamedParameterProviderReflection
    implements Shopgate_Helper_Logging_Stack_Trace_NamedParameterProviderInterface
{
    /** @var array [string, ReflectionParameter[]] */
    protected $functionArgumentsCache;
    
    public function __construct()
    {
        $this->functionArgumentsCache = array();
    }
    
    public function get($className, $functionName, array $arguments)
    {
        if (!empty($className) && !class_exists($className)) {
            return $arguments;
        }
        
        if (!$this->exists($className, $functionName)) {
            return $arguments;
        }
        
        $fullFunctionName                                = $this->getFullFunctionName($className, $functionName);
        $this->functionArgumentsCache[$fullFunctionName] = array();
        
        $namedArguments = $this->getNamedArguments($className, $functionName, $arguments);
        
        for ($i = count($namedArguments); $i < count($arguments); $i++) {
            $namedArguments['unnamed argument ' . $i] = $arguments[$i];
        }
        
        return $namedArguments;
    }
    
    /**
     * @param string  $className
     * @param string  $functionName
     * @param mixed[] $arguments The list of arguments.
     *
     * @return array [string, mixed] An array of the arguments with named indices according to function parameter names.
     */
    private function getNamedArguments($className, $functionName, array $arguments)
    {
        $fullFunctionName = $this->getFullFunctionName($className, $functionName);
        
        if (empty($this->functionArgumentsCache[$fullFunctionName])) {
            $this->functionArgumentsCache[$fullFunctionName] =
                $this->buildReflectionFunction($className, $functionName)->getParameters();
        }
        
        $i              = 0;
        $namedArguments = array();
        foreach ($this->functionArgumentsCache[$fullFunctionName] as $parameter) {
            /** @var ReflectionParameter $parameter */
            try {
                $defaultValue = '[defaultValue:' . $parameter->getDefaultValue() . ']';
            } catch (ReflectionException $e) {
                $defaultValue = '';
            }
            
            $namedArguments[$parameter->getName()] = isset($arguments[$i]) ? $arguments[$i] : $defaultValue;
            $i++;
        }
        
        return $namedArguments;
    }
    
    /**
     * @param string $className
     * @param string $functionName
     *
     * @return string
     */
    private function getFullFunctionName($className, $functionName)
    {
        return empty($className)
            ? $functionName
            : $className . '::' . $functionName;
    }
    
    /**
     * @param string $className
     * @param string $functionName
     *
     * @return ReflectionFunctionAbstract
     */
    private function buildReflectionFunction($className, $functionName)
    {
        return (empty($className))
            ? new ReflectionFunction($this->getFullFunctionName($className, $functionName))
            : new ReflectionMethod($className, $functionName);
    }
    
    /**
     * @param string $className
     * @param string $functionName
     *
     * @return bool
     */
    private function exists($className, $functionName)
    {
        return
            function_exists($this->getFullFunctionName($className, $functionName))
            || method_exists($className, $functionName);
    }
}