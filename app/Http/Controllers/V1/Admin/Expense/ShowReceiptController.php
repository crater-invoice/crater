<?php

namespace Crater\Http\Controllers\V1\Admin\Expense;

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
        $this->authorize('view', $expense);

        if ($expense) {
            $media = $expense->getFirstMedia('receipts');

            if ($media) {
                return response()->file($media->getPath());
            }

            return respondJson('receipt_does_not_exist', 'Receipt does not exist.');
        }
    }
}
