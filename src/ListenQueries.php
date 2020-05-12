<?php
/**
 * Created by PhpStorm.
 * User: Madman
 * Date: 2020/5/12
 * Time: 10:34
 */

namespace LightBear\LaravelQueryMonitor;

use Closure;
use Illuminate\Support\Str;

class ListenQueries
{
    protected $host;
    protected $post;
    protected $info;
    protected $warn;

    function __construct(string $host, int $port)
    {
        $this->host = $host;
        $this->port = $port;
    }

    public function setInfo(Closure $info)
    {
        $this->info = $info;
    }

    public function setWarn(Closure $warn)
    {
        $this->warn = $warn;
    }

    public function run()
    {
        call_user_func($this->info, 'Listen SQL queries on ' . $this->host . ':' . $this->port . PHP_EOL . PHP_EOL);

        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        socket_bind($socket, $this->host, $this->port);

        socket_listen($socket, 4);

        do {
            $msgSocket = socket_accept($socket);

            $data = socket_read($msgSocket, 2048);

            $query = json_decode($data, true);

            if ($query === null) {
                call_user_func($this->warn, '# Something wrong happened with JSON data received: ');
                call_user_func($this->info, $data);
            } else {
                $sql = Str::replaceArray('?', array_map(function ($i) {
                    return is_string($i) ? "'$i'" : $i;
                }, $query['bindings']), $query['sql']);

                call_user_func($this->info, '# SQL: ' . $sql);
                call_user_func($this->info, '# Time: ' . $query['time'] / 1000 . ' seconds');
            }

            call_user_func($this->info, PHP_EOL);

            socket_close($msgSocket);

        } while (true);

        socket_close($socket);
    }
}