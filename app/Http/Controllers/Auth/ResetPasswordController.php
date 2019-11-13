<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use App\Http\Controllers\Api\UserController;
use App\User;
use App\VotingTour;
use App\Http\Controllers\Api\VotingTourController as ApiVotingTour;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    const PASSWORD_CHANGED = 'passwords.changed';

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'changePassword']);
    }

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $token
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.password_reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function reset(Request $request)
    {
        $this->validate($request, $this->rules(), $this->validationErrorMessages());

        //Reset password
        list($result, $errors) = api_result(UserController::class, 'resetPassword', [
            'hash' => $request->get('token'),
            'new_password' => $request->get('password'),
            ], 'id');

        if(!empty($errors)){
            return back()->withErrors((array)$errors);
        }

        $user = User::where('id' , $result)->first();
        if($user && $user->isAdmin()){
            $this->redirectTo = 'admin/';
        }

        return $this->sendResetResponse(Password::PASSWORD_RESET);
    }

    /**
     * Get the password reset validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'token' => 'required',
            //'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ];
    }

    public function changePassword(Request $request)
    {
        if ($request->has('save')) {
            $user = auth()->user();
            $password = $request->get('password');
            $newPassword = $request->get('new_password');

            $data = [
                'user_id' => $user->id,
                'password' => $password,
                'new_password' => $newPassword,
            ];

            $rules = [
                'new_password' => 'confirmed'
            ];

            $validator = \Validator::make(array_merge($data, ['new_password_confirmation' => $request->get('new_password_confirmation')]), $rules);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator);
            }

            list($result, $errors) = api_result(UserController::class, 'changePassword', $data);

            if(!empty($errors)){
                return redirect()->back()->withErrors((array)$errors);
            }

            session()->flash('alert-success', trans(self::PASSWORD_CHANGED));
            return redirect()->back();
        }

        list($votingData, $tourErrors) = api_result(ApiVotingTour::class, 'getLatestVotingTour');

        if ($votingData) {
            $votingData->statusName = VotingTour::getStatuses()[$votingData->status];
            $votingData->showTick = ($votingData->status != VotingTour::STATUS_FINISHED) ? true: false;
        }

        return view('auth.password_change', ['votingTourData' => $votingData]);
    }
}
