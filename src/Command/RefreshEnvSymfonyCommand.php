<?php
namespace App\Command;

use App\Utils\RefreshEnvUtils;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'app:refresh-env-symfony', description: 'Creates or refreshes .env values for Symfony local development server.')]
class RefreshEnvSymfonyCommand extends Command
{
    protected function configure(): void
    {
        // In case of Symfony's local development server:
        // Let the user decide what is what.
        $this
            ->addOption('activeenv', null, InputOption::VALUE_REQUIRED, 'Application active environment. Can be either "dev" (development) or "prod" (production).', 'dev')
            ->addOption('dbname', null, InputOption::VALUE_REQUIRED, 'MySQL database name.', 'rhyme')
            ->addOption('dbuser', null, InputOption::VALUE_REQUIRED, 'MySQL database username.', 'root')
            ->addOption('dbpass', null, InputOption::VALUE_REQUIRED, 'MySQL database password.', '')
            ->addOption('dbhost', null, InputOption::VALUE_REQUIRED, 'MySQL database hostname.', 'localhost')
            ->addOption('dbport', null, InputOption::VALUE_REQUIRED, 'MySQL database port.', '3306')
            ->addOption('tz', null, InputOption::VALUE_REQUIRED, 'PHP timezone. See <https://www.php.net/manual/en/timezones.php>', 'Asia/Ho_Chi_Minh');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $newValues = [];
        foreach (['activeenv', 'dbname', 'dbuser', 'dbpass', 'dbhost', 'dbport', 'tz'] as $option) {
            $newValues[$option] = $input->getOption($option);
        }

        return RefreshEnvUtils::execute($newValues, $output);
    }
}
