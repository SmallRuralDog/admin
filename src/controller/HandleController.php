<?php

namespace smallruraldog\admin\controller;

use Exception;
use Shopwwi\WebmanFilesystem\Facade\Storage;
use Shopwwi\WebmanFilesystem\FilesystemFactory;
use support\Request;
use Respect\Validation\Validator;
use support\Response;

class HandleController extends AdminBase
{

    public array $noNeedPermission = ['action', 'uploadImage', 'uploadFile'];


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
            $res = (new $class())->$action($request, $data);
            return $res ?? amisSuccess("请求成功");
        } catch (Exception $e) {
            return amisError($e->getMessage());
        }
    }


    public function uploadImage(Request $request): Response
    {
        try {
            Validator::input($request->file(), [
                'file' => Validator::image()->setName('图片')
            ]);
            return $this->upload($request);

        } catch (Exception $exception) {
            return amisError($exception->getMessage());
        }
    }

    public function uploadFile(Request $request): Response
    {
        try {
            Validator::input($request->all(), [
                'file' => Validator::file()->setName('文件')
            ]);
            return $this->upload($request);

        } catch (Exception $exception) {
            return amisError($exception->getMessage());
        }
    }


    protected function upload(Request $request): Response
    {
        try {
            $file = $request->file('file');
            $disk = admin_config('upload.disk');
            $path = admin_config('upload.path');
            Storage::adapter($disk);
            $result = Storage::path($path)->upload($file);
            abort_if(!$result, 400, '上传失败');
            $url = $result->file_url;
            $name = $result->file_name;
            $data = [
                'value' => $name,
                'filename' => $name,
                'url' => $url,
                'link' => $url,
            ];
            return jsonData($data);
        } catch (Exception $exception) {
            return amisError($exception->getMessage());
        }

    }
}