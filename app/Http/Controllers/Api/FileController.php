<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\File;
use App\VotingTour;

class FileController extends ApiController
{
    /**
     * Get file data
     *
     * @param integer file_id - required
     *
     * @return json - response with status code and file data or errors
     */
    public function getData(Request $request)
    {
        $votingTour = VotingTour::getLatestTour();
        if (empty($votingTour)) {
            return $this->errorResponse(__('custom.file_not_found'));
        }

        $fileId = $request->get('file_id', null);

        $validator = \Validator::make(['file_id' => $fileId], [
            'file_id' => 'required|int|exists:files,id|digits_between:1,10',
        ]);

        if (!$validator->fails()) {
            try {
                $file = File::where('id', $fileId)->where('voting_tour_id', $votingTour->id)->first();
                if ($file) {
                    return $this->successResponse($file);
                }
            } catch (\Exception $e) {
                logger()->error($e->getMessage());
                return $this->errorResponse(__('custom.get_file_fail'), $e->getMessage());
            }
        }

        return $this->errorResponse(__('custom.file_not_found'), $validator->errors()->messages());
    }
}
