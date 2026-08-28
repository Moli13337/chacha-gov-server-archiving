<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/25
 * Time: 17:41
 */

namespace App\Http\Controllers\Home;


use App\Common\Code;
use App\Http\Controllers\Controller;
use App\Http\Requests\Unscramble\HomeDetailRequest;
use App\Repositories\Policy\PolicyUnscrambleRepository;
use App\Support\Collection;

class UnscrambleController extends Controller
{

    protected $policyUnscrambleRepository;

    public function __construct(PolicyUnscrambleRepository $policyUnscrambleRepository)
    {
        $this->policyUnscrambleRepository = $policyUnscrambleRepository;
    }

    public function detail(HomeDetailRequest $request)
    {
        $column = [
            'id',
            'code',
            'name',
            'content_url',
            'publish_status'
        ];
        $data = $this->policyUnscrambleRepository->cDetail($request->input('id'), $column);

        if (empty($data)) {
            return codeRender(Code::OK, $data);
        }

        if (!empty($data['policy'])) {
            $policy_key = [
                'id',
                'enc_id',
                'name',
            ];

            foreach ($data['policy'] as $key => $value) {
                $value = Collection::filter($policy_key, $value);
                $value['id'] = $value['enc_id'];
                $data['policy'][$key] = $value;
            }
        }
        return codeRender(Code::OK, $data);
    }
}