<?php

namespace smallruraldog\admin\component\grid\trait;

trait GridTree
{
    private bool $toTree = false;
    private ?string $toTreeKey = null;
    private string $toTreeParentKey = "parent_id";
    private string $toTreeChildrenName = "parent_id";


    public function isToTree(): bool
    {
        return $this->toTree;
    }

    public function getToTreeKey(): string
    {
        if ($this->toTreeKey) {
            return $this->toTreeKey;
        }
        return $this->builder()->getModel()->getKeyName();
    }

    public function getToTreeParentKey(): string
    {
        return $this->toTreeParentKey;
    }

    public function getToTreeChildrenName(): string
    {
        return $this->toTreeChildrenName;
    }

    public function toTree(string $toTreeKey = null, string $toTreeParentKey = "parent_id", string $toTreeChildrenName = "children"): self
    {
        if (!$toTreeKey) {
            $toTreeKey = $this->builder()->getModel()->getKeyName();
        }
        $this->toTree = true;
        $this->toTreeKey = $toTreeKey;
        $this->toTreeParentKey = $toTreeParentKey;
        $this->toTreeChildrenName = $toTreeChildrenName;
        return $this;
    }
}