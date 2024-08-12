<?php

namespace smallruraldog\admin\controller;


use smallruraldog\admin\component\Form;
use smallruraldog\admin\component\Grid;
use support\Request;
use support\Response;


/**
 * @method Grid grid(Request $request)
 * @method form(Request $request)
 */
class AdminController extends AdminBase
{
    /**
     * @var bool 是否创建界面
     */
    protected bool $isCreate = false;
    /**
     * @var bool 是否编辑界面
     */
    protected bool $isEdit = false;
    /**
     * @var bool 是否新增提交
     */
    protected bool $isStore = false;
    /**
     * @var bool 是否修改提交
     */
    protected bool $isUpdate = false;
    /**
     * @var mixed|null 当前更新的id
     */
    protected mixed $resourceKey = null;


    public function index(Request $request): Response
    {
        return jsonData($this->grid($request)->render());
    }


    public function create(Request $request): Response
    {
        $this->isCreate = true;
        return jsonData($this->form($request)->render());
    }

    public function edit(Request $request, $id): Response
    {
        $this->isEdit = true;
        $this->resourceKey = $id;
        return jsonData($this->form($request)->edit($id)->render());
    }


    public function update(Request $request, $id)
    {
        $this->resourceKey = $id;
        $this->isUpdate = true;

        /**@var Form $form */
        $form = $this->form($request);

        if ($id === "quickSave") {
            return $form->quickUpdate();
        }
        if ($id === "quickSaveItem") {
            return $form->quickItemUpdate();
        }
        return $form->update($id);
    }


    public function store(Request $request)
    {
        $this->isStore = true;
        return $this->form($request)->store();
    }


    public function destroy(Request $request, $id)
    {
        $this->resourceKey = $id;
        return $this->form($request)->destroy($id);
    }
}