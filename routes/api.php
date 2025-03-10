<?php

use App\Http\Controllers\Api\Admin\MenuController;
use App\Http\Controllers\Api\WeatherController;
use App\Http\Controllers\Api\Admin\RoleController;
use App\Http\Controllers\Api\CaptchasController;
use App\Http\Controllers\Api\QiNiuController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\User\UsersController;
use App\Http\Controllers\Api\User\MembersController;
use App\Http\Controllers\Api\User\MemberLevelController;
use App\Http\Controllers\Api\User\VerificationCodesController;
use App\Http\Controllers\Api\AuthorizationsController;
use App\Http\Controllers\Api\Data\CityController;
use App\Http\Controllers\Api\Admin\DeptController;
use App\Http\Controllers\Api\Admin\PositionController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User\Member;
use App\Models\User\MemberLevel;
use App\Models\Permission\Role;
use App\Models\Permission\Menu;
use App\Models\Web\Dept;
use App\Models\Web\Position;
use App\Models\Web\City;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::name('api')->group(function () {
    // 登录类路由组
    //Route::middleware('throttle:'. config('api.rate_limits.sign'))->group(function () {
    // 获取验证码
    Route::post('verification-code/send', [VerificationCodesController::class, 'send'])
        ->name('verification-code.send');

    // socials 第三方登录
    Route::post('socials/{social_type}/authorizations', [AuthorizationsController::class, 'socialStore'])
        ->where('social_type', 'wechat')
        ->name('socials.authorizations.store');

    // easywechat 第三方登录
    Route::post('easywechat/{type}/authorizations', [AuthorizationsController::class, 'easywechatStore'])
        ->where('type', 'mini_program')
        ->name('easywechat.authorizations.store');


    // 用户注册
    Route::post('user/register', [AuthorizationsController::class, 'register'])->name('user.register');

    // 忘记密码
    Route::post('user/forget', [AuthorizationsController::class, 'forget'])->name('user.forget');

    // 查询账号
    Route::get('user/query-username', [AuthorizationsController::class, 'queryUsername'])->name('user.query-username');

    // 用户登录
    Route::post('user/login', [AuthorizationsController::class, 'login'])->name('user.login');

    // 刷新登录
    Route::get('user/refresh', [AuthorizationsController::class, 'refresh'])->name('user.refresh');

    // 用户退出登录
    Route::delete('user/logout', [AuthorizationsController::class, 'logout'])->name('user.logout');

    //});

    // 访问类路由组 - 限制访问次数
    Route::middleware('throttle:' . config('api.rate_limits.access'))->group(function () {
        // 天气信息
        Route::get('weather', [WeatherController::class, 'index'])->name('weather.index');

        // 后台功能组 - 登录后才能访问的接口 - 验证 token 后会刷新 token 前端需要从响应 Header 中找到新的 token 进行替换
        Route::middleware('auth:api')->middleware('refresh.token')->group(function () {
            /** 用户接口开始 */
            // 获取用户信息
            Route::get('users/getUserInfo', [UsersController::class, 'getUserInfo'])->name('users.getUserInfo');
            // 用户列表
            Route::get('users/list', [UsersController::class, 'index'])->name('users.index');
            // 验证用户
            Route::get('users/checkUser', [UsersController::class, 'checkUser'])->name('users.checkUser');
            // 创建用户
            Route::post('users/add', [UsersController::class, 'add'])->name('users.add');
            // 重置密码
            Route::post('users/resetPwd/{user}', [UsersController::class, 'resetPwd'])->name('users.resetPwd');
            // 修改用户
            Route::post('users/status/{user}', [UsersController::class, 'status'])->name('users.status');
            // 修改用户
            Route::post('users/{user}', [UsersController::class, 'edit'])->name('users.edit');
            // 删除用户
            Route::delete('users/{user}', [UsersController::class, 'delete'])->name('users.delete');
            /** 用户接口结束 */

            /** 员工接口开始 */
            // 员工信息列表
            Route::get('members/index', [MembersController::class, 'index'])->name('members.index')
                ->middleware('filter.process:' . Member::class);
            // 员工信息
            Route::get('members/info', [MembersController::class, 'info'])->name('members.info');
            // 当前员工信息
            Route::get('members/user', [MembersController::class, 'user'])->name('members.user');
            // 添加员工信息
            Route::post('members/add', [MembersController::class, 'add'])->name('members.add');
            // 修改登录用户信息
            Route::post('members/editUser', [MembersController::class, 'editUser'])->name('members.editUser');
            // 修改状态
            Route::post('members/status/{member}', [MembersController::class, 'status'])->name('members.status');
            // 修改员工信息
            Route::post('members/{member}', [MembersController::class, 'edit'])->name('members.edit');
            // 删除员工信息
            Route::delete('members/{member}', [MembersController::class, 'delete'])->name('members.delete');

            // 员工等级列表
            Route::get('member-level/index', [MemberLevelController::class, 'index'])->name('member-level.index')
                ->middleware('filter.process:' . MemberLevel::class);
            // 员工等级列表
            Route::get('member-level/list', [MemberLevelController::class, 'list'])->name('member-level.list');
            // 添加员工等级
            Route::post('member-level/add', [MemberLevelController::class, 'add'])->name('member-level.add');
            Route::delete('member-level/batchDelete', [MemberLevelController::class, 'batchDelete'])->name('member-level.batchDelete');
            // 修改状态
            Route::post('member-level/status/{memberLevel}', [MemberLevelController::class, 'status'])->name('member-level.status');
            // 修改员工等级
            Route::post('member-level/{memberLevel}', [MemberLevelController::class, 'edit'])->name('member-level.edit');
            // 删除员工等级
            Route::delete('member-level/{memberLevel}', [MemberLevelController::class, 'delete'])->name('member-level.delete');
            /** 员工接口结束 */

            /** 角色接口开始 */
            // 获取角色列表
            Route::get('role/getRoleList', [RoleController::class, 'getRoleList'])->name('role.getRoleList');
            // 角色列表
            Route::get('role/index', [RoleController::class, 'index'])->name('role.index')
                ->middleware('filter.process:' . Role::class);
            // 角色权限列表
            Route::get('role/permission/{role}', [RoleController::class, 'getPermissionList'])->name('role.getPermissionList');
            // 添加角色
            Route::post('role/add', [RoleController::class, 'add'])->name('role.add');
            // 批量删除角色
            Route::post('role/batchDelete', [RoleController::class, 'batchDelete'])->name('role.batchDelete');
            // 角色权限更新
            Route::post('role/permission/{role}', [RoleController::class, 'savePermissionList'])->name('role.savePermissionList');
            // 修改角色
            Route::post('role/{role}', [RoleController::class, 'edit'])->name('role.edit');
            // 修改角色状态
            Route::post('role/status/{role}', [RoleController::class, 'status'])->name('role.status');
            // 删除角色
            Route::delete('role/{role}', [RoleController::class, 'delete'])->name('role.delete');
            /** 角色接口结束 */

            /** 菜单接口开始 */
            // 菜单列表
            Route::get('menu/index', [MenuController::class, 'index'])->name('menu.index')
                ->middleware('filter.process:' . Menu::class);
            // 获取菜单列表
            Route::get('index/getMenuList', [MenuController::class, 'getMenuList'])->name('menu.getMenuList');
            // 菜单详情
            Route::get('menu/info/{menu}', [MenuController::class, 'info'])->name('menu.info');
            // 添加菜单
            Route::post('menu/add', [MenuController::class, 'add'])->name('menu.add');
            // 修改菜单
            Route::post('menu/{menu}', [MenuController::class, 'edit'])->name('menu.edit');
            // 删除菜单
            Route::delete('menu/{menu}', [MenuController::class, 'delete'])->name('menu.delete');
            /** 菜单接口结束 */

            /** 部门接口开始 */
            // 列表
            Route::get('dept/index', [DeptController::class, 'index'])->name('dept.index')
                ->middleware('filter.process:' . Dept::class);
            // 获取全部列表
            Route::get('dept/getDeptList', [DeptController::class, 'getDeptList'])->name('dept.getDeptList');
            // 详情
            Route::get('dept/info/{dept}', [DeptController::class, 'info'])->name('dept.info');
            // 添加
            Route::post('dept/add', [DeptController::class, 'add'])->name('dept.add');
            // 修改
            Route::post('dept/{dept}', [DeptController::class, 'edit'])->name('dept.edit');
            // 删除
            Route::delete('dept/{dept}', [DeptController::class, 'delete'])->name('dept.delete');
            /** 部门接口结束 */

            /** 岗位接口开始 */
            // 列表
            Route::get('position/index', [PositionController::class, 'index'])->name('position.index')
                ->middleware('filter.process:' . Position::class);
            // 获取全部列表
            Route::get('position/getPositionList', [PositionController::class, 'getPositionList'])->name('position.getPositionList');
            // 详情
            Route::get('position/info/{position}', [PositionController::class, 'info'])->name('position.info');
            // 添加
            Route::post('position/add', [PositionController::class, 'add'])->name('position.add');
            // 修改
            Route::post('position/{position}', [PositionController::class, 'edit'])->name('position.edit');
            // 删除
            Route::delete('position/{position}', [PositionController::class, 'delete'])->name('position.delete');
            /** 岗位接口结束 */

            /** 城市接口开始 */
            // 列表
            Route::get('city/index', [CityController::class, 'index'])->name('city.index')
                ->middleware('filter.process:' . City::class);
            // 添加
            Route::post('city/add', [CityController::class, 'add'])->name('city.add');
            // 修改
            Route::post('city/{city}', [CityController::class, 'edit'])->name('city.edit');
            // 删除
            Route::delete('city/{city}', [CityController::class, 'delete'])->name('city.delete');
            /** 城市接口结束 */

            /** 上传接口开始 */
            Route::prefix('upload')->group(function () {
                Route::post('uploadImage', [UploadController::class, 'uploadImage']);
                Route::post('uploadFile', [UploadController::class, 'uploadFile']);
            });
            /** 上传接口结束 */
        });
    });

    // 图片验证码
    Route::get('captcha', [CaptchasController::class, 'store'])->name('captcha.store');

    // 后台功能组 - 登录后才能访问的接口 - 验证 token 后会刷新 token 前端需要从响应 Header 中找到新的 token 进行替换
    Route::middleware('auth:api')->middleware('refresh.token')->group(function () {
        // 七牛云上传 token
        Route::get('qiniu/up-token', [QiNiuController::class, 'upToken'])->name('qiniu.up-token');
        // 七牛云私有图片
        Route::get('qiniu/private-url', [QiNiuController::class, 'privateUrl'])->name('qiniu.private-url');
    });

    // 处理访问不存在的请求
    Route::fallback(function () {
        return response()->json([
            'message' => 'Page Not Found. If error persists, contact info@website.com'], 404);
    });
});
