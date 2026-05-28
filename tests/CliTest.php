<?php

declare(strict_types=1);

namespace Velt\Cli\Tests;

use PHPUnit\Framework\TestCase;
use Velt\Cli\ApplicationFactory;
use Velt\Cli\Support\Output;

final class CliTest extends TestCase
{
    public function testListDisplaysAvailableCommands(): void
    {
        $output = new Output();
        ob_start();
        $exitCode = ApplicationFactory::create()->run(['velt', 'list'], $output);
        ob_end_clean();

        self::assertSame(0, $exitCode);
        self::assertStringContainsString('Velt CLI', $output->buffer());
        self::assertStringContainsString('kernel:check', $output->buffer());
        self::assertStringContainsString('make:feature', $output->buffer());
        self::assertStringContainsString('serve', $output->buffer());
    }

    public function testHelpDisplaysCommandHelp(): void
    {
        $output = new Output();
        ob_start();
        $exitCode = ApplicationFactory::create()->run(['velt', 'help', 'make:feature'], $output);
        ob_end_clean();

        self::assertSame(0, $exitCode);
        self::assertStringContainsString('Usage: php bin/velt make:feature <name>', $output->buffer());
    }

    public function testUnknownCommandReturnsError(): void
    {
        $output = new Output();
        ob_start();
        $exitCode = ApplicationFactory::create()->run(['velt', 'missing'], $output);
        ob_end_clean();

        self::assertSame(1, $exitCode);
        self::assertStringContainsString('Unknown command "missing"', $output->buffer());
    }

    public function testKernelCheckValidatesKernelContracts(): void
    {
        $path = $this->temporaryDirectory();
        $output = new Output();
        ob_start();
        $exitCode = ApplicationFactory::create()->run(['velt', 'kernel:check', '--path=' . $path], $output);
        ob_end_clean();

        self::assertSame(0, $exitCode);
        self::assertStringContainsString('Kernel contracts responded successfully.', $output->buffer());
        self::assertStringContainsString('Base path: ' . $path, $output->buffer());
        self::assertStringContainsString('Environment: production', $output->buffer());
    }

    public function testMakeFeatureGeneratesExpectedFiles(): void
    {
        $path = $this->temporaryDirectory();
        $output = new Output();
        ob_start();
        $exitCode = ApplicationFactory::create()->run(['velt', 'make:feature', 'user-profile', '--path=' . $path, '--no-interaction'], $output);
        ob_end_clean();

        self::assertSame(0, $exitCode);
        self::assertFileExists($path . '/features/user-profile/UserProfileController.php');
        self::assertFileExists($path . '/features/user-profile/UserProfileService.php');
        self::assertFileExists($path . '/features/user-profile/UserProfileModel.php');
        self::assertFileExists($path . '/features/user-profile/views/login.velt.php');
        self::assertFileExists($path . '/features/user-profile/tests/UserProfileControllerTest.php');
        self::assertStringContainsString("return Page::make('Connexion')", (string) file_get_contents($path . '/features/user-profile/views/login.velt.php'));
    }

    public function testMakeFeatureDoesNotOverwriteWithoutForce(): void
    {
        $path = $this->temporaryDirectory();
        $app = ApplicationFactory::create();
        ob_start();
        $app->run(['velt', 'make:feature', 'auth', '--path=' . $path], new Output());
        $output = new Output();
        $exitCode = $app->run(['velt', 'make:feature', 'auth', '--path=' . $path], $output);
        ob_end_clean();

        self::assertSame(1, $exitCode);
        self::assertStringContainsString('already exists', $output->buffer());
    }

    public function testMakeTestGeneratesPhpUnitTest(): void
    {
        $path = $this->temporaryDirectory();
        $output = new Output();
        ob_start();
        $exitCode = ApplicationFactory::create()->run(['velt', 'make:test', 'UserControllerTest', '--path=' . $path], $output);
        ob_end_clean();

        self::assertSame(0, $exitCode);
        self::assertFileExists($path . '/tests/Feature/UserControllerTest.php');
        self::assertStringContainsString('extends TestCase', (string) file_get_contents($path . '/tests/Feature/UserControllerTest.php'));
    }

    public function testServeDryRunBuildsPhpServerCommand(): void
    {
        $path = $this->temporaryDirectory();
        mkdir($path . '/public', 0777, true);
        $output = new Output();
        ob_start();
        $exitCode = ApplicationFactory::create()->run(['velt', 'serve', '--path=' . $path, '--host=127.0.0.1', '--port=8000', '--dry-run'], $output);
        ob_end_clean();

        self::assertSame(0, $exitCode);
        self::assertStringContainsString('http://127.0.0.1:8000', $output->buffer());
        self::assertStringContainsString('-S', $output->buffer());
        self::assertStringContainsString('-t', $output->buffer());
    }

    public function testServeFailsWhenPublicDirectoryIsMissing(): void
    {
        $path = $this->temporaryDirectory();
        $output = new Output();
        ob_start();
        $exitCode = ApplicationFactory::create()->run(['velt', 'serve', '--path=' . $path, '--dry-run'], $output);
        ob_end_clean();

        self::assertSame(1, $exitCode);
        self::assertStringContainsString('public directory does not exist', $output->buffer());
    }

    private function temporaryDirectory(): string
    {
        $path = sys_get_temp_dir() . '/velt-cli-' . bin2hex(random_bytes(6));
        mkdir($path, 0777, true);

        return str_replace('\\', '/', $path);
    }
}
