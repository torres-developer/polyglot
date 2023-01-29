<?php

namespace TorresDeveloper\Polyglot;

class LangsGraph
{
    /**
     * @var Lang[] $vertices
     */
    private array $vertices = [];

    public function addVertix(Lang ...$langs): void
    {
        foreach ($langs as $lang) {
            if (!isset($this->vertices[$lang->getCode()])) {
                $this->vertices[$lang->getCode()] = $lang;
            }
        }
    }

    public function addEdge(Lang $to, Lang $from): void
    {
        $this->addVertix($to, $from);

        $this->vertices[$to->getCode()]->addNeighbor(
            $this->vertices[$from->getCode()]
        );
    }

    public function getVertix(Lang $lang): ?Lang
    {
        return $this->vertices[$lang->getCode()] ?? null;
    }
}
