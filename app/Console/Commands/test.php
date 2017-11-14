<?php
namespace App\Console\Commands;

use App\Providers\Helper\Helper;
use App\Providers\HttpClient\HttpClient;
use Illuminate\Console\Command;

class test extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '刷票';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        while (true) {
            $getRequest = 'http://xuanxiu2.zggzwlw.com/plugins/xuanxiu2/miyou_index.php';
            $getParams['hdid'] = '403';
            $getParams['sid'] = '2293';
            $getParams['weixingname'] = 'ovg1_' . str_random(24);
            $getParams['otid'] = rand(397406,500000);
            $getResult = HttpClient::doRequest($getRequest, 'get', $getParams);
//            dd($getResult);
            $url = 'http://xuanxiu2.zggzwlw.com/plugins/xuanxiu/ajax_vote.php?hdid=403&sid=2293';
            $params['ctype'] = 'Vote';
            $params['needwxh'] = 'no';
            $params['userid'] = '133054';
            $ip = '120.205.' .rand(1, 255).'.' . rand(1,255);
            $params['tezhengma'] = md5( $ip. 'Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1');
            $headers['Content'] = 'multipart/form-data';
            $headers['Referer'] = 'http://xuanxiu2.zggzwlw.com/plugins/xuanxiu2/miyou_index.php?hdid=403&sid=2293&weixingname=ovg_vsuDbc47LKoRm443zRhDfTcc&otid=396090';
            $headers['Cookie'] = 'votetzm=' . $params['tezhengma'] . ';PHPSESSID=bd1d8dgvefa12elmmlamlua1n3';// . Helper::randString(26, 5);
            $result = HttpClient::doRequest($url, 'post', $params);
            echo $result . "|ip=" . $ip  . "\n";
        }
    }
}
