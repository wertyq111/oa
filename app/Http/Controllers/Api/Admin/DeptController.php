<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Api\FormRequest;
use App\Http\Resources\BaseResource;
use App\Models\Web\Dept;
use Spatie\QueryBuilder\QueryBuilder;

class DeptController extends Controller
{
    /**
     * 列表 - 不分页
     *
     * @param FormRequest $request
     * @param Dept $dept
     * @return BaseResource
     * @author zhouxufeng <zxf@netsun.com>
     * @date 2025/01/21 09:52
     */
    public function index(FormRequest $request, Dept $dept)
    {
        // 生成允许过滤字段数组
        $allowedFilters = $request->generateAllowedFilters($dept->getRequestFilters());

        $depts = QueryBuilder::for(Dept::class)
            ->allowedFilters($allowedFilters)->orderBy('sort')->get()->toArray();

        return new BaseResource($depts);
    }

    /**
     * 详情
     *
     * @param Dept $dept
     * @return BaseResource
     * @author zhouxufeng <zxf@netsun.com>
     * @date 2025/01/21 13:28
     */
    public function info(Dept $dept)
    {
        $dept = QueryBuilder::for(Dept::class)->findOrFail($dept->id);

        $info = $dept->toArray();

        return new BaseResource($info);
    }

    /**
     * 菜单列表
     *
     * @param FormRequest $request
     * @param Dept $dept
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @author zhouxufeng <zxf@netsun.com>
     * @date 2025/1/21 14:04
     */
    public function list(FormRequest $request, Dept $dept)
    {
        $depts = QueryBuilder::for(Dept::class)->paginate();

        return BaseResource::collection($depts);
    }

    /**
     * 添加菜单
     *
     * @param FormRequest $request
     * @param Dept $dept
     * @return BaseResource
     * @author zhouxufeng <zxf@netsun.com>
     * @date 2025/01/21 13:07
     */
    public function add(FormRequest $request, Dept $dept)
    {
        $data = $request->all();
        try {
            $dept->fill($data);
            $dept->edit();
        } catch (\Exception $e) {
            throw $e;
        }


        return new BaseResource($dept);

    }

    /**
     * 编辑菜单
     *
     * @param Dept $dept
     * @param FormRequest $request
     * @return BaseResource
     * @author zhouxufeng <zxf@netsun.com>
     * @date 2025/01/21 13:08
     */
    public function edit(Dept $dept, FormRequest $request)
    {
        $data = $request->all();

        try {
            $dept->fill($data);
            $dept->edit();
        } catch (\Exception $e) {
            throw $e;
        }

        return new BaseResource($dept);
    }

    /**
     * 删除菜单
     *
     * @param Dept $dept
     * @return \Illuminate\Http\JsonResponse
     * @author zhouxufeng <zxf@netsun.com>
     * @date 2025/01/21 13:23
     */
    public function delete(Dept $dept)
    {
        // 批量删除子级
        $this->service->batchDeleteChildren($dept->children);

        $dept->delete();

        return response()->json([]);
    }

    /**
     * 获取全部
     *
     * @return BaseResource
     * @author zhouxufeng <zxf@netsun.com>
     * @date 2025/01/21 14:30
     */
    public function getDeptList()
    {
        $depts = QueryBuilder::for(Dept::class)
            ->where(['pid' => 0])
            ->orderBy('sort', 'ASC')
            ->get();

        // DeptChildren替换成 children
        $depts = $this->service->convertChildrenKey($depts);

        return new BaseResource($depts);
    }
}
