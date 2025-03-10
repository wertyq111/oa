<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Api\FormRequest;
use App\Http\Requests\Api\User\MemberRequest;
use App\Http\Resources\User\MemberResource;
use App\Models\User\Member;
use App\Services\Api\User\MemberService;
use Illuminate\Http\Request;

class MembersController extends Controller
{
    /**
     * 加载服务
     */
    public function __construct()
    {
        parent::__construct();
        $this->service = new MemberService();
    }

    /**
     * 会员列表
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @author zhouxufeng <zxf@netsun.com>
     * @date 2024/4/10 09:33
     */
    public function index(FormRequest $request, Member $member)
    {
        // 生成允许过滤字段数组
        $allowedFilters = $request->generateAllowedFilters($member->getRequestFilters());

        $config = [
            'includes' => ['user'],
            'allowedFilters' => $allowedFilters
        ];
        $members = $this->queryBuilder(Member::class, true, $config);

        $list = MemberResource::collection($members);
        return $list;
    }

    /**
     * 获取会员信息
     *
     * @param FormRequest $request
     * @return mixed|string
     * @author zhouxufeng <zxf@netsun.com>
     * @date 2024/4/15 14:16
     */
    public function info(FormRequest $request)
    {
        $data = $request->getSnakeRequest();

        try {
            $member = $this->service->getMember($data);
        } catch (\Exception $e) {
            throw $e;
        }


        return $this->resource($member);
    }

    /**
     * 获取当前会员信息
     *
     * @param FormRequest $request
     * @return mixed|string
     * @author zhouxufeng <zxf@netsun.com>
     * @date 2024/4/15 14:16
     */
    public function user()
    {

        try {
            $member = auth()->user()->member;
            $member['email'] = $member->user->email;
            $member['phone'] = $member->user->phone;
            $member = array_merge($member->toArray(), $this->service->getMember($member));

        } catch (\Exception $e) {
            throw $e;
        }


        return $this->resource($member);
    }

    /**
     * 修改状态
     *
     * @param Member $member
     * @param FormRequest $request
     * @return MemberResource
     * @author zhouxufeng <zxf@netsun.com>
     * @date 2024/4/10 09:37
     */
    public function status(Member $member, FormRequest $request)
    {
        $member->status = $request->get('status');
        $member->edit();

        return new MemberResource($member);
    }

    /**
     * 创建会员
     *
     * @param MemberRequest $request
     * @return MemberResource
     * @author zhouxufeng <zxf@netsun.com>
     * @date 2024/4/10 09:39
     */
    public function add(MemberRequest $request)
    {
        $data = $request->getSnakeRequest();

        // 添加会员
        $member = $this->service->add($data);

        return new MemberResource($member);

    }

    /**
     * 修改会员
     *
     * @param Member $member
     * @param MemberRequest $request
     * @return MemberResource
     * @author zhouxufeng <zxf@netsun.com>
     * @date 2024/4/10 09:39
     */
    public function edit(Member $member, MemberRequest $request)
    {
        $data = $request->getSnakeRequest();

        $data = $this->service->completeMember($data);

        $member->fill($data);

        $member->edit();

        return new MemberResource($member);
    }

    /**
     * 修改登录用户信息
     *
     * @param Member $member
     * @param MemberRequest $request
     * @return MemberResource
     * @author zhouxufeng <zxf@netsun.com>
     * @date 2024/4/10 09:39
     */
    public function editUser(MemberRequest $request)
    {
        $data = $request->getSnakeRequest();

        // 获取登录用户
        $user = auth()->user();
        $member = $user->member;

        $data = $this->service->completeMember($data);

        $member->fill($data);

        $member->edit();

        // 更新 user
        if($data['email'] && $data['phone']) {
            $userData = [
                'email' => $data['email'],
                'phone' => $data['phone']
            ];
            $user->fill($userData);
            $user->edit();
        }

        return new MemberResource($member);
    }

    /**
     * 删除会员
     *
     * @param Member $member
     * @return \Illuminate\Http\JsonResponse
     * @author zhouxufeng <zxf@netsun.com>
     * @date 2024/4/10 10:13
     */
    public function delete(Member $member)
    {
        $member->delete();

        return response()->json([]);
    }

    /**
     * 更新打赏
     *
     * @param MemberRequest $request
     * @param Member $member
     * @return MemberResource
     * @author zhouxufeng <zxf@netsun.com>
     * @date 2023/6/8 16:54
     */
    public function updateAdmire(MemberRequest $request, Member $member)
    {
        $member = $member->find($request->get('id'));

        $member->admire = $request->get('admire');

        $member->edit();

        return new MemberResource($member);
    }
}
