<?php

namespace App\Service;

use App\Traits\HelperTrait;
use Google\Client;
use Google\Service\Sheets;

/**
 * Class GoogleApiService
 * @package App\Service
 */
class GoogleApiService
{
    use HelperTrait;

    private const SPREADSHEET_ID = '1z446lp2SwTYE6UdIppACr0OfeF1S__YIuOyvwF4304Y';
    private const CLEAR_SHEET_RANGE = 'congress!A1:Z9999';
    private const SHEET_STARTING_COLUMN = 'A';
    private const SHEET_STARTING_ROW = 1;

    public $client;
    public $service;
    public $readerService;

    /**
     * GoogleApiService constructor.
     * @param ReaderService $readerService
     * @throws \Google\Exception
     */
    public function __construct(ReaderService $readerService)
    {
        $this->client = $this->getClient();
        $this->service = $this->getService($this->client);
        $this->readerService = $readerService;
    }

    /**
     * Returns an authorized API client.
     *
     * @return Client the authorized client object
     * @throws \Google\Exception
     */
    public function getClient()
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
     * @return bool
     */
    public function clearSheet()
    {
        try {
            $this->service->spreadsheets_values->clear(self::SPREADSHEET_ID, self::CLEAR_SHEET_RANGE, (new Sheets\ClearValuesRequest()));
        } catch (\Exception $exception) {
            return false;
        }
        return true;
    }

    /**
     * @return bool
     */
    public function updateSheet()
    {
        $data = $this->readerService->read();
        $params = ['valueInputOption' => 'RAW'];

        try {
            $column = self::SHEET_STARTING_COLUMN;
            foreach ($data as $singleRow) {
                $singleRowMapped = $this->wrapArrayElementWithArray($singleRow);
                $body = new Sheets\ValueRange(['values' => $singleRowMapped]);
                $range = $this->getRange($column, $singleRowMapped);

                $this->service->spreadsheets_values->update(self::SPREADSHEET_ID, $range, $body, $params);
                $column++;
            }
        } catch (\Exception $exception) {
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
            $spreadSheetData = $this->service->spreadsheets->get(self::SPREADSHEET_ID);

            if (isset($spreadSheetData['sheets']) && isset($spreadSheetData['sheets'][0])) {
                return $spreadSheetData['sheets'][0]->properties->sheetId;
            }

            return null;
        } catch (\Exception $exception) {
            return null;
        }
    }
}