<?php

namespace Demo\SendPush\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PushMessageRequest extends FormRequest {
	public function authorize() {
		return TRUE;
	}

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
       		'title' => ['required', 'max:255'],
       		'body' => ['required'],
        ];
    }
}
