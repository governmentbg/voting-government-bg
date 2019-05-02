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
    public function generatePasswordHash(Request $request)
    {
        $username = $request->get('username');
        $email = $request->get('email');

        $rules = [
            'username' => 'required|string',
            'email'    => 'required|email',
        ];

        $validator = \Validator::make(['username' => $username, 'email' => $email], $rules);

        if ($validator->fails()) {
            return $this->errorResponse(__('custom.password_hash_generation_fail'), $validator->errors()->messages());
        }

        try {
            $password = hash_hmac('sha256', str_random(40), config('app.key'));
            $user = User::where('username', $username)->where('email', $email)->first();
            if (!$user) {
                $user = User::where('username', $username)->whereHas('organisation', function ($query) use ($email) {
                    $query->where('email', $email);
                })->first();
            }

            if ($user) {
                $user->update(['pw_reset_hash' => $password]);
            } else {
                return $this->errorResponse(__('custom.user_not_found'));
            }
        } catch (\Exception $e) {
            logger()->error($e->getMessage());
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
     * Change user password.
     *
     * @param int user_id - required
     * @param string new_password - required
     * @param string password - required
     *
     * @return json $response - response with status and user id if successful
     */
    public function changePassword(Request $request)
    {
        $data = $request->only('new_password', 'user_id', 'password');

        $rules = [
            'user_id'      => 'required',
            'password'     => 'required|min:6',
            'new_password' => 'required|min:6',
        ];

        $validator = \Validator::make($data, $rules);

        if ($validator->fails()) {
            return $this->errorResponse(__('custom.change_password_error'), $validator->errors()->messages());
        }

        $user = User::where('id', $data['user_id'])->first();

        if ($user) {
            try {
                if (Hash::check($data['password'], $user->password)) {
                    $user->update(['password' => Hash::make($data['new_password'])]);
                } else {
                    return $this->errorResponse(__('custom.incorrect_password'));
                }

                return $this->successResponse();
            } catch (QueryException $e) {
                logger()->error($e->getMessage());
                return $this->errorResponse(__('custom.database_error'));
            }
        } else {
            return $this->errorResponse(__('custom.user_not_found'));
        }

        return $this->errorResponse(__('custom.change_password_error'));
    }

    /**
     * Add new user record
     *
     * @param array user_data - required
     * @param int user_data[org_id] - optional
     * @param string user_data[username] - required
     * @param string user_data[password] - required
     * @param string user_data[password_confirm] - required
     * @param string user_data[firs_tname] - required without org_id
     * @param string user_data[last_name] - required without org_id
     * @param string user_data[email] - required without org_id
     * @param string user_data[active] - optional
     *
     * @return json $response - response with status and api key if successful
     */
    public function add(Request $request)
    {
        $data = $request->get('user_data', []);

        $rules = [
            'org_id'           => 'nullable|int|exists:organisations,id',
            'username'         => 'required|string|max:255',
            'password'         => 'required|string|min:6|max:255',
            'password_confirm' => 'required|string|same:password',
            'first_name'       => 'nullable|string|max:255',
            'last_name'        => 'nullable|string|max:255',
            'email'            => '',
            'active'           => 'nullable|bool',
        ];

        if (!isset($data['org_id'])) {
            $rules['username'] .= '|unique:users';
            $rules['first_name'] = 'required|string|max:255';
            $rules['last_name'] = 'required|string|max:255';
            $rules['email'] = 'required|email|unique:users';

            if (!isset($data['active'])) {
                $data['active'] = User::ACTIVE_FALSE;
            }
        } else {
            $votingTour = VotingTour::getLatestTour();
            if (!$votingTour) {
                return $this->errorResponse(__('custom.add_user_fail'), __('custom.voting_tour_not_found'));
            }

            $rules['username'] .= '|unique:users,username,NULL,id,voting_tour_id,'. $votingTour->id;

            $data['voting_tour_id'] = $votingTour->id;
            if (isset($data['email'])) {
                unset($data['email']);
            }
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
            return $this->errorResponse(__('custom.add_user_fail'), $ex->getMessage());
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
    public function edit(Request $request)
    {
        $data = $request->get('user_data', []);
        $id = $request->get('user_id', null);

        $data = array_intersect_key($data, array_flip(User::EDITABLE_FIELDS));

        if (isset($id) && !empty($id)) {
            $user = User::where('id', $id)->whereNull('org_id')->first();
            if ($user && isset($data['email']) && $user->email == $data['email']) {
                unset($data['email']); //email is not changed
            }
        }

        $data['user_id'] = $id;

        $rules['email'] = 'sometimes|required|email|unique:users';
        $rules['first_name'] = 'sometimes|required|string';
        $rules['last_name'] = 'sometimes|required|string';
        $rules['active'] = 'sometimes|required|bool';
        $rules['user_id'] = 'required';

        $validator = \Validator::make($data, $rules);

        if (!$validator->fails()) {
            try {
                DB::beginTransaction();

                if ($user) {
                    unset($data['user_id']);

                    $user->update($data);
                } else {
                    return $this->errorResponse(__('custom.user_not_found'));
                }

                DB::commit();

                return $this->successResponse();
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
            $users = User::whereNull('org_id')->sort($field, $order)->paginate();

            return $this->successResponse($users);
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
        $id = $request->get('user_id', null);

        $validator = \Validator::make(['id' => $id], ['id' => 'required']);
        if ($validator->fails()) {
            return $this->errorResponse(__('custom.user_not_found'), $validator->errors()->messages());
        }

        $user = User::where('id', $id)->first();
        if ($user) {
            return $this->successResponse($user);
        }

        return $this->errorResponse(__('custom.user_not_found'));
    }
}
