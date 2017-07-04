<?php
include_once(dirname(__FILE__).'/vendor/autoload.php');


class Fiddle
{
    protected static $withExecutionTime = false;
    protected static $withHighlight = true;
//display
    public static function start()
    {
        include_once(dirname(__FILE__).'/header.inc.php');
    }
    public static function end()
    {
        include_once(dirname(__FILE__).'/footer.inc.php');
    }
    public static function withExecutionTime($tf = true)
    {
        self::$withExecutionTime = $tf;
    }
    public static function withHighlight($tf = true)
    {
        self::$withHighlight = $tf;
    }

//actions
    public static function play($function)
    {
        $source_code = self::loadCode($function);
        $start_time = microtime(true);
        $function_return = $function();
        $time = microtime(true) - $start_time;
        self::display($function_return, $source_code, $time);
    }

    public static function export($function)
    {
        $source_code = self::loadCode($function);
        $start_time = microtime(true);
        $function_return = $function();
        $time = microtime(true) - $start_time;
        self::display(var_export($function(), true), $source_code, $time);
    }

    protected static function loadCode($function)
    {
        if (is_callable($function)) {
            $source_code = self::dumpClosure($function);
        } else {
            $source_code = self::dumpFunction($function);
        }

        return $source_code;
    }

    /**
     * @link http://stackoverflow.com/questions/7026690/reconstruct-get-code-of-php-function
     * @param string $function
     * @return string
     */
    protected static function dumpFunction($function)
    {
        $func = new ReflectionFunction($function);
        $filename = $func->getFileName();
        $start_line = $func->getStartLine() - 1; // it's actually - 1, otherwise you wont get the function() block
        $end_line = $func->getEndLine();
        $length = $end_line - $start_line;
        $source = file($filename);
        $source_code = implode("", array_slice($source, $start_line, $length));

        return $source_code;
    }

    /**
     * @link http://stackoverflow.com/questions/25586109/view-a-php-closures-source
     * @param callable $closure
     * @return string
     */
    protected static function dumpClosure($closure)
    {
        $source_code = 'function (';
        $reflection = new ReflectionFunction($closure);
        $params = array();

        foreach ($reflection->getParameters() as $parameter) {
            $param_string = '';

            if ($parameter->isArray()) {
                $param_string .= 'array ';
            } else if ($parameter->getClass()) {
                $param_string .= $parameter->getClass()->name . ' ';
            }

            if ($parameter->isPassedByReference()) {
                $param_string .= '&';
            }

            $param_string .= '$' . $parameter->name;

            if ($parameter->isOptional()) {
                $param_string .= ' = ' . var_export($parameter->getDefaultValue(), TRUE);
            }

            $params[] = $param_string;
        }

        $source_code .= implode(', ', $params);
        $source_code .= ') ';

        if ($reflection->getStaticVariables()) {
            $used_variables = [];
            array_walk($reflection->getStaticVariables(), function($var, $key) use (&$used_variables) {
                $used_variables[] = '$' . $key;
            });
            $source_code .= PHP_EOL . 'use ('.implode(', ', $used_variables).') ';
        }

        $source_code .= '{' . PHP_EOL;
        $lines = file($reflection->getFileName());

        for ($l = $reflection->getStartLine(); $l < $reflection->getEndLine(); $l++) {
            $source_code .= $lines[$l];
        }

        return $source_code;
    }

    protected static function display($function_return, $function_source_code, $execution_time)
    {
        if (self::$withHighlight) {
            $h = new Highlight\Highlighter();
            $highlighted_source_code = $h->highlight('php', $function_source_code);
            $function_source_code = $highlighted_source_code->value;
        }
        echo '<table class="display">';
        echo '<thead><tr>';
        echo '<th>Source Code</th>';
        echo '<th>Return</th>';
        echo '</tr></thead>';
        echo '<tbody><tr>';
        echo '<td style="width:50%"><pre class="hljs php">' . $function_source_code . '</pre></td>';
        echo '<td style="width:50%"><pre>' . $function_return . '</pre></td>';
        echo '</tr></tbody>';
        if (self::$withExecutionTime) {
            echo '<tfoot><tr><td colspan="2">Execution time : ' . sprintf('%.5f', $execution_time * 1000). ' seconds</td></tr></tfoot>';
        }
        echo '</table>';
    }
}