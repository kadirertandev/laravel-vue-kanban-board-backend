<?php

namespace App\Http\Requests\Api\V1\Task;

use Illuminate\Foundation\Http\FormRequest;

class MoveTaskRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
    return [
      'fromColumn' => ['required', 'exists:columns,id'],
      'toColumn' => ['required', 'exists:columns,id'],
      'position' => ['required', 'numeric'],
    ];
  }

  protected function prepareForValidation(): void
  {
    $this->merge([
      'position' => round($this->position, 5)
    ]);
  }

  public function validated($key = null, $default = null)
  {
    $data = parent::validated($key, $default);

    return collect($data)
      ->only("position")
      ->prepend($data["toColumn"], "column_id")
      ->all();
  }
}
