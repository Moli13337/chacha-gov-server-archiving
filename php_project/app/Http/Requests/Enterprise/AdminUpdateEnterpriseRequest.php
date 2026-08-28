<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/6
 * Time: 11:05
 */

namespace App\Http\Requests\Enterprise;


use App\Http\Requests\BaseFormRequest;
use App\Models\EnterpriseModel;
use Illuminate\Validation\Rule;

class AdminUpdateEnterpriseRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'id' => ['required','integer', Rule::exists(EnterpriseModel::TABLE_NAME)->where(function ($query){
                $query->whereNull('deleted_at');
            })],
            'name' => ['required','string', 'max:50',Rule::unique(EnterpriseModel::TABLE_NAME)->ignore($this->input('id'))->where(function ($query){
                $query->whereNull('deleted_at');
            })],
            'unified_credit_code' => ['required','string','max:20',Rule::unique(EnterpriseModel::TABLE_NAME)->ignore($this->input('id'))->where(function ($query){
                $query->whereNull('deleted_at');
            })],
            'organization_code' => ['required','string','max:100',Rule::unique(EnterpriseModel::TABLE_NAME)->ignore($this->input('id'))->where(function ($query){
                $query->whereNull('deleted_at');
            })],
            'tax_number' => ['required','string','max:20', Rule::unique(EnterpriseModel::TABLE_NAME)->ignore($this->input('id'))->where(function ($query){
                $query->whereNull('deleted_at');
            })],
            'legal_represent' => ['required','string','max:50',],
            'regist_capital' => ['required','numeric'],
            'regist_time' => ['required','int'],
            'regist_address' => ['required','string','max:100',],
            'business_area' => ['nullable','numeric'],
            'business_address' => ['nullable','string','max:100',],
            "first_industry_id" => ['nullable', 'integer'],
            "second_industry_id" => ['nullable', 'integer'],
            "third_industry_id" => ['nullable', 'integer'],
            "fourth_industry_id" => ['nullable', 'integer'],
        ];
    }
}