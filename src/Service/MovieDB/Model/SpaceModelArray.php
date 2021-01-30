<?php


namespace App\Service\MovieDB\Model;


class SpaceModelArray extends \ArrayObject
{
    public function offsetSet($key, $value) {
    if ($value instanceof SpaceModel) {
        parent::offsetSet($key, $value);
        return;
    }
    throw new \InvalidArgumentException('Value must be an SpaceModel');
}
}