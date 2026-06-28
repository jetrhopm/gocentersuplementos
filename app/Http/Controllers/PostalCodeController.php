<?php

namespace App\Http\Controllers;

use App\Models\PostalCode;
use Illuminate\Http\JsonResponse;

class PostalCodeController extends Controller
{
    public function show(string $postalCode): JsonResponse
    {
        if (! preg_match('/^\d{5}$/', $postalCode)) {
            return response()->json([
                'ok' => false,
                'message' => 'El codigo postal debe tener 5 digitos.',
            ], 422);
        }

        $rows = PostalCode::query()
            ->where('postal_code', $postalCode)
            ->orderBy('settlement')
            ->get();

        if ($rows->isEmpty()) {
            return response()->json([
                'ok' => false,
                'message' => 'No encontramos colonias para ese codigo postal.',
            ], 404);
        }

        $first = $rows->first();

        return response()->json([
            'ok' => true,
            'postal_code' => $postalCode,
            'state' => $this->safeText($first->state),
            'city' => $this->safeText($first->city ?: $first->municipality),
            'municipality' => $this->safeText($first->municipality),
            'settlements' => $rows->map(fn (PostalCode $row) => [
                'name' => $this->safeText($row->settlement),
                'type' => $this->safeText($row->settlement_type),
                'zone' => $this->safeText($row->zone),
            ])->values(),
        ], 200, [], JSON_INVALID_UTF8_SUBSTITUTE);
    }

    private function safeText(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return mb_convert_encoding($value, 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252');
    }
}
