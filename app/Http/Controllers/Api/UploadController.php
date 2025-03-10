<?php


namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

/**
 * 文件上传-控制器
 * @author zhouxufeng <zxf@netsun.com>
 * @date: 2022/10/30 13:12
 */
class UploadController extends Controller
{
    /**
     * 上传图片
     *
     * @param Request $request
     * @return array
     * @author zhouxufeng <zxf@netsun.com>
     * @date 2025/1/22 09:48
     */
    public function uploadImage(Request $request)
    {
        // 上传单图统一调取方法
        $result = upload_image($request, 'file');
        if (!$result['success']) {
            return message($result['msg'], false);
        }

        // 文件路径
        $file_path = $result['data']['img_path'];
        if (!$file_path) {
            return message("文件上传失败", false);
        }

        // 网络域名拼接
        if (strpos($file_path, IMG_URL) === false) {
            $file_path = IMG_URL . $file_path;
        }

        // 返回结果
        return message(MESSAGE_OK, true, $file_path);
    }

    /**
     * 上传文件
     * @param Request $request
     * @return array|void
     * @author zhouxufeng <zxf@netsun.com>
     * @date: 2022/10/30 13:12
     */
    public function uploadFile(Request $request)
    {
        $result = upload_file($request);
        if (!$result['success']) {
            return message($result['msg'], false);
        }
        // 文件路径
        $file_path = $result['data']['file_path'];
        if (!$file_path) {
            return message("文件上传失败", false);
        }
        // 网络域名拼接
        if (strpos($file_path, IMG_URL) === false) {
            $file_path = IMG_URL . $file_path;
        }
        // 返回结果
        return message(MESSAGE_OK, true, $file_path);
    }
}
