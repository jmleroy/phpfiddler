<?php

class Collection
{
    protected $collection = [];

    public function __construct($collection = [])
    {
        if (is_array($collection)) {
            $this->collection = $collection;
        }
    }

    public function getCollection()
    {
        return $this->collection;
    }

    public function search()
    {
        return new CollectionQuery($this->collection);
    }

    public function push($value)
    {
        $this->collection[] = $value;
    }
}

class CollectionQuery
{
    public $where;

    protected $valid_operators = [
        '=', '==', 'equals',
        '!=', 'not equals', '<>',
        '===', 'strictly equals',
        '!==', 'strictly not equals',
        '=~', 'similar to',
        '<', 'lower than',
        '>', 'greater than',
        '<=', 'lower than or equals',
        '>=', 'greater than or equals',
        'in',
        'exists',
        'is null',
        'is not null',
    ];
    protected $valid_connections = ['and', 'or'];
    protected $collection;
    protected $trace = false;

    public function __construct($collection)
    {
        $this->setCollection($collection);
    }

    public function setCollection($collection)
    {
        $this->collection = $collection;
    }

    public function search()
    {
        $this->where = [];

        return $this;
    }

    static public function searchIn($collection)
    {
        $that = new self($collection);

        return $that->search();
    }

    public function get()
    {
        $search_result = [];

        if (count($this->where)) {
            foreach ($this->collection as $elem) {
                $keep = false;
                $next_connection = false;

                foreach ($this->where as $where) {
                    $value = $this->getElementValue($elem, $where['key']);
                    $assertion = $this->assertCondition($value, $where['operator'], $where['value']);

                    if (!$next_connection) {
                        $keep = $assertion;
                    } elseif ($next_connection == 'and') {
                        $keep = $keep && $assertion;
                    } elseif ($next_connection == 'or') {
                        $keep = $keep || $assertion;
                    }

                    $next_connection = $where['connection'];
                }

                if ($keep) {
                    $search_result[] = $elem;
                }
            }
        } else {
            $search_result = $this->collection;
        }

        return $search_result;
    }

    public function first()
    {
        $ret = $this->get();

        if (count($ret)) {
            return reset($ret);
        }

        return null;
    }

    public function where($key = null, $operator = 'exists', $value = null, $connection = 'and')
    {
        $operator = strtolower(trim($operator));
        $connection = strtolower(trim($connection));

        if (!$this->isValidOperator($operator)) {
            throw new Exception('Valid operators are: ' . join(', ', $this->valid_operators));
        }

        if (!$this->isValidConnection($connection)) {
            throw new Exception('Valid connections are: ' . join(', ', $this->valid_connections));
        }

        $this->where[] = compact('key', 'operator', 'value', 'connection');

        return $this;
    }

    public function whereValue($operator = '=', $value = null, $connection = 'and')
    {
        return $this->where(null, $operator, $value, $connection);
    }

    public function trace($state = true)
    {
        $this->trace = $state;
        return $this;
    }

    protected function isValidOperator($operator)
    {
        return in_array($operator, $this->valid_operators);
    }

    protected function isValidConnection($connection)
    {
        return in_array($connection, $this->valid_connections);
    }

    public function getElement($key = 0)
    {
        if(isset($this->collection[$key])) {
            return $this->collection[$key];
        }

        /** @todo throw new ElementNotFoundException */
        return null;
    }

    public function getElementValue($target, $key = null)
    {
        // if the value to return is an element of a flat array
        if (is_null($key)) {
            return $target;
        }

        if (is_array($target)) {
            // if the value to return is an element of an associative array
            if ((is_int($key) || is_string($key)) && array_key_exists($key, $target)) {
                return $target[$key];
            }
        } elseif (is_object($target)) {
            if (is_array($key)) {
                // if the value to return is the result of a method with enough parameters to apply to the target
                $arguments = array_values($key);
                $method = array_shift($arguments);

                if (method_exists($target, $method)) {
                    $m = new ReflectionMethod($target, $method);

                    if (count($arguments) >= $m->getNumberOfRequiredParameters()) {
                        return call_user_func_array(array($target, $method), $arguments);
                    }
                }
            } else {
                // if the value to return is a property of the target
                if (property_exists($target, $key)) {
                    return $target->$key;
                }

                // if the value to return is the result of a method without parameter to apply to the target
                if (method_exists($target, $key)) {
                    return $target->$key();
                }

                // if the value to return is the result of a getter method to apply to the target
                $getter = 'get' . ucfirst($key);
                if (method_exists($target, $getter)) {
                    return $target->{$getter}();
                }
            }

        }

        /** @todo throw new ElementNotFoundException */
        return null;
    }

    protected function assertCondition($value_from_collection, $operator, $tested_value)
    {
        if (in_array($operator, ['=', '==', 'equals'])) {
            return $value_from_collection == $tested_value;
        }

        if ($operator == '===' || $operator == 'strictly equals') {
            return $value_from_collection === $tested_value;
        }

        if (in_array($operator, ['!=', '<>', 'not equals'])) {
            return $value_from_collection != $tested_value;
        }

        if ($operator == '!==' || $operator == 'strictly not equals') {
            return $value_from_collection !== $tested_value;
        }

        if ($operator == '>' || $operator == 'greater than') {
            return $value_from_collection > $tested_value;
        }

        if ($operator == '<' || $operator == 'lower than') {
            return $value_from_collection < $tested_value;
        }

        if ($operator == '>=' || $operator == 'greater than or equals') {
            return $value_from_collection >= $tested_value;
        }

        if ($operator == '<=' || $operator == 'lower than or equals') {
            return $value_from_collection <= $tested_value;
        }

        if ($operator == '=~' || $operator == 'similar to') {
            return preg_match($tested_value, $value_from_collection);
        }

        if ($operator == 'in') {
            return in_array($value_from_collection, $tested_value);
        }

        if ($operator == 'exists') {
            return $value_from_collection ? true : false;
        }

        if ($operator == 'is null') {
            return is_null($value_from_collection);
        }

        if ($operator == 'is not null') {
            return !is_null($value_from_collection);
        }

        /** @todo throw new UnresolvedAssertionException */
        return false;
    }
}

