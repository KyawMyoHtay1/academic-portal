<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SearchResultResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => $this['type'] ?? null,
            'id' => $this['id'] ?? null,
            'title' => $this['title'] ?? null,
            'subtitle' => $this['subtitle'] ?? null,
            'url' => $this['url'] ?? null,
        ];
    }
}
