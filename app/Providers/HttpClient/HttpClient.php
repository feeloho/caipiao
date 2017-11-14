<?php
namespace App\Providers\HttpClient;

class HttpClient
{
    /**
     * 根据传过来的数组生成Request的参数
     *
     * @param array $data 键值对数组
     * @param array $ignoreCode 不URL编码的键值对，默认空
     * @return string
     */
    public static function encodeRequest($data, $ignoreCode = array())
    {
        foreach ($data as $k => $v) {
            if (!in_array($k, $ignoreCode)) {
                $v = urlencode($v);
            }
            $dataArr[] = "$k=$v";
        }
        $urlParameter = implode('&', $dataArr);
        return $urlParameter;
    }

    /**
     * 发送HTTP请求
     *
     * @param string $url
     * @param string $requestType 请求类型(GET POST PUT DELETE)
     * @param string|array $params 请求数据
     * @param array $headers
     * @return string
     * @throws \Exception
     */
    public static function doRequest($url, $requestType, $params, $headers = [])
    {
        if (is_array($params)) {
            $params = self::encodeRequest($params);
        }
        $opts = array(
            CURLOPT_TIMEOUT => 30,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
        );
        if (!empty($headers)) {
            $opts[CURLOPT_HTTPHEADER] = $headers;
        }
        switch (strtoupper($requestType)) {
            case "GET" :
                $opts[CURLOPT_HTTPGET] = true;
                $opts[CURLOPT_URL] = $url . '?' . $params;
                break;
            case "POST":
                $opts[CURLOPT_POST] = true;
                $opts[CURLOPT_POSTFIELDS] = $params;
                break;
            case "PUT" :
                $opts[CURLOPT_CUSTOMREQUEST] = "PUT";
                $opts[CURLOPT_POSTFIELDS] = $params;
                break;
            case "DELETE":
                $opts[CURLOPT_CUSTOMREQUEST] = "DELETE";
                $opts[CURLOPT_POSTFIELDS] = $params;
                break;
            default:
                throw new \Exception('不支持的请求方式！');
        }
        /* 初始化并执行curl请求 */
        $ch = curl_init();
        curl_setopt_array($ch, $opts);

        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * 发送异步HTTP请求
     *
     * @param string $url
     * @param string $requestType 请求类型(GET POST PUT DELETE)
     * @param string $data 编码后的请求数据
     * @param array $headers
     * @return string
     */
    public static function doAsynRequest($url, $requestType, $data, $headers = [])
    {
        $urlArr = parse_url($url);
        $fp = fsockopen($urlArr['host'], 80, $errno, $errstr, 5);
        if (!$fp) {
            echo "$errstr ($errno)<br />/n";
        }
        $out = "{$requestType} {$urlArr['path']}?{$data} HTTP/1.1\r\n";
        $out .= "Host: {$urlArr['host']}\r\n";
        foreach ($headers as $header) {
            $out .= "$header\r\n";
        }
        $out .= "Connection: Close\r\n\r\n";
        fwrite($fp, $out);
//        while (!feof($fp)) {
//            echo fread($fp, 128);
//        }
        fclose($fp);
    }
}