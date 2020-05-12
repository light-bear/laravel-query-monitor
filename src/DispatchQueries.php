<?php
/**
 * Created by PhpStorm.
 * User: Madman
 * Date: 2020/5/12
 * Time: 10:28
 */

namespace LightBear\LaravelQueryMonitor;

class DispatchQueries
{
    protected $host;
    protected $port;

    function __construct(string $host, int $port)
    {
        $this->host = $host;
        $this->port = $port;
    }

    public function send($query)
    {
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        socket_connect($socket, $this->host, $this->port);

        $message = json_encode($query);

        socket_write($socket, $message, strlen($message));

        socket_close($socket);
    }
}