<?php

namespace App\Command;

use App\Service\GoogleApiService;
use App\Service\ReaderService;
use Symfony\Component\Console\Command\Command;
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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $clearSheetStatus = $this->googleApiService->clearSheet();
        $updateSheetStatus = $this->googleApiService->updateSheet();
        $spreadSheetId = $this->googleApiService->getSpreadSheetId();

        if (!$clearSheetStatus || !$updateSheetStatus || is_null($spreadSheetId)) {
            $output->write('An error occured!');

            return Command::FAILURE;
        }

        $output->write("SheetId: {$spreadSheetId}");

        return Command::SUCCESS;
    }
}