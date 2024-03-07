<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use OSS\Core\OssException;
use OSS\OssClient;

class AliOssService
{
    /**
     * 上传文件到OSS
     * @param string | UploadedFile $content
     * @return string
     * @throws OssException
     */
    public function putObject($content)
    {
        $ossConfig = config('oss');
        $ossClient = new OssClient($ossConfig['key'], $ossConfig['secret'], $ossConfig['endpoint'], true);
        if (is_string($content)) {
            $key = $ossConfig['folder'] . '/images/' . date('Ym/') . md5(uniqid() . '-' . time()) . '.png';
            $res = $ossClient->putObject($ossConfig['bucket'], $key, base64_decode($content));
        } else {
            $key = $ossConfig['folder'] . '/files/' . date('Ym/') . md5(uniqid() . '-' . time())
                . '.' . $content->getClientOriginalExtension();
            $res = $ossClient->uploadFile($ossConfig['bucket'], $key, $content->getPathname());
        }
        return $res['oss-request-url'] ?? '';
    }
}
