<?php namespace App;

use Illuminate\Database\Eloquent\Model as Eloquent;


class Message extends Eloquent {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'messages';

    /**
     * Method for hasMany relationship with MessageReceiver
     * @param void
     * @return array
     */
    public function message_receivers() {
	return $this->hasMany('App\MessageReceiver');
    }
    
    /**
     * To get latest messages in conversation with user
     */
    public static function getLatestMsg($cuserId, $loggedInUserId) {
	$matchThese = ['messages.sent_by' => $loggedInUserId, 'messages.sent_to' => $cuserId, 'messages.sender_deleted' => 0];
        $orThese = ['messages.sent_to' => $loggedInUserId, 'messages.sent_by' => $cuserId, 'messages.receiver_deleted' => 0];
        $arrData = \DB::table('messages')
                ->where(function($query) use ($matchThese, $orThese) {
                    $query->where($matchThese);
                    $query->orWhere($orThese);
                })
		->orderBy('id', 'DESC')->first();
        return (!empty($arrData->message)) ? $arrData->message : '';
    }
}
