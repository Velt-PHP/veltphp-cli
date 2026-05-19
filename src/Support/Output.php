<?php

declare(strict_types=1);

namespace Velt\Cli\Support;

final class Output
{
    private string $buffer = '';

    public function line(string $message = ''): void
    {
        $this->buffer .= $message . PHP_EOL;
        fwrite(STDOUT, $message . PHP_EOL);
    }

    public function error(string $message): void
    {
        $this->line('[ERROR] ' . $message);
    }

    public function success(string $message): void
    {
        $this->line('[OK] ' . $message);
    }

    public function buffer(): string
    {
        return $this->buffer;
    }
}
