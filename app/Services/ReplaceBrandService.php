<?php

namespace App\Services;

use Illuminate\Support\Str;

class ReplaceBrandService
{
    public function replaceBrand($template, $brand): string
    {
        $searchReplaceMap = $this->searchReplaceMapForBrand($brand);

        return Str::replace(array_keys($searchReplaceMap), $searchReplaceMap, $template);
    }

    public function searchReplaceMapForBrand($brand): array
    {
        return [
            '{product.brand.uuid}' => $brand['uuid'] ?? null,
            '{product.brand.name}' => $brand['name'] ?? null,
            '{product.brand.url}' => $brand['url'] ?? null
        ];

    }
}
