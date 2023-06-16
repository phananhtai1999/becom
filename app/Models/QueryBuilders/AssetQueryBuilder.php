<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\Asset;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use App\Sorts\SortHeightAssetSize;
use App\Sorts\SortWidthAssetSize;
use Doctrine\DBAL\Query\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\Concerns\SortsQuery;

class AssetQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return Asset::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new Asset())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'type',
                'title',
                'asset_size_uuid',
                'url',
                'user_uuid',
                'status'
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'type',
                'title',
                'asset_size_uuid',
                'url',
                'user_uuid',
                'status',
                AllowedSort::custom('asset_size_width', new SortWidthAssetSize()),
                AllowedSort::custom('asset_size_height', new SortHeightAssetSize())
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'type',
                AllowedFilter::exact('exact__type', 'type'),
                'title',
                AllowedFilter::exact('exact__title', 'title'),
                'asset_size_uuid',
                AllowedFilter::exact('exact__asset_size_uuid', 'asset_size_uuid'),
                'updated_at',
                AllowedFilter::exact('exact__updated_at', 'updated_at'),
                'assetSize.name',
                AllowedFilter::exact('exact__asset_size.name', 'assetSize.name'),
                'url',
                AllowedFilter::exact('exact__url', 'url'),
            ]);

    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return Asset::class;
    }

    /**
     * @param $search
     * @param $searchBy
     * @return mixed
     */
    public static function searchQuery($search, $searchBy)
    {
        $initialQuery = static::initialQuery();
        $baseQuery = static::fillAble();

        return SearchQueryBuilder::search($baseQuery, $initialQuery, $search, $searchBy);
    }
}
