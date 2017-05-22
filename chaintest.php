<?php

include('chain.php');

class DummyClass
{
    protected $name;
    protected $city;
    public $country;
    public $id;
    public $keep_it_null = null;

    public function __construct($name = '', $city = '', $country = '', $id = 0)
    {
        $this->name = $name;
        $this->city = $city;
        $this->country = $country;
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function __set($property, $value)
    {
        if (isset($this->$property)) {
            $this->$property = $value;
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getIdMultipliedBy($factor)
    {
        return $this->id * $factor;
    }

    public function getIdMultipliedByAndAppendWith($factor, $string_to_append)
    {
        return ($this->id * $factor) . $string_to_append;
    }
}

class ChainTest extends PHPUnit_Framework_TestCase
{
    public $collection;

    public function setUp()
    {
        $dummies = [];

        for ($i = 0; $i < 5; $i++) {
            $dummy = new DummyClass;
            $dummy->name = 'name' . $i;
            $dummy->city = 'city' . $i;
            $dummy->country = 'country' . $i;
            $dummy->id = $i;
            $dummies[] = $dummy;
        }

        $this->collection = new CollectionQuery($dummies);
    }

    public function testInitSearch()
    {
        $search = $this->collection->search();
        $this->assertTrue($search instanceof CollectionQuery);
        $this->assertTrue($search->where == []);
    }

    public function testGetElementValue()
    {
        $plok = 'abc';

        $search = $this->collection->search();
        $ret = $search->getElementValue($plok);
        $this->assertEquals($plok, $ret);

        $ret = $search->getElementValue($plok, 'foo');
        $this->assertNull($ret);

        $plok = [
            'name' => 'John',
            'city' => 'Houston'
        ];

        $ret = $search->getElementValue($plok, 'foo');
        $this->assertNull($ret);

        $ret = $search->getElementValue($plok, 'name');
        $this->assertEquals($plok['name'], $ret);

        $plok = $this->collection->getElement(0);

        // DummyClass::$name is protected and has a getter => value
        $ret = $search->getElementValue($plok, 'name');
        $this->assertEquals('name0', $ret);

        // DummyClass::$city is protected and has no getter => null
        $ret = $search->getElementValue($plok, 'city');
        $this->assertNull($ret);

        // DummyClass::$country is public => value
        $ret = $search->getElementValue($plok, 'country');
        $this->assertEquals('country0', $ret);
    }

    public function testSearch()
    {
        $search = $this->collection->search()
            ->where('country', '=', 'country1');
        $this->assertTrue(join(',', $search->where[0]) == 'country,=,country1,and');
        $this->assertEquals(1, count($search->get()));
    }

    public function testSearchExists()
    {
        $result = $this->collection->search()
            ->where('country', 'exists')
            ->get();
        $this->assertEquals(5, count($result));

        $result = $this->collection->search()
            ->where('country')
            ->get();
        $this->assertEquals(5, count($result));

        $result = $this->collection->search()
            ->where('id')
            ->get();
        $this->assertEquals(4, count($result));
        // Note: one of the 'id' values is 0

        $result = $this->collection->search()
            ->where('keep_it_null')
            ->get();
        $this->assertEquals(0, count($result));
    }

    public function testSearchIsNull()
    {
        $result = $this->collection->search()
            ->where('keep_it_null', 'is null')
            ->get();
        $this->assertEquals(5, count($result));

        $result = $this->collection->search()
            ->where('keep_it_null', '===', null)
            ->get();
        $this->assertEquals(5, count($result));

        $result = $this->collection->search()
            ->where('keep_it_null', '===', null, 'and')
            ->where('country', '=', 'country2')
            ->get();
        $this->assertEquals(1, count($result));
    }

    public function testSearchIsNotNull()
    {
        $result = $this->collection->search()
            ->where('keep_it_null', 'is not null')
            ->get();
        $this->assertEquals(0, count($result));

        $result = $this->collection->search()
            ->where('keep_it_null', '!==', null)
            ->get();
        $this->assertEquals(0, count($result));
    }

    public function testSearchStrictEquality()
    {
        $result = $this->collection->search()
            ->where('id', '===', '1')
            ->first();
        $this->assertNull($result);

        $result = $this->collection->search()
            ->where('id', '===', 1)
            ->first();
        $this->assertEquals(1, $result->id);

        $result = $this->collection->search()
            ->where('id', 'strictly equals', '1')
            ->first();
        $this->assertNull($result);

        $result = $this->collection->search()
            ->where('id', 'strictly equals', 1)
            ->first();
        $this->assertEquals(1, $result->id);
    }

    public function testSearchStrictInequality()
    {
        $result = $this->collection->search()
            ->where('id', '!==', '1')
            ->get();
        $this->assertEquals(5, count($result));

        $result = $this->collection->search()
            ->where('id', '!==', 1)
            ->get();
        $this->assertEquals(4, count($result));

        $result = $this->collection->search()
            ->where('id', 'strictly not equals', '1')
            ->get();
        $this->assertEquals(5, count($result));

        $result = $this->collection->search()
            ->where('id', 'strictly not equals', 1)
            ->get();
        $this->assertEquals(4, count($result));
    }

    public function testSearchFirst()
    {
        $result = $this->collection->search()
            ->where('country', '=', 'country1')
            ->first();
        $this->assertNotNull($result);
        $this->assertTrue($result instanceof DummyClass);
    }

    public function testSearchNoCondition()
    {
        $result = $this->collection->search()
            ->get();
        $this->assertTrue(is_array($result));
        $this->assertEquals(5, count($result));
    }

    public function testSearchOperatorEqual()
    {
        $result = $this->collection->search()
            ->where('country', '=', 'country4')
            ->get();
        $this->assertEquals(1, count($result));

        $result = $this->collection->search()
            ->where('country', '==', 'country2')
            ->first();
        $this->assertEquals(2, $result->getId());

        $result = $this->collection->search()
            ->where('country', 'equals', 'country4')
            ->first();
        $this->assertEquals(4, $result->getId());
    }

    public function testSearchOperatorDifferent()
    {
        $result = $this->collection->search()
            ->where('country', '<>', 'country2')
            ->first();
        $this->assertTrue($result instanceof DummyClass);

        $result = $this->collection->search()
            ->where('country', '!=', 'country4')
            ->get();
        $this->assertEquals(4, count($result));

        $result = $this->collection->search()
            ->where('country', 'not equals', 'country4')
            ->get();
        $this->assertEquals(4, count($result));
    }

    public function testSearchOperatorSimilarTo()
    {
        $result = $this->collection->search()
            ->where('country', '=~', '/^country/')
            ->get();
        $this->assertEquals(5, count($result));

        $result = $this->collection->search()
            ->where('country', 'similar to', '/^country[12]/')
            ->get();
        $this->assertEquals(2, count($result));
    }

    public function testSearchOperatorUpperThan()
    {
        $result = $this->collection->search()
            ->where('id', '>', 2)
            ->get();
        $this->assertEquals(2, count($result));

        $result = $this->collection->search()
            ->where('id', 'greater than', 3)
            ->get();
        $this->assertEquals(1, count($result));
    }

    public function testSearchOperatorLowerThan()
    {
        $result = $this->collection->search()
            ->where('id', '<', 2)
            ->get();
        $this->assertEquals(2, count($result));

        $result = $this->collection->search()
            ->where('id', 'lower than', 3)
            ->get();
        $this->assertEquals(3, count($result));
    }

    public function testSearchOperatorUpperOrEqualThan()
    {
        $result = $this->collection->search()
            ->where('id', '>=', 2)
            ->get();
        $this->assertEquals(3, count($result));

        $result = $this->collection->search()
            ->where('id', 'greater than or equals', 3)
            ->get();
        $this->assertEquals(2, count($result));
    }

    public function testSearchOperatorLowerOrEqualThan()
    {
        $result = $this->collection->search()
            ->where('id', '<=', 2)
            ->get();
        $this->assertEquals(3, count($result));

        $result = $this->collection->search()
            ->where('id', 'lower than or equals', 3)
            ->get();
        $this->assertEquals(4, count($result));
    }

    public function testSearchOperatorIn()
    {
        $result = $this->collection->search()
            ->where('id', 'in', [2, 3])
            ->get();
        $this->assertEquals(2, count($result));
    }

    public function testSearchOperatorInUppercase()
    {
        // Note: all operators are converted to lowercase
        $result = $this->collection->search()
            ->where('id', 'IN', [4])
            ->get();
        $this->assertEquals(1, count($result));
    }

    public function testSearchObjectMethod()
    {
        $result = $this->collection->search()
            ->where('getId', '=', 2)
            ->first();
        $this->assertTrue($result instanceof DummyClass);
        $this->assertEquals(2, $result->id);
    }

    public function testSearchObjectGetter()
    {
        $result = $this->collection->search()
            ->where('name', '=', 'name2')
            ->first();
        $this->assertTrue($result instanceof DummyClass);
        $this->assertEquals(2, $result->id);
    }

    public function testSearchObjectWrongTypeForMethod()
    {
        $result = $this->collection->search()
            ->where(4.65, '=', 2)
            ->first();
        $this->assertNull($result);

        $result = $this->collection->search()
            ->where(false, '=', 2)
            ->first();
        $this->assertNull($result);
    }

    public function testSearchObjectUnexistingMethod()
    {
        $result = $this->collection->search()
            ->where('unexistingMethod', '=', 2)
            ->get();
        $this->assertTrue(is_array($result));
        $this->assertEquals(0, count($result));

        $result = $this->collection->search()
            ->where('unexistingMethod', '=', 2)
            ->first();
        $this->assertNull($result);
    }

    public function testSearchObjectMethodWithParams()
    {
        $result = $this->collection->search()
            ->where(['getIdMultipliedByAndAppendWith', 3, 'plok'], '=', '6plok')
            ->first();
        $this->assertTrue($result instanceof DummyClass);
        $this->assertEquals(2, $result->id);
    }

    public function testSearchObjectMethodWithWrongParams()
    {
        $result = $this->collection->search()
            ->where(['getIdMultipliedByAndAppendWith', 'plok', 'foo'], '=', '6plok')
            ->first();
        $this->assertNull($result);
    }

    public function testSearchObjectMethodWithTooManyParamNumber()
    {
        $result = $this->collection->search()
            ->where(['getIdMultipliedByAndAppendWith', 3, 'plok', 'foo'], '=', '6plok')
            ->first();
        $this->assertTrue($result instanceof DummyClass);
        $this->assertEquals(2, $result->id);
    }

    public function testSearchObjectMethodWithTooFewParamsNumber()
    {
        $result = $this->collection->search()
            ->where(['getIdMultipliedByAndAppendWith'], '=', '6plok')
            ->first();
        $this->assertNull($result);
    }

    public function testSearchObjectUnexistingMethodWithParams()
    {
        $result = $this->collection->search()
            ->where(['getUnexistingMethod', 3, 'plok'], '=', '6plok')
            ->first();
        $this->assertNull($result);
    }

    public function testChainedWhereConditions()
    {
        $result = $this->collection->search()
            ->where('name', '=', 'name2', 'or')
            ->where('country', '=', 'country4')
            ->get();
        $this->assertEquals(2, count($result));


        $result = $this->collection->search()
            ->where('name', '=', 'name2', 'and')
            ->where('country', '=', 'country2')
            ->get();
        $this->assertEquals(1, count($result));
    }

    public function testSearchSimpleArray()
    {
        $collection = new Collection;

        for ($i = 0; $i < 5; $i++) {
            $collection->push($i);
        }
        for ($i = 5; $i < 10; $i++) {
            $collection->push('test' . $i);
        }

        $result = $collection->search()
            ->where(null, '=', 3)
            ->first();
        $this->assertEquals(3, $result);

        $result = $collection->search()
            ->whereValue('=', 2)
            ->first();
        $this->assertEquals(2, $result);

        $result = $collection->search()
            ->where(null, 'similar to', '/^tes/')
            ->first();
        $this->assertEquals('test5', $result);
    }

    public function testSearchAssociativeArray()
    {
        $collection = new Collection;

        for ($i = 0; $i < 5; $i++) {
            $value = [
                'key' => 'value' . $i,
                'value' => $i,
                'plok' => 'foo'
            ];
            $collection->push($value);
        }

        $result = $collection->search()
            ->where('key', '=', 'value3')
            ->first();
        $this->assertEquals(3, $result['value']);

        $result = $collection->search()
            ->where('plok', '=', 'foo')
            ->get();
        $this->assertEquals(5, count($result));

        $result = $collection->search()
            ->where('unexisting_key', '=', 'foo')
            ->get();
        $this->assertTrue(is_array($result));
        $this->assertEquals(0, count($result));

        $result = $collection->search()
            ->where(['methodName', 4], '=', 'foo')
            ->first();
        $this->assertNull($result);

        $result = $collection->search()
            ->where(4.65, '=', 2)
            ->first();
        $this->assertNull($result);

        $result = $collection->search()
            ->where(false, '=', 2)
            ->first();
        $this->assertNull($result);

        $result = $collection->search()
            ->where(null, '=', 2)
            ->first();
        $this->assertNull($result);
    }

    public function testLargeSetsOfObjects()
    {
        $dummies = [];
        $number_of_objects = 10000;

        for ($i = 0; $i < $number_of_objects; $i++) {
            $dummy = new DummyClass;
            $dummy->name = 'name' . $i;
            $dummy->city = 'city' . $i;
            $dummy->country = 'country' . $i;
            $dummy->id = $i;
            $dummies[] = $dummy;
        }

        $large_collection = new Collection($dummies);
        $result = $large_collection->search()
            ->where('name', '=~', '/^name3/')
            ->get();
        $this->assertEquals(1111, count($result));
    }

    public function testStaticMethodForSearch()
    {
        $collection = [];

        for ($i = 0; $i < 5; $i++) {
            $value = [
                'key' => 'value' . $i,
                'value' => $i,
                'plok' => 'foo'
            ];
            $collection[] = $value;
        }

        $result = CollectionQuery::searchIn($collection)
            ->where('key', '=', 'value3')
            ->first();
        $this->assertTrue(array_key_exists('value', $result));
        $this->assertEquals(3, $result['value']);
    }

    public function tearDown()
    {

    }
}