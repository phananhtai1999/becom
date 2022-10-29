<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Contact;
use App\Models\QueryBuilders\ContactQueryBuilder;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ContactService extends AbstractService
{
    protected $modelClass = Contact::class;

    protected $modelQueryBuilderClass = ContactQueryBuilder::class;

    /**
     * @param $model
     * @return array|void
     */
    public function findContactListKeyByContact($model)
    {
        $contactLists = $model->contactLists()->get();

        if (empty($contactLists)) {

            return [];
        } else {
            foreach ($contactLists as $contactList) {
                $contactListUuid[] = $contactList->uuid;

                return $contactListUuid;
            }
        }
    }

    /**
     * @param $campaignUuid
     * @return mixed
     */
    public function getContactsSendEmail($campaignUuid)
    {
        $contactsCampaign = $this->model->select('contacts.*')
            ->join('contact_contact_list', 'contact_contact_list.contact_uuid', '=', 'contacts.uuid')
            ->join('contact_lists', 'contact_lists.uuid', '=', 'contact_contact_list.contact_list_uuid')
            ->join('campaign_contact_list', 'campaign_contact_list.contact_list_uuid', '=', 'contact_lists.uuid')
            ->join('campaigns', 'campaigns.uuid', '=', 'campaign_contact_list.campaign_uuid')
            ->where('campaigns.uuid', $campaignUuid)->get();

        $resultContacts = [];
        $checkEmailExist = true;
        foreach ($contactsCampaign as $contactCampaign){
            if(empty($resultContacts)){
                $resultContacts[] = $contactCampaign;
            }else{
                foreach ($resultContacts as $value){
                    if($contactCampaign->email == $value->email){
                        $checkEmailExist = true;
                        break;
                    }else{
                        $checkEmailExist = false;
                    }
                }

                if(!$checkEmailExist){
                    $resultContacts[] = $contactCampaign;
                }
            }
        }

        return $resultContacts;
    }

    /**
     * @param $file
     * @return array|bool
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function importExcelOrCsvFile($file)
    {
        $extension = $file->getClientOriginalExtension();
        if ($extension == 'xlsx') {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        } else {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
        }
        $reader->setReadDataOnly(true);
        $reader->setReadEmptyCells(false);
        $spreadsheet = $reader->load($file);
        $getActiveSheet = $spreadsheet->getActiveSheet()->toArray();
        $getKey = $getData = [];
        if (count($getActiveSheet) >= 2) {
            $fields = array_shift($getActiveSheet);
            $rules = [
                'email' => ['required', 'string', 'email:rfc,dns'],
                'first_name' => ['required', 'string'],
                'last_name' => ['required', 'string'],
                'middle_name' => ['nullable', 'string'],
                'phone' => ['nullable', 'numeric'],
                'dob' => ['nullable', 'date_format:Y-m-d'],
                'sex' => ['nullable', 'string'],
                'city' => ['nullable', 'string'],
                'country' => ['nullable', 'string'],
            ];

            foreach ($getActiveSheet as $key => $value) {
                $row = array_combine($fields, $value);
                if (is_integer($row['dob'])) {
                    $data = [
                        'email' => $row['email'],
                        'first_name' => $row['first_name'],
                        'last_name' => $row['last_name'],
                        'middle_name' => $row['middle_name'],
                        'phone' => $row['phone'],
                        'sex' => $row['sex'],
                        'dob' => date_format(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['dob']), 'Y-m-d'),
                        'city' => $row['city'],
                        'country' => $row['country'],
                        'user_uuid' => auth()->user()->getkey()
                    ];
                } else {
                    $data = [
                        'email' => $row['email'],
                        'first_name' => $row['first_name'],
                        'last_name' => $row['last_name'],
                        'middle_name' => $row['middle_name'],
                        'phone' => $row['phone'],
                        'sex' => $row['sex'],
                        'dob' => $row['dob'],
                        'city' => $row['city'],
                        'country' => $row['country'],
                        'user_uuid' => auth()->user()->getkey()
                    ];
                }
                $validator = Validator::make($data, $rules);
                if ($validator->fails()) {
                    $error[] = $validator->errors()->merge(['Row fail' => __('messages.error_data') . ' ' . ($key + 2)]);
                    $jsonDataFail[] = $data;
                    continue;
                }
                $createData = $this->model->create($data);
                $getKey[] = $createData->uuid;
                $getData[] = $createData;
            }

            if (!empty($error)) {
                if (!File::exists(public_path('data_file_error'))) {
                    File::makeDirectory(public_path('data_file_error'));
                }

                if ($extension == 'xlsx') {
                    $fileName = 'import_failed_record_' . uniqid() . '_' . Carbon::today()->toDateString() . '.xlsx';
                } else {
                    $fileName = 'import_failed_record_' . uniqid() . '_' . Carbon::today()->toDateString() . '.csv';
                }
                $fileStorePath = public_path('/data_file_error/' . $fileName);

                //Write into Excel file
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $columnCoordinate = 1;
                $columnHeader = ['email', 'first_name', 'last_name', 'middle_name', 'phone', 'sex', 'dob', 'city', 'country'];
                foreach ($columnHeader as $value) {
                    $sheet->setCellValueByColumnAndRow($columnCoordinate, 1, $value);
                    $columnCoordinate = $columnCoordinate + 1;
                }

                for ($i = 0; $i < count($jsonDataFail); $i++) {
                    unset($jsonDataFail[$i]['user_uuid']);
                    $row = $jsonDataFail[$i];
                    $columnCoordinateData = 1;
                    foreach ($row as $value) {
                        $sheet->setCellValueByColumnAndRow($columnCoordinateData, $i + 2, $value);
                        $columnCoordinateData = $columnCoordinateData + 1;
                    }
                }

                if ($extension == 'xlsx') {
                    $writer = new Xlsx($spreadsheet);
                } else {
                    $writer = new Csv($spreadsheet);
                }
                $writer->save($fileStorePath);

                return [
                    'have_error_data' => true,
                    'success_data' => $getData,
                    'data' => $getKey,
                    'errors' => $error,
                    'error_data' => $jsonDataFail,
                    'slug' => 'data_file_error/' . $fileName
                ];
            }

            return [
                'have_error_data' => false,
                'success_data' => $getData,
                'data' => $getKey
            ];
        } else {
            return false;
        }
    }

    /**
     * @param $file
     * @return array
     */
    public function importJsonFile($file)
    {
        $getKey = [];
        $getFileContents = json_decode(file_get_contents($file));
        $rules = [
            'email' => ['required', 'string', 'email:rfc,dns'],
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'middle_name' => ['nullable', 'string'],
            'phone' => ['nullable', 'numeric'],
            'dob' => ['nullable', 'date_format:Y-m-d'],
            'sex' => ['nullable', 'string'],
            'city' => ['nullable', 'string'],
            'country' => ['nullable', 'string'],
        ];

        foreach ($getFileContents as $key => $content) {
            $data = [
                'email' => $content->email,
                'last_name' => $content->last_name,
                'first_name' => $content->first_name,
                'middle_name' => $content->middle_name,
                'phone' => $content->phone,
                'sex' => $content->sex,
                'dob' => $content->dob,
                'city' => $content->city,
                'country' => $content->country,
                'user_uuid' => auth()->user()->getkey()
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                $error[] = $validator->errors()->merge(['Row fail' => __('messages.error_data') . ' ' . ($key + 1)]);
                $jsonDataFail[] = $data;
                continue;
            }
            $createData = $this->model->create($data);
            $getKey[] = $createData->uuid;
        }

        if (!empty($error)) {
            if (!File::exists(public_path('data_file_error'))) {
                File::makeDirectory(public_path('data_file_error'));
            }
            $errorData = json_encode($jsonDataFail);
            $fileName = 'import_failed_record_' . uniqid() . '_' . Carbon::today()->toDateString() . '.json';
            $fileStorePath = public_path('/data_file_error/' . $fileName);
            File::put($fileStorePath, $errorData);

            return [
                'have_error_data' => true,
                'data' => $getKey,
                'errors' => $error,
                'error_data' => $jsonDataFail,
                'slug' => 'data_file_error/' . $fileName
            ];
        }

        return [
            'have_error_data' => false,
            'data' => $getKey
        ];
    }
}
