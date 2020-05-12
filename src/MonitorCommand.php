<?php
/**
 * Created by PhpStorm.
 * User: Madman
 * Date: 2020/5/12
 * Time: 10:36
 */

namespace LightBear\LaravelQueryMonitor;


use Illuminate\Console\Command;

class MonitorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'query:monitor {--host=} {--port=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show in real-time SQL Queries';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $host = $this->option('host') ?? '0.0.0.0';
        $port = $this->option('port') ?? '9001';

        $listenQueries = new ListenQueries($host, $port);

        $listenQueries->setInfo(function ($message) {
            $this->info($message);
        });

        $listenQueries->setWarn(function ($message) {
            $this->warn($message);
        });

        $listenQueries->run();
    }
}