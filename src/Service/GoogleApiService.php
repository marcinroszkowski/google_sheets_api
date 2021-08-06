<?php

namespace App\Service;

use App\Traits\HelperTrait;
use Google\Client;
use Google\Service\Sheets;
use Psr\Log\LoggerInterface;

/**
 * Class GoogleApiService
 * @package App\Service
 */
class GoogleApiService
{
    use HelperTrait;

    private const CLEAR_SHEET_RANGE = 'congress!A1:Z9999';
    private const SHEET_STARTING_COLUMN = 'A';
    private const SHEET_STARTING_ROW = 1;

    public $client;
    public $spreadsheetId;
    public $service;
    public $readerService;
    protected $logger;

    /**
     * GoogleApiService constructor.
     * @param ReaderService $readerService
     * @throws \Google\Exception
     */
    public function __construct(ReaderService $readerService, LoggerInterface $logger)
    {
        $this->client = $this->getClient();
        $this->service = $this->getService($this->client);
        $this->readerService = $readerService;
        $this->logger = $logger;
    }

    /**
     * Returns an authorized API client.
     * @return Client
     * @throws \Google\Exception
     */
    public function getClient(): Client
    {
        $client = new Client();
        $client->setApplicationName('Some name');
        $client->setScopes(Sheets::SPREADSHEETS);
        $client->setAccessType('offline');
        $client->setAuthConfig(getcwd() . '/public/data/oauth-190219-a56ef8debe12.json');

        return $client;
    }

    /**
     * @param Client $client
     * @return Sheets
     */
    public function getService(Client $client): Sheets
    {
        return new Sheets($client);
    }

    /**
     * @param string $spreadsheetId
     * @return $this
     */
    public function setSpreadsheetId(string $spreadsheetId): GoogleApiService
    {
        $this->spreadsheetId = $spreadsheetId;

        return $this;
    }

    /**
     * @return bool
     */
    public function clearSheet(): bool
    {
        try {
            $this->service->spreadsheets_values->clear($this->spreadsheetId, self::CLEAR_SHEET_RANGE, (new Sheets\ClearValuesRequest()));
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());

            return false;
        }
        return true;
    }

    /**
     * @return bool
     */
    public function updateSheet(): bool
    {
        $data = $this->readerService->read();
        $params = ['valueInputOption' => 'RAW'];

        try {
            $column = self::SHEET_STARTING_COLUMN;
            foreach ($data as $singleRow) {
                $singleRowMapped = $this->wrapArrayElementWithArray($singleRow);
                $body = new Sheets\ValueRange(['values' => $singleRowMapped]);
                $range = $this->getRange($column, $singleRowMapped);

                $this->service->spreadsheets_values->update($this->spreadsheetId, $range, $body, $params);
                $column++;
            }
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());

            return false;
        }

        return true;
    }

    /**
     * @return string|null
     */
    public function getSpreadSheetId(): ?string
    {
        try {
            $spreadSheetData = $this->service->spreadsheets->get($this->spreadsheetId);

            if (isset($spreadSheetData['sheets']) && isset($spreadSheetData['sheets'][0])) {
                return $spreadSheetData['sheets'][0]->properties->sheetId;
            }

            return null;
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());

            return null;
        }
    }
}