<?php

namespace Crater\Http\Controllers\V1\PDF;

use Crater\Http\Controllers\Controller;
use Crater\Models\Expense;

class DownloadReceiptController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Expense $expense
     * @param   string $hash
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Expense $expense)
    {
        $this->authorize('view', $expense);

        if ($expense) {
            $media = $expense->getFirstMedia('receipts');
            if ($media) {
                $imagePath = $media->getPath();
                $response = \Response::download($imagePath, $media->file_name);
                if (ob_get_contents()) {
                    ob_end_clean();
                }

                return $response;
            }
        }

        return response()->json([
            'error' => 'receipt_not_found',
        ]);
    }
}
