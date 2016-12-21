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
interface Shopgate_Helper_Logging_Stack_Trace_NamedParameterProviderInterface
{
    /**
     * Maps numerically indexed arguments to a function or method to its named parameters if it is available and callable.
     *
     * @param string   $className    The name of the class or an empty string if not referring to a method.
     * @param string   $functionName The name of the function.
     * @param string[] $arguments    The arguments the function was called with.
     *
     * @return array [int|string, string] An array with the argument names as keys or the untouched $arguments if names could not be determined.
     */
    public function get($className, $functionName, array $arguments);
}