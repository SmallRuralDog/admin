<?php

namespace smallruraldog\admin\controller;


use Exception;
use Respect\Validation\Validator as v;
use smallruraldog\admin\Admin;
use smallruraldog\admin\model\SystemUser;
use smallruraldog\admin\renderer\form\AmisForm;
use smallruraldog\admin\renderer\form\InputText;
use support\Request;
use support\Response;

class AuthController extends AdminBase
{

    public array $noNeedLogin = ['login', 'captcha'];

    public array $noNeedPermission = ['userSetting'];

    public function login(Request $request): Response
    {
        try {
            $data = v::input($request->all(), [
                'username' => v::notEmpty()->setName('用户名'),
                'password' => v::notEmpty()->length(5, 64)->setName('密码'),
            ]);

            /** @var SystemUser $adminUser */
            $adminUser = SystemUser::query()->where('username', $data['username'])->first();
            abort_if(!$adminUser, 400, '用户不存在');
            $password = $adminUser->password;
            abort_if(!password_verify($data['password'], $password), 400, '密码错误');

            session()->set('admin_id', $adminUser->id);

            return jsonData([
                'access_token' => $adminUser->id,
            ]);
        } catch (Exception $exception) {
            return jsonError($exception->getMessage());
        }

    }


    public function logout(Request $request)
    {
        try {
            session()->remove('admin_id');
            return jsonData("退出成功");
        } catch (Exception $exception) {
            return jsonError($exception->getMessage());
        }
    }

    public function userSetting(Request $request)
    {

        if ($request->method() == 'POST') {
            try {


                $name = $request->input('name');

                $old_password = $request->input('old_password');
                $new_password = $request->input('new_password');
                $confirm_password = $request->input('new_password_confirmation');

                abort_if($new_password && $new_password != $confirm_password, 400, '两次密码不一致');

                $update = [];

                if ($name) {
                    $update['name'] = $name;
                }


                //验证旧密码是否正确
                if ($new_password) {
                    $userPassword = SystemUser::query()->find(Admin::userId())->value('password');
                    abort_if(password_verify($old_password, $userPassword) == false, 400, '旧密码错误');
                    $update['password'] = password_hash($new_password, PASSWORD_DEFAULT);
                }
                $update = SystemUser::query()->where('id', Admin::userId())->update($update);
                return amisSuccess("修改成功");
            } catch (Exception $e) {
                return amisError($e->getMessage());
            }

        }


        $form = AmisForm::make()
            ->title('个人设置')
            ->data(Admin::userInfo()->only(['username', 'name']))
            //->resetAfterSubmit(true)
            ->api(route('admin.userSetting'));
        $form->body([
            InputText::make()->name('username')->label('用户名')->readOnly(true)->disabled(true),
            InputText::make()->name('name')->label('名称')->required(true)->placeholder("请输入名称"),
            InputText::make()->password()->name('old_password')->label('旧密码')->placeholder("请输入旧密码"),
            InputText::make()->password()->name('new_password')->label('密码')->placeholder("请输入密码"),
            InputText::make()->password()->name('new_password_confirmation')->label('确认密码')->placeholder("请输入确认密码"),
        ]);
        return jsonData($form);
    }

}