<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
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
        $fileId = $request->get('file_id', null);

        $validator = \Validator::make(['file_id' => $fileId], [
            'file_id' => 'required|int|exists:files,id|digits_between:1,10',
        ]);

        if (!$validator->fails()) {
            try {
                $result = [];

                $votingTour = VotingTour::getLatestTour();
                if (!empty($votingTour)) {
                    $file = File::where('id', $fileId)->where('voting_tour_id', $votingTour->id)->first();

                    if ($file) {
                        $result = [
                            'id'         => $file->id,
                            'name'       => $file->name,
                            'data'       => base64_encode($file->data),
                            'mime_type'  => $file->mime_type,
                            'message_id' => $file->message_id,
                            'org_id'     => $file->org_id,
                            'created_at' => $file->created_at,
                        ];
                    }

                    return $this->successResponse($result);
                }
            } catch (QueryException $e) {
                logger()->error($e->getMessage());
                return $this->errorResponse(__('custom.get_file_fail'));
            }
        }

        return $this->errorResponse(__('custom.file_not_found'), $validator->errors()->messages());
    }
}
