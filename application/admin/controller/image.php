<?php


namespace app\admin\controller;


class image
{
    /**
     * @return \think\response\Json
     * @throws \Exception
     */
    public function index()
    {
        $res = $this->uploadFile($_FILES);
        if ($res) {
            return json(['code' => 0, 'img' => "/uploads/{$res}", 'msg' => 'success']);
        } else {
            return json(['code' => 1, 'msg' => '上传失败!']);
        }
    }

    /**
     * 上传文件
     * @throws \Exception
     */
    private function uploadFile($files)
    {
        try {
            $uploads_dir = '../public/static/uploads';
            $name = '';
            foreach ($files as $key => $file) {
                if ($file['error'] == UPLOAD_ERR_OK) {
                    $tmp_name = $file["tmp_name"];
                    $name = md5(sprintf('%.4f', microtime(true)));
                    $name = $name . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
                    $img_url = "{$uploads_dir}/{$name}";
                    move_uploaded_file($tmp_name, $img_url);
                } else {
                    throw new \Exception('上传文件失败!');
                }
            }
        } catch (\Exception $e) {
            $err = "{$e->getMessage()}, {$e->getFile()}, {$e->getLine()}";
            throw new \Exception($err);
        }

        return $name;
    }
}