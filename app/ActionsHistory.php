<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\MetaData;

class ActionsHistory extends Model
{
    use MetaData;

    /*
     * User action types
     */
    const TYPE_SEE = 1;
    const TYPE_ADD = 2;
    const TYPE_MOD = 3;
    const TYPE_VOTED = 4;
    const TYPE_LOGGED_IN = 5;
    const TYPE_DOWNLOADED = 6;
    const TYPE_CHANGED_PASSWORD = 7;
    const TYPE_RANKED = 8;
    const TYPE_CANCELLED_TOUR = 9;
    const TYPE_IMPORT_SUCCESS = 10;
    const TYPE_IMPORT_FAILURE = 11;

    /*
     * Modules
     */
    const ORGANISATIONS = 1;
    const VOTING_TOURS = 2;
    const USERS = 3;
    const FILES_MESSAGE = 4;
    const FILES_REGISTRATION = 5;
    const MESSAGES = 6;
    const VOTES = 7;
    const ORGANISATION_MESSAGES = 8;
    const ORGANISATIONS_FILES = 9;
    const IMPORTS = 10;

    const DEFAULT_ORDER_FIELD = 'id';
    const DEFAULT_ORDER_TYPE = 'DESC';

    const ALLOWED_ORDER_FIELDS = [
        'occurrence',
        'user_id',
        'username',
        'action',
        'module',
        'object',
        'voting_tour_id',
        'ip_address',
    ];
    const ALLOWED_ORDER_TYPES = ['ASC', 'DESC'];

    protected $table = 'actions_history';

    protected $perPage = 15;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['actor'];

    public $timestamps = false;

    /**
     * Get the types of action
     *
     * @return array of actions
     */
    public static function getActions()
    {
        return [
            self::TYPE_SEE              => __('custom.saw'),
            self::TYPE_ADD              => __('custom.added'),
            self::TYPE_MOD              => __('custom.modified'),
            self::TYPE_VOTED            => __('custom.voted_action'),
            self::TYPE_LOGGED_IN        => __('custom.logged_in'),
            self::TYPE_DOWNLOADED       => __('custom.downloaded'),
            self::TYPE_CHANGED_PASSWORD => __('custom.changed_password'),
            self::TYPE_RANKED           => __('custom.ranked'),
            self::TYPE_CANCELLED_TOUR   => __('custom.cancelled_tour'),
            self::TYPE_IMPORT_SUCCESS   => __('custom.import_success'),
            self::TYPE_IMPORT_FAILURE   => __('custom.import_failure'),
        ];
    }

    /**
     * Get the available modules
     *
     * @return array of modules
     */
    public static function getModules()
    {
        return [
            self::ORGANISATIONS         => __('custom.organisations'),
            self::VOTING_TOURS          => __('custom.voting_tours'),
            self::USERS                 => __('custom.users'),
            self::FILES_MESSAGE         => __('custom.file_to_message'),
            self::FILES_REGISTRATION    => __('custom.file_to_reg'),
            self::MESSAGES              => __('custom.messages'),
            self::VOTES                 => __('custom.votingmenu'),
            self::ORGANISATION_MESSAGES => __('custom.organisation_messages'),
            self::ORGANISATIONS_FILES   => __('custom.organisation_files'),
            self::IMPORTS               => __('custom.imports'),
        ];
    }

    /**
     * Record action history by module and action for a logged user
     *
     * @param integer module - coming from getModules (required)
     * @param integer action - action done upon the object (required)
     *
     * @return void
     */
    public static function add($request)
    {
        $actions = ActionsHistory::getActions();

        $validator = \Validator::make($request, [
            'module' => 'required|int',
            'action' => 'required|int|digits_between:1,3|in:'. implode(',', array_flip($actions)),
            'object' => 'nullable|int',
            'actor'  => 'nullable|int|exists:users,id'
        ]);

        $object = isset($request['object']) ? $request['object'] : null;
        $ip = request()->ip();


        if (isset($request['actor'])) {
            $actor = $request['actor'];
        } else {
            $userId = isset(\Auth::guard('backend')->user()->id) ? \Auth::guard('backend')->user()->id : null;

            if (!$userId) {
                $userId = isset(\Auth::user()->id) ? \Auth::user()->id : null;
            }

            if ($userId) {
                $actor = $userId;
            } else {
                $actor = null;
            }
        }

        $tour = VotingTour::getLatestTour() ? VotingTour::getLatestTour()->id : null;

        if (!$validator->fails()) {
            try {
                $dbData = [
                    'user_id'        => $actor,
                    'action'         => $request['action'],
                    'module'         => $request['module'],
                    'object'         => $object,
                    'voting_tour_id' => $tour,
                    'occurrence'     => date('Y-m-d H:i:s'),
                    'ip_address'     => !empty($ip) ? $ip : 'N/A',
                ];

                ActionsHistory::create($dbData);
            } catch (QueryException $ex) {
                logger()->error($ex);
            }
        }
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
