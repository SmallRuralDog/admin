<?php

namespace smallruraldog\admin\component\grid\trait;

trait GridPermission
{
    protected bool $hasEditPermission = true;//是否有编辑权限
    protected bool $hasDeletePermission = true;//是否有删除权限
    protected bool $hasCreatePermission = true;//是否有创建权限

    public function hasEditPermission(): bool
    {
        return $this->hasEditPermission;
    }

    public function hasDeletePermission(): bool
    {
        return $this->hasDeletePermission;
    }

    public function hasCreatePermission(): bool
    {
        return $this->hasCreatePermission;
    }
}