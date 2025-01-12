<?php

namespace Reinder83\BinaryFlags;

use Countable;
use Iterator;
use JsonSerializable;

/**
 * This class holds useful methods for checking, adding or removing binary flags
 *
 * @author Reinder
 */
abstract class BinaryFlags implements Iterator, Countable, JsonSerializable
{
    use Traits\BinaryFlags;

    private int $currentPos = 0;

    public function __construct(int $mask = 0, ?callable $onModify = null)
    {
        $this->setMask($mask);

        // set onModify callback if specified
        if ($onModify !== null) {
            $this->setOnModifyCallback($onModify);
        }
    }

    /**
     * Return the current element
     *
     * @return string the description of the flag or the name of the constant
     * @since 1.2.0
     */
    public function current(): mixed
    {
        return $this->getFlagNames($this->currentPos);
    }

    /**
     * Move forward to next element
     *
     * @return void
     * @since 1.2.0
     */
    public function next(): void
    {
        $this->currentPos <<= 1; // shift to next bit
        while (($this->mask & $this->currentPos) == 0 && $this->currentPos > 0) {
            $this->currentPos <<= 1;
        }
    }

    /**
     * Return the key of the current element
     *
     * @return int the flag
     * @since 1.2.0
     */
    public function key(): mixed
    {
        return $this->currentPos;
    }

    /**
     * Checks if current position is valid
     */
    public function valid(): bool
    {
        return $this->currentPos > 0;
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @since 1.2.0
     */
    public function rewind(): void
    {
        // find the first element
        if ($this->mask === 0) {
            $this->currentPos = 0;

            return;
        }

        $this->currentPos = 1;
        while (($this->mask & $this->currentPos) == 0) {
            $this->currentPos <<= 1;
        }
    }

    /**
     * Returns the number of flags that are set
     */
    public function count(): int
    {
        $count = 0;
        $mask  = $this->mask;

        while ($mask != 0) {
            if (($mask & 1) == 1) {
                $count++;
            }
            $mask >>= 1;
        }

        return $count;
    }

    /**
     * Specify data which should be serialized to JSON
     */
    public function jsonSerialize(): array
    {
        return ['mask' => $this->mask];
    }
}
