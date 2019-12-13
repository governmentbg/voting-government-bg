<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\MetaData;

class ActionsHistory extends Model
{
    use MetaData;

    protected $table = 'actions_history';
    public $timestamps = false;
    protected $guarded = ['id'];
    protected $hidden = ['actor'];

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
            if (isset(\Auth::user()->id)) {
                $actor = \Auth::user()->id;
            } else {
                $actor = 1;
            }
        }
        $tour = VotingTour::getLatestTour();
        if (!$validator->fails()) {
            if (!empty($tour)) {
                try {
                    $dbData = [
                        'user_id'        => $actor,
                        'action'         => $request['action'],
                        'module'         => $request['module'],
                        'object'         => $object,
                        'voting_tour_id' => $tour->id,
                        'occurrence'     => date('Y-m-d H:i:s'),
                        'ip_address'     => !empty($ip) ? $ip : 'N/A',
                    ];

                    ActionsHistory::create($dbData);
                } catch (QueryException $ex) {
                    logger()->error($ex);
                }
            }
        }
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
