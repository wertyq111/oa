<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Api\Admin\MenuRequest;
use App\Http\Requests\Api\FormRequest;
use App\Http\Resources\BaseResource;
use App\Models\Permission\Menu;
use App\Services\Api\MenuService;
use Spatie\QueryBuilder\QueryBuilder;

class MenuController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->service = new MenuService();
    }


    /**
     * 菜单列表 - 不分页
     *
     * @param FormRequest $request
     * @param Menu $menu
     * @return BaseResource
     * @author zhouxufeng <zxf@netsun.com>
     * @date 2025/01/21 09:52
     */
    public function index(FormRequest $request, Menu $menu)
    {
        // 生成允许过滤字段数组
        $allowedFilters = $request->generateAllowedFilters($menu->getRequestFilters());

        $menus = QueryBuilder::for(Menu::class)
            ->allowedFilters($allowedFilters)->orderBy('sort')->get()->toArray();

        return new BaseResource($menus);
    }

    /**
     * 菜单详情
     *
     * @param Menu $menu
     * @return BaseResource
     * @author zhouxufeng <zxf@netsun.com>
     * @date 2025/01/21 13:28
     */
    public function info(Menu $menu)
    {
        $menu = QueryBuilder::for(Menu::class)->findOrFail($menu->id);

        $info = $menu->toArray();
        if($info['pid'] > 0 &&  $menu->children) {
            $info['checkedList'] = array_column($menu->children->toArray(), 'sort');
        }

        return new BaseResource($info);
    }

    /**
     * 菜单列表
     *
     * @param FormRequest $request
     * @param Menu $menu
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @author zhouxufeng <zxf@netsun.com>
     * @date 2025/1/21 14:04
     */
    public function list(FormRequest $request, Menu $menu)
    {
        $menus = QueryBuilder::for(Menu::class)->paginate();

        return BaseResource::collection($menus);
    }

    /**
     * 添加菜单
     *
     * @param MenuRequest $request
     * @param Menu $menu
     * @return BaseResource
     * @author zhouxufeng <zxf@netsun.com>
     * @date 2025/01/21 13:07
     */
    public function add(MenuRequest $request, Menu $menu)
    {
        $data = $request->all();
        try {
            $menu->fill($data);
            $menu->edit();
        } catch (\Exception $e) {
            throw $e;
        }


        return new BaseResource($menu);

    }

    /**
     * 编辑菜单
     *
     * @param Menu $menu
     * @param FormRequest $request
     * @return BaseResource
     * @author zhouxufeng <zxf@netsun.com>
     * @date 2025/01/21 13:08
     */
    public function edit(Menu $menu, FormRequest $request)
    {
        $data = $request->all();

        try {
            $menu->fill($data);
            $menu->edit();
        } catch (\Exception $e) {
            throw $e;
        }

        return new BaseResource($menu);
    }

    /**
     * 删除菜单
     *
     * @param Menu $menu
     * @return \Illuminate\Http\JsonResponse
     * @author zhouxufeng <zxf@netsun.com>
     * @date 2025/01/21 13:23
     */
    public function delete(Menu $menu)
    {
        // 批量删除子级
        $this->service->batchDeleteChildren($menu->children);

        $menu->delete();

        return response()->json([]);
    }

    /**
     * 获取全部菜单
     *
     * @return BaseResource
     * @author zhouxufeng <zxf@netsun.com>
     * @date 2025/01/21 14:30
     */
    public function getMenuList()
    {
        $menus = QueryBuilder::for(Menu::class)
            ->where(['pid' => 0])
            ->with(['menuChildren'])
            ->orderBy('sort', 'ASC')
            ->get();

        // menuChildren替换成 children
        $menus = $this->service->convertChildrenKey($menus, 'menu');

        return new BaseResource($menus);
    }
}
