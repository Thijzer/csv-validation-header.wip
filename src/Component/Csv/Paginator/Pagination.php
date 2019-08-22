<?php

namespace Misery\Component\Csv\Paginator;

use Misery\Component\Csv\Reader\CsvCursorInterface;

class Pagination
{
    private $nbResults = 0;
    private $pageId = 0;
    private $limit = 0;

    public static function calculate(CsvCursorInterface $cursor, int $pageId, int $limit): Pagination
    {
        $self = new self();
        $self->nbResults = $cursor->count();
        $self->pageId = $pageId;
        $self->limit = $limit;

        return $self;
    }

    public function getUpset(): int
    {
        return $this->limit . $this->pageId;
    }

    public function getPageOffset(): int
    {
        return ($this->limit * $this->pageId) - $this->limit;
    }

    public function getNbResults(): int
    {
        return $this->nbResults;
    }

    public function getNbPages(): int
    {
        return $this->nbResults / $this->limit;
    }

    private function minimumNbPages(): int
    {
        return 1;
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

    public function count(): int
    {
        return $this->getNbResults();
    }

    public function canPaginate(): bool
    {
        return $this->getNbResults() > $this->limit;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }
}