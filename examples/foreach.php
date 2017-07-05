<?php
class Gen {
  public $values = [];
  public function fetch()
  {
    foreach($this->values as $v) {
      yield $v;
    }
  }
}

$fiddle->export(function () {
  foreach([] as $plok) {
    echo 'boink';
  }

  $gen = new Gen;
  $gen->values = ['plok', 'foo', 'bar'];
  $ret = [];
  foreach($gen->fetch() as $value) {
    $ret[] = $value;
  }

  return $ret;
});