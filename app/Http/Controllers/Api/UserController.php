<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use App\Http\Controllers\ApiController;
use App\VotingTour;

class UserController extends ApiController
{
    /**
     * Generate password reset hash.
     *
     * @return json $response - response with status and the generated hash if successful
     */
    public function generatePasswordHash()
    {
        try {
            $password = Hash::make(str_random(60));
        } catch (\Exception $e) {
            logger()->errror($e->getMessage());
            return $this->errorResponse(__('custom.password_hash_generation_fail'), $e->getMessage());
        }

        return $this->successResponse(['hash' => $password], true);
    }

    /**
     * Reset user password.
     *
     * @param string new_password - required
     * @param string hash - required
     *
     * @return json $response - response with status and user id if successful
     */
    public function resetPassword(Request $request)
    {
        $new_password = $request->get('new_password');
        $hash = $request->get('hash');

        $rules = [
            'hash'     => 'required',
            'password' => 'required|min:6',
        ];

        $validator = \Validator::make(['password' => $new_password, 'hash' => $hash], $rules);

        if ($validator->fails()) {
            return $this->errorResponse(__('custom.password_reset_fail'), $validator->errors()->messages());
        }

        $user = User::where('pw_reset_hash', $hash)->first();

        if ($user) {
            try {
                $user->update(['password' => Hash::make($new_password)]);

                return $this->successResponse(['id' => $user->id], true);
            } catch (QueryException $e) {
                logger()->error($e->getMessage());
                return $this->errorResponse(__('custom.database_error'));
            }
        }

        return $this->errorResponse(__('custom.password_reset_token_invalid'));
    }

    /**
     * Add new user record
     *
     * @param array data - required
     * @param string data[firs_tname] - required
     * @param string data[last_name] - required
     * @param string data[email] - required
     * @param string data[username] - optional
     * @param string data[password] - required
     * @param string data[active] - required
     * @param string data[org_id] - optional
     *
     * @return json $response - response with status and api key if successful
     */
    public function addUser(Request $request)
    {
        $data = $request->get('data', []);

        $rules = [
            'org_id'           => 'nullable',
            'first_name'       => 'nullable|string',
            'last_name'        => 'nullable|string',
            'username'         => 'required|string|unique:users',
            'email'            => '',
            'active'           => 'nullable|bool',
            'password'         => 'required|string|min:6',
            'password_confirm' => 'required|string|same:password',
        ];

        if (!isset($data['org_id'])) {
            $rules['email'] = 'required|email|unique:users';
            $rules['first_name'] = 'required|string';
            $rules['last_name'] = 'required|string';
            $rules['active'] = 'required|bool';
        }
        else{
            $votingTour = VotingTour::getLatestTour();
            if (!$votingTour) {
                return $this->errorResponse(__('custom.message_not_send'), __('custom.voting_tour_not_found'));
            }

            $data['voting_tour_id'] = $votingTour->id;
        }

        $validator = \Validator::make($data, $rules);

        if ($validator->fails()) {
            return $this->errorResponse(__('custom.add_user_fail'), $validator->errors()->messages());
        }

        try {
            DB::beginTransaction();
            unset($data['password_confirm']);

            $data['password'] = Hash::make($data['password']);

            $user = User::create($data);

            DB::commit();

            return $this->successResponse(['id' => $user->id], true);
        } catch (QueryException $ex) {
            DB::rollback();
            Log::error($ex->getMessage());
        }
    }

    /**
     * Edit user record
     *
     * @param integer id - required
     * @param array data - required
     * @param string data[firs_tname] - optional
     * @param string data[last_name] - optional
     * @param string data[password] - required
     * @param string data[password_confirm] - optional
     * @param string data[org_id] - optional
     *
     * @return json $response - response with status and api key if successful
     */
    public function editUser(Request $request)
    {
        $data = $request->get('data', []);
        $id = $request->get('id', null);

        $rules = [
            'org_id'     => 'nullable',
            'first_name' => 'nullable|string',
            'last_name'  => 'nullable|string',
            'active'     => 'nullable|bool',
        ];

        if (!isset($data['org_id'])) {
            //$rules['email'] = 'required|email|unique:users';
            $rules['first_name'] = 'required|string';
            $rules['last_name'] = 'required|string';
            $rules['active'] = 'required|bool';
        }

        $validator = \Validator::make($data, $rules);

        if (!$validator->fails()) {
            try {
                DB::beginTransaction();

                $user = User::findOrFail($id);
                $user->first_name = $data['first_name'];
                $user->last_name = $data['last_name'];
                $user->active = $data['active'];

                DB::commit();

                return $this->successResponse(['id' => $user->id], true);
            } catch (QueryException $ex) {
                DB::rollback();
                Log::error($ex->getMessage());
                return $this->errorResponse(__('custom.edit_user_fail'), $validator->errors()->messages());
            }
        }

        return $this->errorResponse(__('custom.edit_user_fail'), $validator->errors()->messages());
    }

    /**
     * List all user in specific order.
     *
     * @param string $order_field - required
     * @param string $order_type  - required
     *
     * @return json $response - response with status and collection of user models if successful
     */
    public function list(Request $request)
    {
        $field = $request->get('order_field', null);
        $order = $request->get('order_type', 'ASC');
        $page = $request->get('page_number');
        $request->request->add(['page' => $page]);

        try {
            $users = User::sort($field, $order)->paginate();

            return $this->successResponse(['users' => $users]);
        } catch (QueryException $e) {
            logger()->error($e->getMessage());
            return $this->errorResponse(__('custom.user_not_found'), $e->getMessage());
        }


        return $this->errorResponse(__('custom.user_not_found'));
    }

    /**
     * Get user model by id.
     *
     * @param integer id - required
     *
     * @return json $response - response with status and user model if successful
     */
    public function getData(Request $request)
    {
        $id = $request->get('id', null);

        $validator = \Validator::make(['id' => $id], ['id' => 'required']);
        if ($validator->fails()) {
            return $this->errorResponse(__('custom.user_not_found'), $validator->errors()->messages());
        }

        $user = User::findOrFail($id);
        if ($user) {
            return $this->successResponse(['user' => $user]);
        }

        return $this->errorResponse(__('custom.user_not_found'));
    }
}
