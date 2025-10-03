<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Community extends Model
{
	use HasFactory;

	protected $fillable = [
		'name',
		'slug',
		'avatar_path',
		'category',
		'description',
		'visibility',
		'status',
		'owner_id',
	];

	protected static function booted(): void
	{
		static::creating(function (Community $community) {
			if (empty($community->slug)) {
				$community->slug = Str::slug($community->name . '-' . Str::random(6));
			}
		});
	}

	public function owner()
	{
		return $this->belongsTo(User::class, 'owner_id');
	}

	public function members()
	{
		return $this->belongsToMany(User::class)
			->withTimestamps()
			->withPivot('role');
	}

	public function hashtags()
	{
		return $this->belongsToMany(Hashtag::class, 'community_hashtag')
			->withTimestamps();
	}

	public function engagementMetrics()
	{
		return $this->hasMany(EngagementMetric::class);
	}
}
