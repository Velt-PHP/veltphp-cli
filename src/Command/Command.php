<?php

declare(strict_types=1);

namespace Velt\Cli\Command;

use Velt\Cli\Support\Input;
use Velt\Cli\Support\Output;

interface Command
{
    public function name(): string;

    public function description(): string;

    public function help(): string;

    public function run(Input $input, Output $output): int;
}
