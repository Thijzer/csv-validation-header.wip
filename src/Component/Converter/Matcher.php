<?php

namespace Misery\Component\Converter;

class Matcher
{
    private string $separator = '|';
    private array $matches = [];
    private bool $scopable = false;
    private bool $localizable = false;

    public static function create(string $mainMatch, ?string $locale = null, ?string $scope = null): self
    {
        $main = new self();
        $main->localizable = is_string($locale);
        $main->scopable = is_string($scope);

        $matches = explode($main->separator, $mainMatch);
        if ($main->localizable) {
            $matches[] = $locale;
        }
        if ($main->scopable) {
            $matches[] = $scope;
        }
        $main->matches = $matches;

        return $main;
    }

    public function getPrimaryKey(): string
    {
        return $this->matches[1];
    }

    public function getRowKey(): string
    {
        return str_replace('values-', '', str_replace($this->separator, '-', $this->getMainKey()));
    }

    public function getMainKey(): string
    {
        return implode($this->separator, $this->matches);
    }

    public function matches(string $match): bool
    {
        return in_array($match, $this->matches);
    }

    public function duplicateWithNewKey(string $newPrimaryKey): self
    {
        $matcher = new self();
        $matcher->scopable = $this->scopable;
        $matcher->localizable = $this->localizable;
        $matcher->matches = $this->matches;
        $matcher->matches[1] = $newPrimaryKey;

        return $matcher;
    }
}