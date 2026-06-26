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
            'state' => $first->state,
            'city' => $first->city ?: $first->municipality,
            'municipality' => $first->municipality,
            'settlements' => $rows->map(fn (PostalCode $row) => [
                'name' => $row->settlement,
                'type' => $row->settlement_type,
                'zone' => $row->zone,
            ])->values(),
        ]);
    }
}
