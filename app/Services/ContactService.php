<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Http\Resources\StatusResource;
use App\Models\Contact;
use App\Models\QueryBuilders\ContactQueryBuilder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ContactService extends AbstractService
{
    protected $modelClass = Contact::class;

    protected $modelQueryBuilderClass = ContactQueryBuilder::class;

    /**
     * @param $campaignUuid
     * @return array
     */
    public function getContactsSendEmail($campaignUuid)
    {
        return $this->model->select('contacts.*')
            ->join('contact_contact_list', 'contact_contact_list.contact_uuid', '=', 'contacts.uuid')
            ->join('contact_lists', 'contact_lists.uuid', '=', 'contact_contact_list.contact_list_uuid')
            ->join('campaign_contact_list', 'campaign_contact_list.contact_list_uuid', '=', 'contact_lists.uuid')
            ->join('campaigns', 'campaigns.uuid', '=', 'campaign_contact_list.campaign_uuid')
            ->where('campaigns.uuid', $campaignUuid)->get()->unique('email');

    }

    /**
     * @param $campaignUuid
     * @return mixed
     */
    public function getContactsSendSms($campaignUuid)
    {
        return $this->model->select('contacts.*')
            ->join('contact_contact_list', 'contact_contact_list.contact_uuid', '=', 'contacts.uuid')
            ->join('contact_lists', 'contact_lists.uuid', '=', 'contact_contact_list.contact_list_uuid')
            ->join('campaign_contact_list', 'campaign_contact_list.contact_list_uuid', '=', 'contact_lists.uuid')
            ->join('campaigns', 'campaigns.uuid', '=', 'campaign_contact_list.campaign_uuid')
            ->where('campaigns.uuid', $campaignUuid)->get()->unique('phone');
    }

    /**
     * @param $campaignUuid
     * @return array
     */
    public function getBirthdayContactsSendEmail($campaignUuid)
    {
        $timezone = $this->getConfigByKeyInCache('timezone')->value;
        $currentTime = Carbon::parse(Carbon::now($timezone))->format('m-d');

        return $this->model->select('contacts.*')
            ->join('contact_contact_list', 'contact_contact_list.contact_uuid', '=', 'contacts.uuid')
            ->join('contact_lists', 'contact_lists.uuid', '=', 'contact_contact_list.contact_list_uuid')
            ->join('campaign_contact_list', 'campaign_contact_list.contact_list_uuid', '=', 'contact_lists.uuid')
            ->join('campaigns', 'campaigns.uuid', '=', 'campaign_contact_list.campaign_uuid')
            ->where('campaigns.uuid', $campaignUuid)
            ->where(function ($query) use ($currentTime) {
                $query->whereRaw("DATE_FORMAT(contacts.dob, '%m-%d') = '{$currentTime}'");
            })->get()->unique('email');
    }

    /**
     * @param $campaignUuid
     * @return mixed
     */
    public function getBirthdayContactsSendSms($campaignUuid)
    {
        $timezone = $this->getConfigByKeyInCache('timezone')->value;
        $currentTime = Carbon::parse(Carbon::now($timezone))->format('m-d');

        return $this->model->select('contacts.*')
            ->join('contact_contact_list', 'contact_contact_list.contact_uuid', '=', 'contacts.uuid')
            ->join('contact_lists', 'contact_lists.uuid', '=', 'contact_contact_list.contact_list_uuid')
            ->join('campaign_contact_list', 'campaign_contact_list.contact_list_uuid', '=', 'contact_lists.uuid')
            ->join('campaigns', 'campaigns.uuid', '=', 'campaign_contact_list.campaign_uuid')
            ->where('campaigns.uuid', $campaignUuid)
            ->where(function ($query) use ($currentTime) {
                $query->whereRaw("DATE_FORMAT(contacts.dob, '%m-%d') = '{$currentTime}'");
            })->get()->unique('phone');
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
            //get contacts by campaign with send_type 'email'
            ->where([
                ['campaigns.send_type', 'email'],
                ['campaigns.uuid', $campaignUuid],
                ['contacts.email', $email]
            ])
            //get contacts by campaign with send_type 'phone'
            ->orWhere([
                ['campaigns.send_type', '!=', 'email'],
                ['campaigns.uuid', $campaignUuid],
                ['contacts.phone', $email]
            ])
            ->first();
    }

    /**
     * @param $campaignUuid
     * @param $email
     * @return mixed
     */
    public function getContactByCampaignTypeEmail($campaignUuid, $email)
    {
        return $this->model->select('contacts.*')
            ->join('contact_contact_list', 'contact_contact_list.contact_uuid', '=', 'contacts.uuid')
            ->join('contact_lists', 'contact_lists.uuid', '=', 'contact_contact_list.contact_list_uuid')
            ->join('campaign_contact_list', 'campaign_contact_list.contact_list_uuid', '=', 'contact_lists.uuid')
            ->join('campaigns', 'campaigns.uuid', '=', 'campaign_contact_list.campaign_uuid')
            ->where([
                ['campaigns.uuid', $campaignUuid],
                ['contacts.email', $email]
            ])->get()->unique('email');
    }

    /**
     * @param $campaignUuid
     * @param $email
     * @return mixed
     */
    public function getContactByCampaignTypeSms($campaignUuid, $email)
    {
        return $this->model->select('contacts.*')
            ->join('contact_contact_list', 'contact_contact_list.contact_uuid', '=', 'contacts.uuid')
            ->join('contact_lists', 'contact_lists.uuid', '=', 'contact_contact_list.contact_list_uuid')
            ->join('campaign_contact_list', 'campaign_contact_list.contact_list_uuid', '=', 'contact_lists.uuid')
            ->join('campaigns', 'campaigns.uuid', '=', 'campaign_contact_list.campaign_uuid')
            ->where([
                ['campaigns.uuid', $campaignUuid],
                ['contacts.phone', $email]
            ])->get()->unique('phone');
    }

    /**
     * @param $campaignUuid
     * @param $email
     * @return mixed
     */
    public function addPointContactOpenByCampaign($campaignUuid, $email)
    {
        return $this->model->select('contacts.*')
            ->join('contact_contact_list', 'contact_contact_list.contact_uuid', '=', 'contacts.uuid')
            ->join('contact_lists', 'contact_lists.uuid', '=', 'contact_contact_list.contact_list_uuid')
            ->join('campaign_contact_list', 'campaign_contact_list.contact_list_uuid', '=', 'contact_lists.uuid')
            ->join('campaigns', 'campaigns.uuid', '=', 'campaign_contact_list.campaign_uuid')
            ->where([
                ['campaigns.send_type', 'email'],
                ['campaigns.uuid', $campaignUuid],  //add point campaign send_type is email
                ['contacts.email', $email]
            ])->orWhere([
                ['campaigns.send_type', 'sms'],
                ['campaigns.uuid', $campaignUuid],  //add point campaign send_type is sms
                ['contacts.phone', $email]
            ])->orWhere([
                ['campaigns.send_type', 'telegram'],
                ['campaigns.uuid', $campaignUuid],  //add point campaign send_type is telegram
                ['contacts.phone', $email]
            ])->orWhere([
                ['campaigns.send_type', 'viber'],
                ['campaigns.uuid', $campaignUuid],  //add point campaign send_type is viber
                ['contacts.phone', $email]
            ])
            ->get();
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
        $reader->setReadDataOnly(false);
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
                'phone' => ['nullable', 'numeric', 'digits_between:1,11'],
                'dob' => ['nullable', 'date_format:Y-m-d'],
                'sex' => ['nullable', 'string'],
                'city' => ['nullable', 'string'],
                'country' => ['nullable', 'string'],
            ];

            foreach ($getActiveSheet as $key => $value) {
                $row = array_combine($fields, $value);
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
                    'user_uuid' => auth()->user()->getkey(),
                    'status_uuid' => optional(app(StatusService::class)->selectStatusDefault(auth()->user()->getKey()))->uuid
                ];
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
                'user_uuid' => auth()->user()->getkey(),
                'status_uuid' => optional(app(StatusService::class)->selectStatusDefault(auth()->user()->getKey()))->uuid

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
        $totalContact = $this->model->selectRaw('sum(contacts.points) as points')
            ->join('contact_contact_list', 'contact_contact_list.contact_uuid', '=', 'contacts.uuid')
            ->join('contact_lists', 'contact_contact_list.contact_list_uuid', '=', 'contact_lists.uuid')
            ->when($contactListUuid, function ($query, $contactListUuid) {
                $query->where('contact_lists.uuid', $contactListUuid);
            })
            ->whereNull('contact_lists.deleted_at')
            ->whereDate('contacts.updated_at', '>=', $startDate)
            ->whereDate('contacts.updated_at', '<=', $endDate)
            ->first();

        return (int)$totalContact->points;
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
     * @return mixed
     */
    public function getListsContactsSendSmsByCampaigns($campaignUuid)
    {
        return $this->model->select('contacts.*')
            ->join('contact_contact_list', 'contact_contact_list.contact_uuid', '=', 'contacts.uuid')
            ->join('contact_lists', 'contact_lists.uuid', '=', 'contact_contact_list.contact_list_uuid')
            ->join('campaign_contact_list', 'campaign_contact_list.contact_list_uuid', '=', 'contact_lists.uuid')
            ->join('campaigns', 'campaigns.uuid', '=', 'campaign_contact_list.campaign_uuid')
            ->where('campaigns.uuid', $campaignUuid)->get()->unique('phone')->count();
    }

    /**
     * @param $campaignUuid
     * @param $fromDate
     * @param $toDate
     * @return mixed
     */
    public function getBirthdayContactsSendEmailsByCampaigns($campaignUuid, $fromDate, $toDate)
    {
        $fromDateFormatted = Carbon::parse($fromDate)->format('m-d');
        $toDateFormatted = Carbon::parse($toDate)->format('m-d');

        return $this->model->select('contacts.*')
            ->join('contact_contact_list', 'contact_contact_list.contact_uuid', '=', 'contacts.uuid')
            ->join('contact_lists', 'contact_lists.uuid', '=', 'contact_contact_list.contact_list_uuid')
            ->join('campaign_contact_list', 'campaign_contact_list.contact_list_uuid', '=', 'contact_lists.uuid')
            ->join('campaigns', 'campaigns.uuid', '=', 'campaign_contact_list.campaign_uuid')
            ->where(function ($query) use ($fromDateFormatted, $toDateFormatted) {
                $query->whereRaw("DATE_FORMAT(contacts.dob, '%m-%d') >= '{$fromDateFormatted}'")
                    ->whereRaw("DATE_FORMAT(contacts.dob, '%m-%d') <= '{$toDateFormatted}'");
            })
            ->where('campaigns.uuid', $campaignUuid)->get()->unique('email')->count();
    }

    /**
     * @param $campaignUuid
     * @param $fromDate
     * @param $toDate
     * @return mixed
     */
    public function getBirthdayContactsSendSmsByCampaigns($campaignUuid, $fromDate, $toDate)
    {
        $fromDateFormatted = Carbon::parse($fromDate)->format('m-d');
        $toDateFormatted = Carbon::parse($toDate)->format('m-d');

        return $this->model->select('contacts.*')
            ->join('contact_contact_list', 'contact_contact_list.contact_uuid', '=', 'contacts.uuid')
            ->join('contact_lists', 'contact_lists.uuid', '=', 'contact_contact_list.contact_list_uuid')
            ->join('campaign_contact_list', 'campaign_contact_list.contact_list_uuid', '=', 'contact_lists.uuid')
            ->join('campaigns', 'campaigns.uuid', '=', 'campaign_contact_list.campaign_uuid')
            ->where(function ($query) use ($fromDateFormatted, $toDateFormatted) {
                $query->whereRaw("DATE_FORMAT(contacts.dob, '%m-%d') >= '{$fromDateFormatted}'")
                    ->whereRaw("DATE_FORMAT(contacts.dob, '%m-%d') <= '{$toDateFormatted}'");
            })
            ->where('campaigns.uuid', $campaignUuid)->get()->unique('phone')->count();
    }

    /**
     * @return QueryBuilder
     */
    public function filteringByCustomContactField()
    {
        $modelKeyName = $this->model->getKeyName();

        return QueryBuilder::for($this->model)
            ->allowedFields([
                $modelKeyName,
                'email',
                'first_name',
                'last_name',
                'middle_name',
                'points',
                'phone',
                'sex',
                'dob',
                'city',
                'country',
                'avatar',
                'status_uuid',
                'user_uuid'
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'email',
                'first_name',
                'last_name',
                'middle_name',
                'points',
                'phone',
                'sex',
                'dob',
                'city',
                'country',
                'avatar',
                'status_uuid',
                'user_uuid'
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'email',
                AllowedFilter::exact('exact__email', 'email'),
                'first_name',
                AllowedFilter::exact('exact__first_name', 'first_name'),
                'last_name',
                AllowedFilter::exact('exact__last_name', 'last_name'),
                'middle_name',
                AllowedFilter::exact('exact__middle_name', 'middle_name'),
                'points',
                AllowedFilter::exact('exact__points', 'points'),
                'phone',
                AllowedFilter::exact('exact__phone', 'phone'),
                'sex',
                AllowedFilter::exact('exact__sex', 'sex'),
                'dob',
                AllowedFilter::exact('exact__dob', 'dob'),
                'city',
                AllowedFilter::exact('exact__city', 'city'),
                'country',
                AllowedFilter::exact('exact__country', 'country'),
                'avatar',
                AllowedFilter::exact('exact__avatar', 'avatar'),
                'status_uuid',
                AllowedFilter::exact('exact__status_uuid', 'status_uuid'),
                AllowedFilter::scope('exact__status.name', 'statusName'),
                'user_uuid',
                AllowedFilter::exact('exact__user_uuid', 'user_uuid'),
                'user.username',
                AllowedFilter::exact('exact__user.username', 'user.username'),
                AllowedFilter::scope('uuids_in'),
                AllowedFilter::scope('uuids_not_in'),
                AllowedFilter::scope('from__dob'),
                AllowedFilter::scope('to__dob'),
                //Custom filter full_name Append (LIKE)
                AllowedFilter::callback("full_name", function (Builder $query, $values) {
                    if (is_array($values)) {
                        $query->where(function ($q) use ($values) {
                            foreach ($values as $value) {
                                $fullName = ltrim($value, ' ');
                                $q->orWhereRaw("CONCAT(first_name, ' ', last_name) like '%$fullName%'");
                            }
                        });
                    } else {
                        $query->whereRaw("CONCAT(first_name, ' ', last_name) like '%$values%'");
                    }
                }),
                //Custom filter full_name Append (EXACT)
                AllowedFilter::callback("exact__full_name", function (Builder $query, $values) {
                    if (is_array($values)) {
                        $query->where(function ($q) use ($values) {
                            foreach ($values as $value) {
                                $fullName = ltrim($value, ' ');
                                $q->orWhereRaw("CONCAT(first_name, ' ', last_name) = '$fullName'");
                            }
                        });
                    } else {
                        $query->whereRaw("CONCAT(first_name, ' ', last_name) = '$values'");
                    }
                }),
                $this->getDuplicateFiltersByNumeric($modelKeyName),
                $this->getDuplicateFilters('email'),
                $this->getDuplicateFilters('first_name'),
                $this->getDuplicateFilters('last_name'),
                $this->getDuplicateFilters('middle_name'),
                $this->getDuplicateFilters('country'),
                $this->getDuplicateFilters('city'),
                $this->getDuplicateFilters('phone'),
                $this->getDuplicateFilters('sex'),
                $this->getDuplicateFiltersByNumeric('points'),
                $this->getDuplicateFiltersByNumeric('dob'),
                $this->getDuplicateFiltersByNumeric('user_uuid'),
                $this->getFilterRelationshipWithUser('user.username'),
            ]);
    }

    /**
     * @param $search
     * @param $searchBy
     * @return QueryBuilder
     */
    public function search($search, $searchBy): QueryBuilder
    {
        if ($search && !empty($searchBy)) {
            //Get all fields
            $getFillAble = app(Contact::class)->getFillable();
            $query = $this->filteringByCustomContactField();
            $query->where(function ($query) use ($search, $searchBy, $getFillAble) {
                foreach ($searchBy as $value) {
                    $query->when(in_array($value, $getFillAble), function ($q) use ($search, $value) {

                        return $q->orWhere($value, 'like', '%' . $search . '%');
                    });
                }
            });

            return $query;
        }

        return $this->filteringByCustomContactField();
    }

    /**
     * @param $field
     * @return AllowedFilter
     */
    public function getDuplicateFilters($field)
    {
        return AllowedFilter::callback("custom__$field", function (Builder $query, $value, $field) {
            if ($value[0] == $field) {
                if ($value[1] == '=') {
                    $query->whereIn($field, array_slice($value, 3));
                } elseif ($value[1] == '!=') {
                    $query->whereNotIn($field, array_slice($value, 3));
                } elseif ($value[1] == 'like') {
                    if (count($value) > 4) {
                        $query->where(function ($query) use ($value, $field) {
                            for ($i = 4; $i <= count($value); $i++) {
                                $query->orWhere($field, 'like', '%' . $value[$i - 1] . '%');
                            }
                        });
                    } else {
                        $query->where($field, 'like', '%' . $value[3] . '%');
                    }
                } elseif ($value[1] == 'empty') {
                    $query->whereNull($field);
                } elseif ($value[1] == '!empty') {
                    $query->whereNotNull($field);
                }
            }
        }, $field);
    }

    /**
     * @param $field
     * @return AllowedFilter
     */
    public function getDuplicateFiltersByNumeric($field)
    {
        return AllowedFilter::callback("custom__$field", function (Builder $query, $value, $field) {
            if ($value[0] == $field) {
                if ($value[1] == '=') {
                    $query->whereIn($field, array_slice($value, 3));
                } elseif ($value[1] == '!=') {
                    $query->whereNotIn($field, array_slice($value, 3));
                } elseif ($value[1] == 'like') {
                    if (count($value) > 4) {
                        $query->where(function ($query) use ($value, $field) {
                            for ($i = 4; $i <= count($value); $i++) {
                                $query->orWhere($field, 'like', '%' . $value[$i - 1] . '%');
                            }
                        });
                    } else {
                        $query->where($field, 'like', '%' . $value[3] . '%');
                    }
                } elseif ($value[1] == '>') {
                    $query->where($field, '>', max(array_slice($value, 3)));
                } elseif ($value[1] == '>=') {
                    $query->where($field, '>=', min(array_slice($value, 3)));
                } elseif ($value[1] == '<') {
                    $query->where($field, '<', min(array_slice($value, 3)));
                } elseif ($value[1] == '<=') {
                    $query->where($field, '<=', max(array_slice($value, 3)));
                } elseif ($value[1] == 'empty') {
                    $query->whereNull($field);
                } elseif ($value[1] == '!empty') {
                    $query->whereNotNull($field);
                }
            }
        }, $field);
    }

    /**
     * @param $field
     * @return AllowedFilter
     */
    public function getFilterRelationshipWithUser($field)
    {
        return AllowedFilter::callback("custom__$field", function (Builder $query, $value, $field) {
            if ($value[0] == $field) {
                if ($value[1] == '=') {
                    $query->whereExists(function ($user) use ($value) {
                        $user->from('users')
                            ->whereRaw('contacts.user_uuid = users.uuid')
                            ->whereIn('users.username', array_slice($value, 3));
                    });
                } elseif ($value[1] == '!=') {
                    $query->whereExists(function ($user) use ($value) {
                        $user->from('users')
                            ->whereRaw('contacts.user_uuid = users.uuid')
                            ->whereNotIn('users.username', array_slice($value, 3));
                    });
                } elseif ($value[1] == 'like') {
                    if (count($value) > 4) {
                        $query->where(function ($query) use ($value) {
                            for ($i = 4; $i <= count($value); $i++) {
                                $query->orWhereExists(function ($query) use ($value, $i) {
                                    $query->select("users.uuid")
                                        ->from('users')
                                        ->whereRaw('contacts.user_uuid = users.uuid')
                                        ->where('users.username', 'like', '%' . $value[$i - 1] . '%');
                                });
                            }
                        });
                    } else {
                        $query->whereExists(function ($user) use ($value) {
                            $user->select("users.uuid")
                                ->from('users')
                                ->whereRaw('contacts.user_uuid = users.uuid')
                                ->where('users.username', 'like', '%' . $value[3] . '%');
                        });
                    }
                }
            }
        }, $field);
    }

    /**
     * @param $uuidsIn
     * @param $uuidsNotIn
     * @param $perPage
     * @param $search
     * @param $searchBy
     * @return LengthAwarePaginator|QueryBuilder
     */
    public function sortContactsToTopOrBottomOfListByUuid($uuidsIn, $uuidsNotIn, $perPage, $search, $searchBy)
    {
        $arrayIntersectUuidsIn = array_intersect(explode(',', $uuidsIn), $this->model->all()->pluck('uuid')->toArray());
        $arrayIntersectUuidsNotIn = array_intersect(explode(',', $uuidsNotIn), $this->model->all()->pluck('uuid')->toArray());
        if (!empty($uuidsIn) && !empty($uuidsNotIn) && !empty($arrayIntersectUuidsIn) && !empty($arrayIntersectUuidsNotIn)) {

            $collection = $this->search($search, $searchBy)->get()
                ->sortBy(function ($item) use ($arrayIntersectUuidsIn, $arrayIntersectUuidsNotIn) {
                    if (in_array($item->uuid, $arrayIntersectUuidsIn) || !in_array($item->uuid, $arrayIntersectUuidsNotIn)) {

                        return $item->created_at;
                    }
                }, SORT_STRING, true);

            return $this->collectionPagination($collection, $perPage);
        } elseif (!empty($uuidsIn) && !empty($arrayIntersectUuidsIn) && empty($uuidsNotIn) && empty($arrayIntersectUuidsNotIn)) {
            //Uuids_in
            $collection = $this->search($search, $searchBy)->get()
                ->sortBy(function ($item) use ($arrayIntersectUuidsIn) {
                    if (in_array($item->uuid, $arrayIntersectUuidsIn)) {

                        return $item->created_at;
                    }
                }, SORT_STRING, true);

            return $this->collectionPagination($collection, $perPage);
        } elseif (!empty($uuidsNotIn) && !empty($arrayIntersectUuidsNotIn) && empty($uuidsIn) && empty($arrayIntersectUuidsIn)) {
            //Uuids_Not_in
            $collection = $this->search($search, $searchBy)->get()
                ->sortBy(function ($item) use ($arrayIntersectUuidsNotIn) {
                    if (!in_array($item->uuid, $arrayIntersectUuidsNotIn)) {

                        return $item->created_at;
                    }
                }, SORT_STRING, true);

            return $this->collectionPagination($collection, $perPage);
        } elseif (
            (!empty($uuidsIn) && !empty($uuidsNotIn) && empty($arrayIntersectUuidsIn) && empty($arrayIntersectUuidsNotIn)) ||
            (!empty($uuidsIn) && !empty($uuidsNotIn) && !empty($arrayIntersectUuidsIn) && empty($arrayIntersectUuidsNotIn)) ||
            (!empty($uuidsIn) && !empty($uuidsNotIn) && empty($arrayIntersectUuidsIn) && !empty($arrayIntersectUuidsNotIn))
        ) {

            return $this->collectionPagination([], $perPage);
        }

        return $this->search($search, $searchBy);
    }

    /**
     * @param $contacts
     * @return mixed
     */
    public function checkActiveStatus($contacts)
    {
        //Get All Status Admin
        $statusAdmin = app(StatusService::class)->getAllStatusDefault();
        $contacts->each(function ($contact) use ($statusAdmin) {
            $statusUser = app(StatusService::class)->getAllStatusByUserUuid($contact->user_uuid);
            if ($statusUser->count() != 0) {
                $contact->status_active = $statusUser->where('points', '<=', $contact->points)->sortByDesc('points')->first() ?: app(StatusService::class)->firstStatusByUserUuid($contact->user_uuid);
                $contact->status_list = $statusUser;
            } else {
                $contact->status_active = $statusAdmin->where('points', '<=', $contact->points)->sortByDesc('points')->first() ?: app(StatusService::class)->firstStatusAdmin();
                $contact->status_list = $statusAdmin;
            }
            //List status admin
            $contact->admin_status_active = $statusAdmin->where('points', '<=', $contact->points)->sortByDesc('points')->first() ?: app(StatusService::class)->firstStatusAdmin();
            $contact->admin_status_list = $statusAdmin;
        });

        return $contacts;
    }
}
