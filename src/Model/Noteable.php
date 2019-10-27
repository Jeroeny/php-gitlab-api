<?php

declare(strict_types=1);

namespace Gitlab\Model;

interface Noteable
{
    public function addComment(string $comment): Note;

    /**
     * @return Note[]
     */
    public function showComments(): array;

    /**
     * @return static
     */
    public function close(?string $comment = null);

    /**
     * @return static
     */
    public function open();

    /**
     * @return static
     */
    public function reopen();

    public function isClosed(): bool;
}
