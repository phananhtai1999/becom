<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Asset;
use App\Models\QueryBuilders\AssetQueryBuilder;
use App\Models\Role;
use Carbon\Carbon;

class AssetService extends AbstractService
{
    protected $modelClass = Asset::class;

    protected $modelQueryBuilderClass = AssetQueryBuilder::class;

    public function getGifDuration($filename)
    {
        $gifData = file_get_contents($filename);

        $delayPositions = [];
        $offset = 0;
        while (($position = strpos($gifData, "\x21\xF9\x04", $offset)) !== false) {
            $delayPositions[] = $position + 4;
            $offset = $position + 1;
        }

        $totalDuration = 0;
        foreach ($delayPositions as $position) {
            $delayBytes = substr($gifData, $position, 2);
            $delayTime = unpack('v', $delayBytes)[1];
            $totalDuration += $delayTime;
        }

        return $totalDuration / 100.0;
    }

    public function getFrames($filename)
    {
        $gifData = file_get_contents($filename);
        $lastFramePosition = strrpos($gifData, "\x00\x2C");

        return substr_count($gifData, "\x00\x21\xF9\x04", 0, $lastFramePosition + 1);
    }

    function getGifLoopCount($filepath)
    {
        $gifData = file_get_contents($filepath);

        preg_match('/\x21\xFF\x0B(?:\x4E\x45\x54\x53\x43\x41\x50\x45\x32\x2E\x30\x03\x01(.{2}))/', $gifData, $matches);
        $loopCount = isset($matches[1]) ? unpack('v', $matches[1])[1] : 0;

        return $loopCount;
    }

    public function validateGif($filename, $uploadUrl)
    {
        $duration = $this->getGifDuration($filename);
        $loop = $this->getGifLoopCount($filename);
        if (empty($loop) || $duration > 30 || $loop * $duration > 30) {

            return ['is_failed' => true, 'message' => 'The gif longer than 30s'];
        } elseif ($this->getFrames($filename) / $duration > 5) {

            return ['is_failed' => true, 'message' => 'The gif must be smaller than 5FPS'];
        }

        return ['is_failed' => false];
    }

    public function addJsCodeToIndex($models)
    {
        $mainUrl = $this->getConfigByKeyInCache('main_url');
        $code = auth()->user()->partner->code ?? Role::ROLE_ADMIN;
        foreach ($models as $model) {
            if ($model->type == Asset::TYPE_IMAGE) {
                $jsCode = '<script type="text/javascript" src="' . asset("/") . 'api/generate-image?pn=' . $model->uuid . '&as=' . $model->uuid . '&link=' . $mainUrl->value . '?ref=' . $code . '"> </script>';
            } else {
                $jsCode = '<script type="text/javascript" src="' . asset("/") . 'api/generate-video?pn=' . $model->uuid . '&as=' . $model->uuid . '&link=' . $mainUrl->value . '?ref=' . $code . '"> </script>';
            }
            $model->js_code = $jsCode;
        }
    }

    public function showAssetForEditorById($id)
    {
        return $this->model->whereIn('status', [Asset::PENDING_STATUS, Asset::REJECT_STATUS, Asset::DRAFT_STATUS])
            ->where('uuid', $id)->firstOrFail();
    }

    public function destroyMyAsset($id)
    {
        $model = $this->findOneWhereOrFail([
            'uuid' => $id,
            'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
        ]);
        $model->delete();
    }

    public function totalEditorAssetChart($startDate, $endDate)
    {
        return $this->model->selectRaw("COUNT(IF( status = 'publish', 1, NULL ) ) as approve,
        COUNT(IF( status = 'pending', 1, NULL ) ) as pending,
        COUNT(IF( status = 'reject', 1, NULL ) ) as reject")
            ->where([
                ['user_uuid', auth()->userId()],
                ['app_id', auth()->appId()]
            ])
            ->whereDate('updated_at', '>=', $startDate)
            ->whereDate('updated_at', '<=', $endDate)
            ->first()->toArray();
    }

    public function getAssetsChartByDateFormat($dateFormat, $startDate, $endDate)
    {
        return $this->model->selectRaw("date_format(updated_at, '{$dateFormat}') as label,
        COUNT(IF( status = 'publish', 1, NULL ) ) as approve,
        COUNT(IF( status = 'pending', 1, NULL ) ) as pending,
        COUNT(IF( status = 'reject', 1, NULL ) ) as reject")
            ->where([
                ['user_uuid', auth()->userId()],
                ['app_id', auth()->appId()]
            ])
            ->whereDate('updated_at', '>=', $startDate)
            ->whereDate('updated_at', '<=', $endDate)
            ->groupBy('label')
            ->orderBy('label', 'ASC')
            ->get();
    }

    public function editorAssetChart($groupBy, $startDate, $endDate)
    {
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);
        $currentDate = $startDate->copy();
        $times = [];
        $result = [];

        if ($groupBy == "hour") {
            $dateFormat = "%Y-%m-%d %H:00:00";

            $endDate = $endDate->endOfDay();
            while ($currentDate <= $endDate) {
                $times[] = $currentDate->format('Y-m-d H:00:00');
                $currentDate = $currentDate->addHour();
            }
        }

        if ($groupBy == "date") {
            $dateFormat = "%Y-%m-%d";
            while ($currentDate <= $endDate) {
                $times[] = $currentDate->format('Y-m-d');
                $currentDate = $currentDate->addDay();
            }
        }

        if ($groupBy == "month") {
            $dateFormat = "%Y-%m";
            while ($currentDate <= $endDate) {
                $times[] = $currentDate->format('Y-m');
                $currentDate = $currentDate->addMonth();
            }
        }

        $charts = $this->getAssetsChartByDateFormat($dateFormat, $startDate, $endDate)->keyBy('label');

        foreach ($times as $time) {
            $mailByTime = $charts->first(function ($item, $key) use ($time) {
                return $key == $time;
            });

            if ($mailByTime) {
                $result[] = $mailByTime->toArray();
            } else {
                $result [] = [
                    'label' => $time,
                    'approve' => 0,
                    'pending' => 0,
                    'reject' => 0,
                ];
            }
        }

        return $result;
    }
}
