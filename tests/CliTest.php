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
        self::assertStringContainsString('make:page', $output->buffer());
        self::assertStringContainsString('make:service', $output->buffer());
        self::assertStringContainsString('make:migration', $output->buffer());
        self::assertStringContainsString('db:seed', $output->buffer());
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
        self::assertFileExists($path . '/features/UserProfile/UserProfilePage.php');
        self::assertFileExists($path . '/features/UserProfile/UserProfileController.php');
        self::assertFileExists($path . '/features/UserProfile/UserProfileService.php');
        self::assertFileExists($path . '/features/UserProfile/UserProfileModel.php');
        self::assertFileExists($path . '/features/UserProfile/Tests/UserProfileControllerTest.php');
        self::assertStringContainsString('namespace App\\UserProfile;', (string) file_get_contents($path . '/features/UserProfile/UserProfilePage.php'));
        self::assertStringContainsString('use Velt\\Ui\\Page;', (string) file_get_contents($path . '/features/UserProfile/UserProfilePage.php'));
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

    public function testMakePageGeneratesSkeletonCompatiblePage(): void
    {
        $path = $this->temporaryDirectory();
        $output = new Output();
        ob_start();
        $exitCode = ApplicationFactory::create()->run(['velt', 'make:page', 'dashboard', '--path=' . $path], $output);
        ob_end_clean();

        self::assertSame(0, $exitCode);
        self::assertFileExists($path . '/features/Dashboard/DashboardPage.php');
        self::assertStringContainsString('namespace App\\Dashboard;', (string) file_get_contents($path . '/features/Dashboard/DashboardPage.php'));
        self::assertStringContainsString('final class DashboardPage', (string) file_get_contents($path . '/features/Dashboard/DashboardPage.php'));
    }

    public function testMakeServiceDoesNotOverwriteWithoutForce(): void
    {
        $path = $this->temporaryDirectory();
        $app = ApplicationFactory::create();
        ob_start();
        $app->run(['velt', 'make:service', 'billing', '--path=' . $path], new Output());
        $output = new Output();
        $exitCode = $app->run(['velt', 'make:service', 'billing', '--path=' . $path], $output);
        ob_end_clean();

        self::assertSame(1, $exitCode);
        self::assertStringContainsString('already exists', $output->buffer());
        self::assertFileExists($path . '/features/Billing/BillingService.php');
    }

    public function testMakeMigrationAndSeederGenerateExpectedFiles(): void
    {
        $path = $this->temporaryDirectory();
        $output = new Output();
        ob_start();
        $migrationExitCode = ApplicationFactory::create()->run(['velt', 'make:migration', 'create_users_table', '--path=' . $path], $output);
        $seederExitCode = ApplicationFactory::create()->run(['velt', 'make:seeder', 'UserSeeder', '--path=' . $path], $output);
        ob_end_clean();

        $migrations = glob($path . '/database/migrations/*_create_users_table.php') ?: [];

        self::assertSame(0, $migrationExitCode);
        self::assertSame(0, $seederExitCode);
        self::assertCount(1, $migrations);
        self::assertStringContainsString('return new class', (string) file_get_contents($migrations[0]));
        self::assertFileExists($path . '/database/seeders/UserSeeder.php');
        self::assertStringContainsString('extends Seeder', (string) file_get_contents($path . '/database/seeders/UserSeeder.php'));
    }

    public function testMakeMigrationDoesNotDuplicateNameWithoutForce(): void
    {
        $path = $this->temporaryDirectory();
        $app = ApplicationFactory::create();
        ob_start();
        $app->run(['velt', 'make:migration', 'create_users_table', '--path=' . $path], new Output());
        $output = new Output();
        $exitCode = $app->run(['velt', 'make:migration', 'create_users_table', '--path=' . $path], $output);
        ob_end_clean();

        self::assertSame(1, $exitCode);
        self::assertStringContainsString('already exists', $output->buffer());
    }

    public function testMigrateRollbackAndSeedUseDatabasePackage(): void
    {
        $path = $this->databaseProject();
        $output = new Output();
        ob_start();
        $migrateExitCode = ApplicationFactory::create()->run(['velt', 'migrate', '--path=' . $path], $output);
        $seedExitCode = ApplicationFactory::create()->run(['velt', 'db:seed', '--class=UserSeeder', '--path=' . $path], $output);
        $rollbackExitCode = ApplicationFactory::create()->run(['velt', 'migrate:rollback', '--path=' . $path], $output);
        ob_end_clean();

        self::assertSame(0, $migrateExitCode);
        self::assertSame(0, $seedExitCode);
        self::assertSame(0, $rollbackExitCode);
        self::assertStringContainsString('Migrated:', $output->buffer());
        self::assertStringContainsString('Seeded: UserSeeder', $output->buffer());
        self::assertStringContainsString('Rolled back:', $output->buffer());
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

    private function databaseProject(): string
    {
        $path = $this->temporaryDirectory();
        mkdir($path . '/bootstrap', 0777, true);
        mkdir($path . '/config', 0777, true);
        mkdir($path . '/database/migrations', 0777, true);
        mkdir($path . '/database/seeders', 0777, true);

        $databasePath = str_replace('\\', '/', $path . '/database.sqlite');
        file_put_contents($path . '/config/database.php', <<<PHP
<?php

declare(strict_types=1);

return [
    'default' => 'sqlite',
    'connections' => [
        'sqlite' => [
            'driver' => 'sqlite',
            'database' => '{$databasePath}',
        ],
    ],
    'migrations' => [
        'path' => 'database/migrations',
    ],
];
PHP);

        file_put_contents($path . '/bootstrap/app.php', <<<'PHP'
<?php

declare(strict_types=1);

use Velt\Database\DatabaseServiceProvider;
use Velt\Kernel\Application;

$app = new Application(dirname(__DIR__));
$app->registerProvider(DatabaseServiceProvider::class);
$app->boot();

return ['app' => $app];
PHP);

        file_put_contents($path . '/database/migrations/2026_01_01_000000_create_users_table.php', <<<'PHP'
<?php

declare(strict_types=1);

use Velt\Database\Schema\Blueprint;
use Velt\Database\Schema\Schema;

return new class {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email');
        });
    }

    public function down(): void
    {
        Schema::drop('users');
    }
};
PHP);

        file_put_contents($path . '/database/seeders/UserSeeder.php', <<<'PHP'
<?php

declare(strict_types=1);

use Velt\Database\DB;
use Velt\Database\Seeders\Seeder;

final class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Ada',
            'email' => 'ada@example.com',
        ]);
    }
}
PHP);

        return $path;
    }
}
