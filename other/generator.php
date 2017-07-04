<?php

function generator_example($factor, $start = 0) {
  $i = $start;
  do {
    yield $i => $i * $factor;
    $i++;
  } while($i);
}

echo '<h1>Generator example</h1>';

$factor = 10;
$start = 20;
foreach(generator_example($factor, $start) as $unit => $value) {
  echo $unit.' &times; '.$factor.' = '.$value.' !<br>';
  if($value >= 1000) {
    break;
  }
}

echo 'finished at value='.$value.'.<br>';
/*
class MockCursor// implements Zend_Db_Statement_Interface
{
  protected $data;

  public function __construct($data)
  {
    $this->data = $data;
  }

  public function fetch($style = null, $cursor = null, $offset = null)
  {
    foreach($this->data as $row) {
      yield $row;
    }
    yield false;
  }
}

echo 'start!';
echo '<pre>';
$mock = new MockCursor( [ [1,2,3], [3,4,5], [5,6,7] ] );
//foreach($mock->fetch() as $row) {

do {
  $row = $mock->fetch();
  var_dump($row);
} while($row !== false);

echo '</pre>';
echo "finished!";
