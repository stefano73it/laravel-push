<?php

namespace Demo\SendPush;

use Illuminate\Database\Eloquent\Model;

class PushMessage extends Model {
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'sendpush_messages';

	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var bool
	 */
	public $timestamps = TRUE;
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'title', 'body', 'callback_url',
	];
}
