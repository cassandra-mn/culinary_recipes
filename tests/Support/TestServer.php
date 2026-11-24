<?php

namespace Tests\Support;

use RuntimeException;

final class TestServer
{
    private static mixed $process = null;
    private static ?string $url = null;

    public static function url(): string
    {
        self::boot();

        return self::$url;
    }

    public static function boot(): void
    {
        if (self::$process !== null) {
            return;
        }

        self::prepareDatabase();

        $port = self::freePort();
        $root = self::root();

        self::$process = proc_open(
            [
                PHP_BINARY,
                '-d', 'variables_order=EGPCS',
                '-d', 'xdebug.mode=off',
                '-S', "127.0.0.1:{$port}",
                '-t', $root . '/public',
                $root . '/public/index.php',
            ],
            [
                0 => ['pipe', 'r'],
                1 => ['file', self::logFile(), 'a'],
                2 => ['file', self::logFile(), 'a'],
            ],
            $pipes,
            $root,
            self::environment()
        );

        if (!is_resource(self::$process)) {
            self::$process = null;
            throw new RuntimeException('Não foi possível iniciar o servidor de testes.');
        }

        register_shutdown_function([self::class, 'shutdown']);

        self::waitUntilReady($port);

        self::$url = "http://127.0.0.1:{$port}";
    }

    public static function shutdown(): void
    {
        if (self::$process !== null) {
            proc_terminate(self::$process);
            proc_close(self::$process);

            self::$process = null;
            self::$url = null;
        }
    }

    private static function prepareDatabase(): void
    {
        $root = self::root();

        $process = proc_open(
            [PHP_BINARY, '-d', 'variables_order=EGPCS', '-d', 'xdebug.mode=off', $root . '/src/Database/Seed.php'],
            [1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
            $root,
            self::environment()
        );

        $output = stream_get_contents($pipes[1]) . stream_get_contents($pipes[2]);
        $exitCode = proc_close($process);

        if ($exitCode !== 0) {
            throw new RuntimeException("Falha ao preparar o banco de teste (seed): {$output}");
        }
    }

    private static function waitUntilReady(int $port): void
    {
        $deadline = microtime(true) + 10;

        while (microtime(true) < $deadline) {
            $socket = @fsockopen('127.0.0.1', $port, $errorCode, $errorMessage, 0.2);

            if ($socket !== false) {
                fclose($socket);

                return;
            }

            usleep(50_000);
        }

        self::shutdown();

        throw new RuntimeException("O servidor de testes não respondeu na porta {$port}. Veja " . self::logFile());
    }

    private static function freePort(): int
    {
        $socket = stream_socket_server('tcp://127.0.0.1:0', $errorCode, $errorMessage);

        if ($socket === false) {
            throw new RuntimeException("Não foi possível reservar uma porta: {$errorMessage}");
        }

        $port = (int) parse_url('tcp://' . stream_socket_get_name($socket, false), PHP_URL_PORT);
        fclose($socket);

        return $port;
    }

    private static function environment(): array
    {
        return array_merge(getenv(), TestEnvironment::variables());
    }

    private static function root(): string
    {
        return dirname(__DIR__, 2);
    }

    private static function logFile(): string
    {
        $directory = self::root() . '/.phpunit.cache';

        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        return $directory . '/test-server.log';
    }
}
