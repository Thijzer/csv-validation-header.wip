<?php

namespace Misery\Component\Csv\Paginator;

use Html\Functions\PaginationInterface;
use Misery\Component\Common\Cursor\CursorInterface;
use Misery\Component\Csv\Reader\ReaderInterface;

class Pagination implements PaginationInterface
{
    private $pageId = 0;
    private $limit = 0;
    /** @var CursorInterface */
    private $cursor;

    public static function calculate(CursorInterface $cursor, int $pageId, int $limit): Pagination
    {
        $self = new self();
        $self->pageId = $pageId;
        $self->limit = $limit;
        $self->cursor = $cursor;

        return $self;
    }

    public function getPageUpset(): int
    {
        return $this->getPageOffset() + $this->limit -1;
    }

    public function getPageOffset(): int
    {
        return ($this->limit * $this->pageId) - $this->limit;
    }

    public function getNbResults(): int
    {
        return $this->cursor->count();
    }

    public function getNbPages(): int
    {
        return ceil($this->getNbResults() / $this->limit);
    }

    private function minimumNbPages(): int
    {
        return 1;
    }

    public function getCurrentPageResults(): array
    {
        $this->cursor->seek($this->getPageOffset()+1);

        $results = [];
        while ($this->cursor->valid()) {
            $results[$this->cursor->key()] = $this->cursor->current();
            if (\count($results) === $this->getPageLimit()) {
                break;
            }
            $this->cursor->next();
        }

        $this->cursor->rewind();

        return $results;
    }

    public function getCurrentPage(): int
    {
        return $this->pageId;
    }

    public function hasPreviousPage(): bool
    {
        return $this->pageId > 1;
    }

    public function getPreviousPage(): int
    {
        return $this->pageId - 1;
    }

    public function hasNextPage(): bool
    {
        return $this->pageId < $this->getNbPages();
    }

    public function getNextPage(): int
    {
        return $this->pageId + 1;
    }

    public function canPaginate(): bool
    {
        return $this->getNbResults() > $this->limit;
    }

    public function getPageLimit(): int
    {
        return $this->limit;
    }
}