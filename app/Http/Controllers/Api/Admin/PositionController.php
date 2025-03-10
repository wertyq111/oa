<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Api\FormRequest;
use App\Http\Resources\BaseResource;
use App\Models\Web\Position;
use Spatie\QueryBuilder\QueryBuilder;

class PositionController extends Controller
{
    /**
     * 列表
     *
     * @param FormRequest $request
     * @param Position $position
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @author zhouxufeng <zxf@netsun.com>
     * @date 2025/2/5 16:04
     */
    public function index(FormRequest $request, Position $position)
    {
        // 生成允许过滤字段数组
        $allowedFilters = $request->generateAllowedFilters($position->getRequestFilters());

        $config = [
            'allowedFilters' => $allowedFilters,
            'orderBy' => [['sort' => 'asc']]
        ];
        $positions = $this->queryBuilder(Position::class, true, $config);

        $list = BaseResource::collection($positions);
        return $list;
    }

    /**
     * 详情
     *
     * @param Position $position
     * @return BaseResource
     * @author zhouxufeng <zxf@netsun.com>
     * @date 2025/01/21 13:28
     */
    public function info(Position $position)
    {
        $position = QueryBuilder::for(Position::class)->findOrFail($position->id);

        $info = $position->toArray();

        return new BaseResource($info);
    }

    /**
     * 菜单列表
     *
     * @param FormRequest $request
     * @param Position $position
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @author zhouxufeng <zxf@netsun.com>
     * @date 2025/1/21 14:04
     */
    public function list(FormRequest $request, Position $position)
    {
        $positions = QueryBuilder::for(Position::class)->paginate();

        return BaseResource::collection($positions);
    }

    /**
     * 添加菜单
     *
     * @param FormRequest $request
     * @param Position $position
     * @return BaseResource
     * @author zhouxufeng <zxf@netsun.com>
     * @date 2025/01/21 13:07
     */
    public function add(FormRequest $request, Position $position)
    {
        $data = $request->all();
        try {
            $position->fill($data);
            $position->edit();
        } catch (\Exception $e) {
            throw $e;
        }


        return new BaseResource($position);

    }

    /**
     * 编辑菜单
     *
     * @param Position $position
     * @param FormRequest $request
     * @return BaseResource
     * @author zhouxufeng <zxf@netsun.com>
     * @date 2025/01/21 13:08
     */
    public function edit(Position $position, FormRequest $request)
    {
        $data = $request->all();

        try {
            $position->fill($data);
            $position->edit();
        } catch (\Exception $e) {
            throw $e;
        }

        return new BaseResource($position);
    }

    /**
     * 删除菜单
     *
     * @param Position $position
     * @return \Illuminate\Http\JsonResponse
     * @author zhouxufeng <zxf@netsun.com>
     * @date 2025/01/21 13:23
     */
    public function delete(Position $position)
    {
        // 批量删除子级
        $this->service->batchDeleteChildren($position->children);

        $position->delete();

        return response()->json([]);
    }

    /**
     * 获取全部
     *
     * @return BaseResource
     * @author zhouxufeng <zxf@netsun.com>
     * @date 2025/01/21 14:30
     */
    public function getPositionList()
    {
        $positions = QueryBuilder::for(Position::class)
            ->orderBy('sort', 'ASC')
            ->get();

        return new BaseResource($positions);
    }
}
