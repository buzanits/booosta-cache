<?php
namespace booosta\cache;

use \booosta\Framework as b;
b::init_module('cache');

class Cache extends \booosta\base\Module
{
  use moduletrait_cache;

  protected $validity = 86400;
  protected $store;


  public function set_validity($val) { $this->validity = $val; }

  public function get($key)
  {
    $result = $this->readstore($key);
    if($result === false || $this->is_invalid($key)) $result = $this->savestore($key);

    return $result;
  }

  protected function savestore($key)
  {
    $result = $this->retrieve($key);
    $this->store->storeobj($key, $result);
    return $result;
  }

  public function is_valid($key)
  {
    return $this->get_timestamp($key) >= time() - $this->validity;
  }

  public function is_invalid($key) { return !$this->is_valid($key); }
  protected function readstore($key) { return $this->store->getobj($key); }
  protected function get_timestamp($key) { return $this->store->get_timestamp($key); }
  public function invalidate($key) { $this->store->invalidate($key); }
  public function clear() { return $this->store->clear(); }
  public function cleanup() { return $this->store->cleanup(); }

  protected function retrieve($key) { return file_get_contents($key); }
}


abstract class Cachestore extends \booosta\base\Module
{
  abstract public function getobj($key);
  abstract public function storeobj($key, $obj);
  abstract public function get_timestamp($key);
  abstract public function invalidate($key);
  abstract public function clear();
  abstract public function cleanup();
}
