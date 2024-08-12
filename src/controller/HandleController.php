<?php

namespace smallruraldog\admin\controller;

use Exception;
use support\Request;
use Respect\Validation\Validator;

class HandleController extends AdminBase
{

    public array $noNeedPermission = ['action'];


    public function action(Request $request)
    {
        try {
            $data = $request->all();
            Validator::input($data, [
                'action' => Validator::notEmpty()->stringType(),
                'class' => Validator::notEmpty()->stringType(),
                'data' => Validator::arrayType(),
            ]);
            $class = base64_decode($data['class']);
            $action = $data['action'];
            $data = $data['data'];
            $res = (new $class())->$action($request,$data);
            return $res ?? amisSuccess("请求成功");
        } catch (Exception $e) {
            return amisError($e->getMessage());
        }
    }
}