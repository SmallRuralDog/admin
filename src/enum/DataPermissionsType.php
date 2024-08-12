<?php

namespace smallruraldog\admin\enum;


enum DataPermissionsType: string
{
    use BaseEnums;

    /**
     * 本人
     */
    case SELF = '1';

    /**
     * 本部门
     */
    case DEPT = '2';

    /**
     * 本部门及子部门
     */
    case DEPT_AND_SUB = '3';

    /**
     * 全部
     */
    case ALL = '99';

}