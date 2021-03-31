<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Duration implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    protected $request;
    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $data = $this->request->validate([
            'duration' => 'required|integer',
            ]);
        return $data['duration']%30 == 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '所要時間は30分単位で入力してください';
    }
}
