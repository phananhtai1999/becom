<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Contact;
use App\Models\QueryBuilders\ContactQueryBuilder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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
     * @return array
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
        foreach ($contactsCampaign as $contactCampaign) {
            if (empty($resultContacts)) {
                $resultContacts[] = $contactCampaign;
            } else {
                foreach ($resultContacts as $value) {
                    if ($contactCampaign->email == $value->email) {
                        $checkEmailExist = true;
                        break;
                    } else {
                        $checkEmailExist = false;
                    }
                }

                if (!$checkEmailExist) {
                    $resultContacts[] = $contactCampaign;
                }
            }
        }

        return $resultContacts;
    }

    /**
     * @param $campaignUuid
     * @return array
     */
    public function getBirthdayContactsSendEmail($campaignUuid)
    {
        $birthdayContacts = $this->model->select('contacts.*')
            ->join('contact_contact_list', 'contact_contact_list.contact_uuid', '=', 'contacts.uuid')
            ->join('contact_lists', 'contact_lists.uuid', '=', 'contact_contact_list.contact_list_uuid')
            ->join('campaign_contact_list', 'campaign_contact_list.contact_list_uuid', '=', 'contact_lists.uuid')
            ->join('campaigns', 'campaigns.uuid', '=', 'campaign_contact_list.campaign_uuid')
            ->where('campaigns.uuid', $campaignUuid)
            ->whereDate('contacts.dob', Carbon::now())->get();

        $resultBirthdayContacts = [];
        $checkEmailExist = true;
        foreach ($birthdayContacts as $birthdayContact) {
            if (empty($resultBirthdayContacts)) {
                $resultBirthdayContacts[] = $birthdayContact;
            } else {
                foreach ($resultBirthdayContacts as $value) {
                    if ($birthdayContact->email == $value->email) {
                        $checkEmailExist = true;
                        break;
                    } else {
                        $checkEmailExist = false;
                    }
                }

                if (!$checkEmailExist) {
                    $resultBirthdayContacts[] = $birthdayContact;
                }
            }
        }

        return $resultBirthdayContacts;
    }

    /**
     * @param $campaignUuid
     * @param $email
     * @return mixed
     */
    public function getContactByCampaign($campaignUuid, $email)
    {
        return $this->model->select('contacts.*')
            ->join('contact_contact_list', 'contact_contact_list.contact_uuid', '=', 'contacts.uuid')
            ->join('contact_lists', 'contact_lists.uuid', '=', 'contact_contact_list.contact_list_uuid')
            ->join('campaign_contact_list', 'campaign_contact_list.contact_list_uuid', '=', 'contact_lists.uuid')
            ->join('campaigns', 'campaigns.uuid', '=', 'campaign_contact_list.campaign_uuid')
            ->where([
                ['campaigns.uuid', $campaignUuid],
                ['contacts.email', $email]
            ])->first();
    }

    /**
     * @param $contact
     * @param $contactListUuid
     * @return mixed
     */
    public function checkAndInsertContactIntoContactList($contact, $contactListUuid)
    {
        $model = $this->model->select('contacts.*')
            ->join('contact_contact_list', 'contact_contact_list.contact_uuid', '=', 'contacts.uuid')
            ->where('contact_contact_list.contact_list_uuid', $contactListUuid)
            ->where('contacts.email', $contact->email)
            ->first();

        if (empty($model)) {
            $contact->contactLists()->attach($contactListUuid);
            return $contact;
        }
        return $model;
    }

    /**
     * @param $campaignUuid
     * @param $email
     * @return void
     */
    public function addPointContactOpenMailCampaign($campaignUuid, $email)
    {
        $contactsOpenMail = $this->model->select('contacts.*')
            ->join('contact_contact_list', 'contact_contact_list.contact_uuid', '=', 'contacts.uuid')
            ->join('contact_lists', 'contact_lists.uuid', '=', 'contact_contact_list.contact_list_uuid')
            ->join('campaign_contact_list', 'campaign_contact_list.contact_list_uuid', '=', 'contact_lists.uuid')
            ->join('campaigns', 'campaigns.uuid', '=', 'campaign_contact_list.campaign_uuid')
            ->where([
                ['campaigns.uuid', $campaignUuid],
                ['contacts.email', $email]
            ])->get();

        foreach ($contactsOpenMail as $contactOpenMail) {
            $this->update($contactOpenMail, [
                'points' => $contactOpenMail->points + 1
            ]);
        }
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

    /**
     * @param $startDate
     * @param $endDate
     * @param $contactListUuid
     * @return int
     */
    public function getTotalPointsContactByContactList($startDate, $endDate, $contactListUuid = null)
    {
        if (empty($contactListUuid)) {
            $totalMyContact = $this->model->selectRaw('sum(contacts.points) as points')
                ->join('contact_contact_list', 'contact_contact_list.contact_uuid', '=', 'contacts.uuid')
                ->join('contact_lists', 'contact_contact_list.contact_list_uuid', '=', 'contact_lists.uuid')
                ->whereNull('contact_lists.deleted_at')
                ->whereDate('contacts.updated_at', '>=', $startDate)
                ->whereDate('contacts.updated_at', '<=', $endDate)
                ->first();
        } else {
            $totalMyContact = $this->model->selectRaw('sum(contacts.points) as points')
                ->join('contact_contact_list', 'contact_contact_list.contact_uuid', '=', 'contacts.uuid')
                ->join('contact_lists', 'contact_contact_list.contact_list_uuid', '=', 'contact_lists.uuid')
                ->where('contact_lists.uuid', $contactListUuid)
                ->whereNull('contact_lists.deleted_at')
                ->whereDate('contacts.updated_at', '>=', $startDate)
                ->whereDate('contacts.updated_at', '<=', $endDate)
                ->first();
        }

        return (int)$totalMyContact->points;
    }

    /**
     * @param $dateFormat
     * @param $startDate
     * @param $endDate
     * @param $type
     * @param $contactListUuid
     * @return array
     */
    public function createQueryGetPointsContactByContactList($dateFormat, $startDate, $endDate, $type, $contactListUuid)
    {
//        SELECT today.label, today.pointsContact, (today.pointsContact - yest.pointsContact) as increase
//FROM (SELECT date_format(updated_at, '%Y-%m-%d') as label, SUM(points) as pointsContact
//from contacts
//where date(updated_at) >= '2022-11-21' AND date(updated_at) <= '2022-11-24'
//GROUP By label) today LEFT JOIN
//    (SELECT date_format(updated_at, '%Y-%m-%d') as label, SUM(points) as pointsContact
//from contacts
//where date(updated_at) >= '2022-11-21' AND date(updated_at) <= '2022-11-24'
//GROUP By label) yest On yest.label = today.label - INTERVAL 1 day;
        $queryContactList = !empty($contactListUuid) ? "and cl.uuid = '{$contactListUuid}'" : "";
        $string = $type === "month" ? "-01" : "";
        $todayPointsContactTableSubQuery = $yesterdayPointsContactTableSubQuery = "(SELECT date_format(c.updated_at, '{$dateFormat}') as label, sum(c.points) as points
                  from contacts c, contact_contact_list ccl, contact_lists cl
                  where c.uuid = ccl.contact_uuid AND ccl.contact_list_uuid = cl.uuid and
                  date(c.updated_at) >= '{$startDate}' and date(c.updated_at) <= '{$endDate}'
                  {$queryContactList}
                  and c.deleted_at is NULL and cl.deleted_at is NULL
                  GROUP By label)";

        return DB::table(DB::raw("$todayPointsContactTableSubQuery as today"))->selectRaw("today.label, today.points, (today.points - yest.points) as increase")
            ->leftJoin(DB::raw("$yesterdayPointsContactTableSubQuery as yest"), 'yest.label', '=', DB::raw("date_format(concat(today.label, '$string') - INTERVAL 1 {$type}, '{$dateFormat}')"))
            ->get()->toArray();
    }

    /**
     * @param $groupBy
     * @param $startDate
     * @param $endDate
     * @param $contactListUuid
     * @return array
     */
    public function getPointsContactChartByContactList($groupBy, $startDate, $endDate, $contactListUuid = null)
    {
        $times = $result = [];
        $check = true;
        $subDate = $startDate;
        $startDate = Carbon::parse($startDate);

        if ($groupBy === "hour") {
            $dateFormat = "%Y-%m-%d %H:00:00";
            $subDate = Carbon::parse($subDate)->subDay();
            $endDate = Carbon::parse($endDate)->endOfDay();

            while ($startDate <= $endDate) {
                $times[] = $startDate->format('Y-m-d H:00:00');
                $startDate = $startDate->addHour();
            }
        }
        if ($groupBy === "date") {
            $dateFormat = "%Y-%m-%d";
            $subDate = Carbon::parse($subDate)->subDay();
            $endDate = Carbon::parse($endDate);

            while ($startDate <= $endDate) {
                $times[] = $startDate->format('Y-m-d');
                $startDate = $startDate->addDay();
            }
        }
        if ($groupBy === "month") {
            $dateFormat = "%Y-%m";
            $subDate = Carbon::parse($subDate)->subMonth();
            $endDate = Carbon::parse($endDate);

            while ($startDate <= $endDate) {
                $times[] = $startDate->format('Y-m');
                $startDate = $startDate->addMonth();
            }
        }

        $pointsContactsChart = $this->createQueryGetPointsContactByContactList($dateFormat, $subDate, $endDate, $groupBy === 'date' ? 'day' : $groupBy, $contactListUuid);

        $lastIncrease = 0;
        foreach ($times as $time) {
            if (!empty($pointsContactsChart)) {
                foreach ($pointsContactsChart as $pointsContactChart) {
                    if ($time == $pointsContactChart->label) {
                        $result[] = [
                            'label' => $pointsContactChart->label,
                            'points' => (int)$pointsContactChart->points,
                            'increase' => (int)($pointsContactChart->increase ?? $pointsContactChart->points)
                        ];
                        $check = true;
                        break;
                    } else {
                        $prevTime = $time;
                        if ($groupBy === 'hour') {
                            $prevTime = Carbon::parse($prevTime)->subHour()->toDateTimeString();
                        }
                        if ($groupBy === 'date') {
                            $prevTime = Carbon::parse($prevTime)->subDay()->toDateString();
                        }
                        if ($groupBy === 'month') {
                            $prevTime = Carbon::parse($prevTime)->subMonth()->format('Y-m');
                        }
                        if ($prevTime == $pointsContactChart->label) {
                            $lastIncrease = $pointsContactChart->points;
                        }
                        $check = false;
                    }
                }
                if (!$check) {
                    $result[] = [
                        'label' => $time,
                        'points' => 0,
                        'increase' => -$lastIncrease
                    ];
                    $lastIncrease = 0;
                }
            } else {
                $result[] = [
                    'label' => $time,
                    'points' => 0,
                    'increase' => 0

                ];
            }
        }
        return $result;
    }

    /**
     * @param $campaignUuid
     * @return mixed
     */
    public function getListsContactsSendEmailsByCampaigns($campaignUuid)
    {
        return $this->model->select('contacts.*')
            ->join('contact_contact_list', 'contact_contact_list.contact_uuid', '=', 'contacts.uuid')
            ->join('contact_lists', 'contact_lists.uuid', '=', 'contact_contact_list.contact_list_uuid')
            ->join('campaign_contact_list', 'campaign_contact_list.contact_list_uuid', '=', 'contact_lists.uuid')
            ->join('campaigns', 'campaigns.uuid', '=', 'campaign_contact_list.campaign_uuid')
            ->where('campaigns.uuid', $campaignUuid)->get()->unique('email')->count();
    }

    /**
     * @param $campaignUuid
     * @param $fromDate
     * @param $toDate
     * @return mixed
     */
    public function getBirthdayContactsSendEmailsByCampaigns($campaignUuid, $fromDate, $toDate)
    {
        return $this->model->select('contacts.*')
            ->join('contact_contact_list', 'contact_contact_list.contact_uuid', '=', 'contacts.uuid')
            ->join('contact_lists', 'contact_lists.uuid', '=', 'contact_contact_list.contact_list_uuid')
            ->join('campaign_contact_list', 'campaign_contact_list.contact_list_uuid', '=', 'contact_lists.uuid')
            ->join('campaigns', 'campaigns.uuid', '=', 'campaign_contact_list.campaign_uuid')
            ->whereDate('contacts.dob', '>=', $fromDate)
            ->whereDate('contacts.dob', '<=', $toDate)
            ->where('campaigns.uuid', $campaignUuid)->get()->unique('email')->count();
    }
}
