<?php


namespace App\Service\MovieDB\Model;


class StorageModelArray extends \ArrayObject
{
    public function offsetSet($key, $value) {
        if ($value instanceof StorageModel) {
            parent::offsetSet($key, $value);
            return;
        }
        throw new \InvalidArgumentException('Value must be an StorageModel');
    }
}