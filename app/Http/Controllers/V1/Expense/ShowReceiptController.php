<?php

namespace Crater\Http\Controllers\V1\Expense;

use Crater\Http\Controllers\Controller;
use Crater\Models\Expense;

class ShowReceiptController extends Controller
{
    /**
     * Retrieve details of an expense receipt from storage.
     *
     * @param   \Crater\Models\Expense $expense
     * @return  \Illuminate\Http\JsonResponse
     */
    public function __invoke(Expense $expense)
    {
        $imagePath = null;

        if ($expense) {
            $media = $expense->getFirstMedia('receipts');
            if ($media) {
                $imagePath = $media->getPath();
            } else {
                return response()->json([
                    'error' => 'receipt_does_not_exist',
                ]);
            }
        }

        $type = \File::mimeType($imagePath);

        $image = 'data:'.$type.';base64,'.base64_encode(file_get_contents($imagePath));

        return response()->json([
            'image' => $image,
            'type' => $type,
        ]);
    }
}
