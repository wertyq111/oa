<?php


namespace App\Http\Controllers\Api\Data;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Api\Data\CityRequest;
use App\Http\Requests\Api\FormRequest;
use App\Http\Resources\BaseResource;
use App\Models\Web\City;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * 城市-控制器
 * @author vwms@netsun.com
 * @date: 2022/10/30 12:14
 */
class CityController extends Controller
{
    /**
     * @param FormRequest $request
     * @param City $city
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @author zhouxufeng <zxf@netsun.com>
     * @date 2025/1/22 10:26
     */
    public function index(FormRequest $request, City $city)
    {
//        // 生成允许过滤字段数组
//        $allowedFilters = $request->generateAllowedFilters($city->getRequestFilters());
//
//        $cities = QueryBuilder::for(City::class)
//            ->allowedFilters($allowedFilters)->orderBy('sort')->get()->toArray();
//
//
//        return new BaseResource($cities);

        // 生成允许过滤字段数组
        $allowedFilters = $request->generateAllowedFilters($city->getRequestFilters());

        $config = [
            'allowedFilters' => $allowedFilters,
            'orderBy' => [['sort' => 'asc']]
        ];
        $members = $this->queryBuilder(City::class, false, $config);

        $list = BaseResource::collection($members);
        return $list;
    }

    /**
     * 详情
     *
     * @param City $city
     * @return BaseResource
     * @author zhouxufeng <zxf@netsun.com>
     * @date 2025/01/21 13:28
     */
    public function info(City $city)
    {
        $city = QueryBuilder::for(City::class)->findOrFail($city->id);

        $info = $city->toArray();
        if($info['pid'] > 0 &&  $city->children) {
            $info['checkedList'] = array_column($city->children->toArray(), 'sort');
        }

        return new BaseResource($info);
    }

    /**
     * 菜单列表
     *
     * @param FormRequest $request
     * @param City $city
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @author zhouxufeng <zxf@netsun.com>
     * @date 2025/1/21 14:04
     */
    public function list(FormRequest $request, City $city)
    {
        $cities = QueryBuilder::for(City::class)->paginate();

        return BaseResource::collection($cities);
    }

    /**
     * 添加菜单
     *
     * @param CityRequest $request
     * @param City $city
     * @return BaseResource
     * @author zhouxufeng <zxf@netsun.com>
     * @date 2025/01/21 13:07
     */
    public function add(CityRequest $request, City $city)
    {
        $data = $request->getSnakeRequest();
        try {
            $city->fill($data);
            $city->edit();
        } catch (\Exception $e) {
            throw $e;
        }


        return new BaseResource($city);

    }

    /**
     * 编辑菜单
     *
     * @param City $city
     * @param CityRequest $request
     * @return BaseResource
     * @author zhouxufeng <zxf@netsun.com>
     * @date 2025/01/21 13:08
     */
    public function edit(City $city, CityRequest $request)
    {
        $data = $request->getSnakeRequest();

        try {
            $city->fill($data);
            $city->edit();
        } catch (\Exception $e) {
            throw $e;
        }

        return new BaseResource($city);
    }

    /**
     * 删除菜单
     *
     * @param City $city
     * @return \Illuminate\Http\JsonResponse
     * @author zhouxufeng <zxf@netsun.com>
     * @date 2025/01/21 13:23
     */
    public function delete(City $city)
    {
        // 批量删除子级
        $this->service->batchDeleteChildren($city->children);

        $city->delete();

        return response()->json([]);
    }

    /**
     * 获取全部菜单
     *
     * @return BaseResource
     * @author zhouxufeng <zxf@netsun.com>
     * @date 2025/01/21 14:30
     */
    public function getCityList()
    {
        $citys = QueryBuilder::for(City::class)
            ->where(['pid' => 0])
            ->with(['CityChildren'])
            ->orderBy('sort', 'ASC')
            ->get();

        // CityChildren替换成 children
        $citys = $this->service->convertChildrenKey($citys);

        return new BaseResource($citys);
    }
}
