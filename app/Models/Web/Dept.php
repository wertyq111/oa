<?php

namespace App\Models\Web;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Dept extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'fullname',
        'type',
        'pid',
        'sort',
        'note'
    ];

    /**
     * 过滤参数配置
     *
     * @var array[]
     */
    protected $requestFilters = [
        'name' => ['column' => 'name']
    ];

    /**
     * 一对多关联
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @author zhouxufeng <zxf@netsun.com>
     * @date 2023/6/12 10:51
     */
    public function child()
    {
        return $this->hasMany(self::class,'pid');
    }

    /**
     * 递归子级
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @author zhouxufeng <zxf@netsun.com>
     * @date 2024/3/7 17:07
     */
    public function children()
    {
        return $this->child()->with('children');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @author zhouxufeng <zxf@netsun.com>
     * @date 2024/3/7 17:07
     */
    public function parent()
    {
        return $this->hasMany(self::class,'id','pid');
    }

    /**
     * 递归父级
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @author zhouxufeng <zxf@netsun.com>
     * @date 2024/3/7 17:07
     */
    public function parents()
    {
        return $this->parent()->with('parents');
    }

}
