<?php

namespace Infrastructure\Persistence;

class FlushList extends \ArrayObject
{
    /**
     * @var callable
     */
    private $invokeOnFlush;

    /**
     * @var int
     */
    private $chunkSize;

    public function __construct(callable $invokeOnFlush, $chunkSize = 500)
    {
        parent::__construct();
        $this->invokeOnFlush = $invokeOnFlush;
        $this->chunkSize = $chunkSize;
    }

    public function getChunkSize()
    {
        return $this->chunkSize;
    }

    public function addAll(\Traversable $traversable)
    {
        foreach ($traversable as $k => $e) {
            $this[$k] = $e;
        }

        $this->flush();
    }

    public function offsetSet($index, $newval)
    {
        parent::offsetSet($index, $newval);

        if ($this->chunkSize > -1 && $this->count() === $this->chunkSize) {
            $this->flush();
        }
    }

    public function flush()
    {
        if (!$this->count()) {
            return;
        }

        call_user_func($this->invokeOnFlush, $this);
        $this->clear();
    }

    public function clear()
    {
        $this->exchangeArray([]);
        gc_collect_cycles();
    }
}
