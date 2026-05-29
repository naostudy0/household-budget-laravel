<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use App\Models\User;
use Database\Factories\AccountFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable(['account_uuid', 'user_id', 'name'])]
class Account extends Model
{
    /** @use HasFactory<AccountFactory> */
    use HasFactory;

    protected $primaryKey = 'account_id';

    // Infrastructure 配下の Model は Laravel の Factory 推測規約から外れるため、明示しておく。
    protected static function newFactory(): Factory
    {
        return AccountFactory::new();
    }

    public function getRouteKeyName(): string
    {
        return 'account_uuid';
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    protected static function booted(): void
    {
        static::creating(function (Account $account) {
            if ($account->account_uuid === null || $account->account_uuid === '') {
                $account->account_uuid = (string) Str::uuid();
            }
        });
    }
}
