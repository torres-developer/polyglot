<?php

namespace TorresDeveloper\Polyglot;

use Generator;

class Lang
{
    private string $code;
    private ?string $name;
    private array $neighbors = [];

    private ?Lang $parent;

    public function __construct(string $code, ?string $name = null)
    {
        $this->code = $code;
        $this->name = $name;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isAdjacent(Lang $lang): bool
    {
        return isset($this->neighbors[$lang->getCode()]);
    }

    public function getNeighbors(): array
    {
        return $this->neighbors;
    }

    public function addNeighbor(Lang $lang): void
    {
        $this->neighbors[$lang->getCode()] = $lang;
    }

    public function getParent(): Lang
    {
        return $this->parent;
    }
    public function setParent(?Lang $lang): void
    {
        $this->parent = $lang;
    }

    public function hasParent(): bool
    {
        return isset($this->parent);
    }

    public function shortPathTo(Lang $lang): Generator
    {
        $q = new \SplQueue();
        $q->enqueue($this);

        /** @var Lang[] $explored */
        $explored = [];

        while (!$q->isEmpty()) {
            $l = $q->dequeue();

            if ($l->getCode() == $lang->getCode()) {
                foreach ($this->createPath($l) as $vertix) {
                    yield $vertix;
                }

                break;
            }

            foreach ($this->getNeighbors() as $neighbor) {
                if (!$neighbor->hasParent()) {
                    $explored[] = $neighbor;
                    $neighbor->setParent($l);
                    $q->enqueue($neighbor);
                }
            }
        }

        /** @var Lang $visited */
        foreach ($explored as $visited) {
            $visited->setParent(null);
        }
    }

    private function createPath(Lang $lang): array
    {
        $path = [];

        do {
            $path = [$lang, ...$path];

            $parent = $lang->getParent();
            $lang->setParent(null);
            $lang = $parent;
        } while ($lang->hasParent());

        return $path;
    }
}
