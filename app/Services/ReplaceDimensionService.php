<?php

namespace App\Services;

use Illuminate\Support\Str;

class ReplaceDimensionService
{
    public function replaceDimension($template, $dimension): string
    {
        $searchReplaceMap = $this->searchReplaceMapForDimension($dimension);

        return Str::replace(array_keys($searchReplaceMap), $searchReplaceMap, $template);
    }

    public function searchReplaceMapForDimension($dimension): array
    {
        return [
            '{product.dimension.uuid}' => $dimension['uuid'] ?? null,
            '{product.dimension.length}' => $dimension['name'] ?? null,
            '{product.dimension.width}' => $dimension['width'] ?? null,
            '{product.dimension.height}' => $dimension['height'] ?? null,
            '{product.dimension.weight}' => $dimension['weight'] ?? null,
            '{product.dimension.unit_type_weight}' => $dimension['unit_type_weight'] ?? null,
            '{product.dimension.unit_type_dimension}' => $dimension['unit_type_dimension'] ?? null
        ];

    }
}
