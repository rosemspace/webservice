<?php

namespace Rosem\Http\Factory;

use Psr\Http\Message\StreamInterface;
use Psrnext\Http\Factory\StreamFactoryInterface;
use Zend\Diactoros\Stream;

class StreamFactory implements StreamFactoryInterface
{
    public function createStream($content = '') : StreamInterface
    {
        $resource = fopen('php://temp', 'r+');
        fwrite($resource, $content);
        rewind($resource);

        return $this->createStreamFromResource($resource);
    }

    public function createStreamFromFile($file, $mode = 'r') : StreamInterface
    {
        $resource = fopen($file, $mode);

        return $this->createStreamFromResource($resource);
    }

    public function createStreamFromResource($resource) : StreamInterface
    {
        return new Stream($resource);
    }
}
