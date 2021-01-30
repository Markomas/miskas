<?php


namespace App\Service\Crawler\Model;


use ArrayAccess;
use Exception;

class TorrentModelArray extends \ArrayObject
{
    public function offsetSet($key, $value) {
        if ($value instanceof TorrentModel) {
            parent::offsetSet($key, $value);
            return;
        }
        throw new \InvalidArgumentException('Value must be an TorrentModel');
    }
}