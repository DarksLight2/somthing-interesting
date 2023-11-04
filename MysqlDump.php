<?php
declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

final class MysqlDump extends Command
{
    protected $signature = 'db:dumper {--load= : Absolute path to sql file} {--dump= : Absolute path where be created dump}';
    protected $description = 'Make dump or load database dump to database.';
    public function handle(): int
    {
        $user = config('database.connections.mysql.username');
        $pass = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        $database = config('database.connections.mysql.database');

        if($this->option('load') && $this->option('dump')) {
            $this->error('You must set option --load OR --dump!');
            return CommandAlias::INVALID;
        }

        if($path = $this->option('load')) {
            $this->info('Started import database dump...');
            return $this->load($user, $pass, $host, $database, $path);
        }

        if($path = $this->option('dump')) {
            $this->info('Started export database dump...');
            return $this->dump($user, $pass, $host, $database, $path);
        }

        $this->error('Something went wong!');
        return CommandAlias::FAILURE;
    }

    private function load(
        string $user,
        string $pass,
        string $host,
        string $database,
        string $dump_path,
    ): int
    {
        return $this->exec(
            command: 'mysql',
            operation: '< ',
            user: $user,
            pass: $pass,
            host: $host,
            database: $database,
            dump_path: $dump_path,
        );
    }

    private function parseDir(string $path): string
    {
        $explode = str($path)->explode('/');
        $explode->pop();
        return $explode->implode('/');
    }

    private function generateDumpName(string $path, string $database): string
    {
        return str($path . "/" . now() . "_$database.sql")->replace(' ', '_')->toString();
    }

    private function dump(
        string $user,
        string $pass,
        string $host,
        string $database,
        string $dump_path,
    ): int
    {
        return $this->exec(
            command: 'mysqldump',
            operation: '--result_file=',
            user: $user,
            pass: $pass,
            host: $host,
            database: $database,
            dump_path: $this->generateDumpName($dump_path, $database),
        );
    }

    private function exec(
        string $command,
        string $operation,
        string $user,
        string $pass,
        string $host,
        string $database,
        string $dump_path,
    ): int
    {
        if(!empty(exec("$command --version"))) {
            if(!is_dir($dir = $this->parseDir($dump_path))) mkdir($dir, 0755);
            exec("$command --user=$user --password=$pass --host=$host $database $operation$dump_path 2>&1");
            if(file_exists($dump_path)) {
                $this->info('Successful');
                return CommandAlias::SUCCESS;
            }
            $this->error('Something went wrong!');
        } else {
            $this->error("$command is not installed!");
        }
        return CommandAlias::FAILURE;
    }
}
