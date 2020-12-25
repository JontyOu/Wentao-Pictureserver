<?php
/**
 *User:WenTao
 *Date:2020/12/24
 *Time:11:04
 */

namespace Wentao\Pictureserver;

class Upload
{
    protected $url = 'http://pictureserver.wsandos.com/api/upload/index';
    protected $app_id;
    protected $secret;

    public function __construct(string $app_id, string $secret)
    {
        $this->app_id = $app_id;
        $this->secret = $secret;
    }

    //上传文件
    public function put($path, $key)
    {
        //$file="@H:\code\pictureserver\src\st.jpg";//如果是文件 则参数为"@"+绝对路径  ‘@' 符号告诉服务器为上传资源 php<=5.5   这个是php版本小于5.5的用法
        $file      = new \CURLFile($path);//如果是文件 则参数为"@"+绝对路径
        $post_data = array(
            'app_id'    => $this->app_id,
            'timestamp' => time(),
            'path'      => $key,
        );

        $post_data['sign'] = $this->getSign($post_data);
        $post_data['file'] = $file;
        $result            = $this->postData($this->url, $post_data);
        return $result;
    }

    //生成签名
    protected function getSign($data)
    {
        $params = http_build_query($data);
        $sign   = md5($this->secret . $params);
        return $sign;
    }

    //发送请求
    protected function postData($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);   //请求地址
        curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
        curl_setopt($ch, CURLOPT_POST, true);  //post请求
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);//二进制流
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);      //数据
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  //当CURLOPT_RETURNTRANSFER设置为1时 $head 有请求的返回值
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);    //设置请求超时时间
        $handles = curl_exec($ch);
        return $handles;
    }

}