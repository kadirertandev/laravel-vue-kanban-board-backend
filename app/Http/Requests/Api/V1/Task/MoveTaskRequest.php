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
      'toColumn' => [
        'required',
        'exists:columns,id',
        function ($attribute, $value, $fail) {
          #check if toColumn belongs to user
          $ownsColumn = \App\Models\Column::where("id", $value)
            ->whereHas("board", fn($q) => $q->where("user_id", $this->user()->id))
            ->exists();

          if (!$ownsColumn)
            abort(403, "This action is unauthorized");
        }
      ],
      'position' => ['required', 'numeric'],
    ];
  }

  protected function prepareForValidation(): void
  {
    if ($this->has("position")) {
      $this->merge([
        'position' => round($this->position, 5)
      ]);
    }
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
