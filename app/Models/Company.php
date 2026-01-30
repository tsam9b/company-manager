<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
	use HasFactory;

	protected $table = 'company';

	protected $fillable = [
		'name',
		'email',
		'logo',
		'website'
	];

	public static function rules(): array
	{
		return [
			'name' => ['required', 'string', 'max:255'],
			'email' => ['nullable', 'email', 'max:255'],
			'logo' => ['nullable', 'image', 'dimensions:min_width=100,min_height=100', 'max:2048'],
			'website' => ['nullable', 'url', 'max:255']
		];
	}

	public function getEmployees(): HasMany
	{
		return $this->hasMany(Employee::class, 'company_id');
	}
}