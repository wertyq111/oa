<?php

namespace App\Models\User;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Member extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'member_level',
        'dept_id',
        'position_id',
        'realname',
        'nickname',
        'gender',
        'avatar',
        'birthday',
        'province_code',
        'city_code',
        'district_code',
        'address',
        'intro',
        'signature',
        'admire',
        'device',
        'source',
        'status',
        'app_version',
        'code',
        'login_ip',
        'login_at',
        'login_region',
        'login_count'
    ];

    /**
     * 过滤参数配置
     *
     * @var array[]
     */
    protected $requestFilters = [
        'username' => ['column' => 'user.username'],
        'gender' => ['column' => 'gender', 'filterType' => 'exact'],
        'nickname' => ['column' => 'nickname']
    ];

    /**
     *  一对一关联(反向)
     *  @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
