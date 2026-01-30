<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
	use HasFactory;

	protected $table = 'employee';

	protected $fillable = [
		'first_name',
		'last_name',
		'company_id',
		'email',
		'phone',
	];

	public static function rules(): array
	{
		return [
			'first_name' => ['required', 'string', 'max:255'],
			'last_name' => ['required', 'string', 'max:255'],
			'company_id' => ['required', 'integer', 'exists:company,id'],
			'email' => ['nullable', 'email', 'max:255'],
			'phone' => ['nullable', 'string', 'max:255'],
		];
	}

	public function getCompany(): BelongsTo
	{
		return $this->belongsTo(Company::class, 'company_id');
	}
}
