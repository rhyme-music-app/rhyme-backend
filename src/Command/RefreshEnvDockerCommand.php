<?php
namespace App\Command;

use App\Utils\RefreshEnvUtils;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:refresh-env-docker', description: 'Creates or refreshes .env values for Dockerized Apache server.')]
class RefreshEnvDockerCommand extends Command
{
    protected function configure(): void
    {
        // In case of Dockerized Apache server:
        // Reflects the information in docker-compose.yml.
        // => no options present to the user, except these ones !
        $this
            ->addOption('changesecret', null, InputOption::VALUE_NONE, 'Specify this option if you want to change APP_SECRET (which will invalidate current login tokens!). If this option is not specified, APP_SECRET will be created if it hasn\'t existed ; otherwise it will be left unchanged.')
            ->addOption('activeenv', null, InputOption::VALUE_REQUIRED, 'Application active environment. Can be either "dev" (development) or "prod" (production).', 'dev')
            ->addOption('tz', null, InputOption::VALUE_REQUIRED, 'PHP timezone. See <https://www.php.net/manual/en/timezones.php>', 'Asia/Ho_Chi_Minh');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $newValues = [
            'changesecret' => $input->getOption('changesecret'),
            'activeenv' => $input->getOption('activeenv'),
            'tz' => $input->getOption('tz'),
            // The following values are hardcoded as in docker-compose.yml.
            'dbname' => 'rhyme',
            'dbuser' => 'root',
            'dbpass' => '',
            // Docker Default Network: Use host's DNS
            // => Database hostname accessed from host: host.docker.internal
            // => Database port accessed from host: 9906 (as in docker-compose.yml)
            'dbhost' => 'host.docker.internal',
            'dbport' => '9906',
        ];

        return RefreshEnvUtils::execute($newValues, $output);
    }
}
