<?php

namespace App\Command;

use App\Service\GoogleApiService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreateUserCommand
 * @package App\Command
 */
class SynchronizeGoogleSheet extends Command
{
    protected static $defaultName = 'app:synchronize-google-sheet';

    protected $googleApiService;

    /**
     * SynchronizeGoogleSheet constructor.
     * @param GoogleApiService $googleApiService
     * @param string|null $name
     */
    public function __construct(GoogleApiService $googleApiService, string $name = null)
    {
        parent::__construct($name);
        $this->googleApiService = $googleApiService;
    }

    protected function configure()
    {
        $this->addArgument('spreadsheetId', InputArgument::REQUIRED, 'The Spreadsheet ID');

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $spreadsheetId = $input->getArgument('spreadsheetId');
        if (is_null($spreadsheetId)) {
            $output->writeln('Please provide valid Spreadsheet ID!');
            exit();
        }

        $this->googleApiService->setSpreadsheetId($spreadsheetId);
        parent::initialize($input, $output);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $clearSheetStatus = $this->googleApiService->clearSheet();
        $updateSheetStatus = $this->googleApiService->updateSheet();
        $spreadSheetId = $this->googleApiService->getSpreadSheetId();

        if (!$clearSheetStatus || !$updateSheetStatus || is_null($spreadSheetId)) {
            return Command::FAILURE;
        }

        $output->write("SheetId: {$spreadSheetId}");

        return Command::SUCCESS;
    }
}