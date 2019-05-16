<?php
/**
 * @author xialeistudio
 */

namespace SwooleThrift\Server;

use Swoole\Server;
use Thrift\Exception\TTransportException;
use Thrift\Server\TServerTransport;
use Thrift\Transport\TTransport;

/**
 * Server传输协议
 * Class SwooleServerTransport
 * @package swoole\server
 */
class SwooleServerTransport extends TServerTransport
{
    /**
     * @var string 监听地址
     */
    public $host = 'localhost';
    /**
     * @var int 监听端口
     */
    public $port = 9501;
    /**
     * @var int 进程模型
     */
    public $mode = SWOOLE_PROCESS;
    /**
     * @var int SOCK类型
     */
    public $sockType = SWOOLE_SOCK_TCP;
    /**
     * @var array 服务器选项
     */
    public $options = [
        'worker_num' => 1,
        'dispatch_mode' => 1, //1: 轮循, 3: 争抢
        'open_length_check' => true, //打开包长检测
        'package_max_length' => 8192000, //最大的请求包长度,8M
        'package_length_type' => 'N', //长度的类型，参见PHP的pack函数
        'package_length_offset' => 0,   //第N个字节是包长度的值
        'package_body_offset' => 4,   //从第几个字节计算长度
    ];

    /**
     * @var Server
     */
    public $server;

    /**
     * SwooleServerTransport constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $config = $this->mergeConfig($config);
        $this->server = new Server($config['host'], $config['port'], $config['mode'], $config['sockType']);
        $this->server->set($config['options']);
    }


    /**
     * List for new clients
     *
     * @return void
     * @throws TTransportException
     */
    public function listen()
    {
        if (!$this->server->start()) {
            throw new TTransportException('SwooleServerTransport start failed.', TTransportException::UNKNOWN);
        }
    }

    /**
     * Close the server
     *
     * @return void
     */
    public function close()
    {
        $this->server->shutdown();
    }

    /**
     * Swoole服务端通过回调函数获取请求，不可以调用accept方法
     * @return TTransport
     */
    protected function acceptImpl()
    {
        return null;
    }

    /**
     * 合并配置
     * @param array $config
     * @return array
     */
    private function mergeConfig(array $config)
    {
        return array_merge([
            'host' => $this->host,
            'port' => $this->port,
            'mode' => $this->mode,
            'sockType' => $this->sockType,
            'options' => $this->options
        ], $config);
    }
}