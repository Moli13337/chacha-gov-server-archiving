<?php
namespace App\Repositories;

use App\Models\ApplyModel;
use App\Repositories\Apply\ApplyPdfRepository;
use App\Repositories\Apply\ApplyRepository;
use Illuminate\Support\Facades\Log;
use App\Models\StaffCodeModel;
use Xkd\Pdf\Pdf;
use Exception;

/**
 * pdf
 * @author ASUS
 *
 */
class PdfRepository  extends BaseRepository
{
	public function model()
	{
		return StaffCodeModel::class;
	}
	
	/**
	 * 申请表pdf
	 * post
	 */
	public function createApprovalPdf($arr, $hasDepartment = false)
	{
		$html = '
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Document</title>
	<style type="text/css">
		* {
			margin: 0;
			padding: 0;
			font-size: 14px;
		}
		.page-title {
			font-size: 22px;
			font-weight: bold;
		}
		.text-center {
			text-align: center
		}
		.display-inline {
			display: inline;
		}
		.marginT10 {
			margin-top: 10px;
		}
		.xkd-table {
			width: 100%;
			border-collapse:collapse;
		}

		table.xkd-table, th, td, tr {
			border: 1px solid #DCDFE6;
		}
		table.xkd-table, td {
			padding: 10px;
		}
		table.xkd-table .table-label {
			background: #FBFBFB;
			font-weight: bold;
			width: 20%;
		}
		th {
			padding: 10px;
			background: #FBFBFB;
		}
		table.xkd-table .label-without-width {
			background: #FBFBFB;
			font-weight: bold;
		}
		.title-2 {
			font-size: 18px;
			font-weight: bold;
			margin-top: 20px;
			margin-bottom: 20px;
		}

		ul,li {
			list-style: none;
		}
		
		.clearfix:after {
			visibility: hidden;
			display: block;
			font-size: 0;
			content: " ";
			clear: both;
			height: 0;
		}
	</style>
</head>
<body>
	<div class="print-container">
		<div class="page-title text-center">'.$arr['title'].'</div>
		<div class="row marginT10">
			<div class="label display-inline">政策类型：</div>
			<div class="value display-inline">'.$arr['policy_name'].'</div>
		</div>
		<div class="row marginT10">
			<div class="label display-inline">支持项目：</div>
			<div class="value display-inline">'.$arr['project_name'].'</div>
		</div>
		<!-- 企业单位信息 -->
		<div class="title-2">企业基本情况</div>
		<table class="marginT10 xkd-table">
			<tr>
				<td class="table-label">单位名称</td>
				<td>'.$arr['enterprise_name'].'</td>
				<td class="table-label">组织机构代码</td>
				<td>'.$arr['organization_code'].'</td>
			</tr>
			<tr>
				<td class="table-label">注册地址</td>
				<td>'.$arr['regist_address'].'</td>
				<td class="table-label">注册时间</td>
				<td>'.date('Y-m-d', $arr['regist_time']).'</td>
			</tr>
			<tr>
				<td class="table-label">经营（办公）地址</td>
				<td>'.$arr['business_address'].'</td>
				<td class="table-label">经营（办公）面积</td>
				<td>'.$arr['business_area'].'m²</td>
			</tr>
			<tr>
				<td class="table-label">注册资本</td>
				<td>'.$arr['regist_capital'].'万元</td>
				<td class="table-label">统一社会信用代码</td>
				<td>'.$arr['unified_credit_code'].'</td>
			</tr>
			<tr>
				<td class="table-label">行业类别</td>
				<td colspan="3">'.implode('，', $arr['industry_text']).'</td>
			</tr>
			<tr>
				<td class="table-label">单位员工总数</td>
				<td>'.$arr['employee_number'].'人</td>
				<td class="table-label">本科以上学历人数</td>
				<td>'.$arr['employee_degree'].'人</td>
			</tr>
			<tr>
				<td class="table-label">大专学历人数</td>
				<td>'.$arr['employee_junior'].'人</td>
				<td class="table-label">其他学历人数</td>
				<td>'.$arr['employee_other'].'人</td>
			</tr>
		</table>
		<!-- 单位联系人信息 -->
		<div class="title-2">单位联系人信息</div>
		<table class="marginT10 xkd-table">
			<tr>
				<td class="label-without-width">法人代表姓名</td>
				<td>'.$arr['legal_name'].'</td>
				<td class="label-without-width">手机号码</td>
				<td>'.$arr['legal_phone'].'</td>
				<td class="label-without-width">微信号</td>
				<td>'.$arr['legal_wechat'].'</td>
			</tr>
			<tr>
				<td class="label-without-width">单位负责人姓名</td>
				<td>'.$arr['charge_name'].'</td>
				<td class="label-without-width">手机号码</td>
				<td>'.$arr['charge_phone'].'</td>
				<td class="label-without-width">微信号</td>
				<td>'.$arr['charge_wechat'].'</td>
			</tr>
			<tr>
				<td class="label-without-width">联系人姓名</td>
				<td>'.$arr['contact_name'].'</td>
				<td class="label-without-width">手机号码</td>
				<td>'.$arr['contact_phone'].'</td>
				<td class="label-without-width">微信号</td>
				<td>'.$arr['contact_wechat'].'</td>
			</tr>
		</table>
		<!-- 主要经济指标 -->
		<div class="title-2">主要经济指标</div>
		<table class="marginT10 xkd-table" style="text-align:center;">
			<tr>
				<td class="table-label"></td>
				<td>'.$arr['economy_list'][0]['year'].'年</td>
				<td>'.$arr['economy_list'][1]['year'].'年</td>
				<td>'.$arr['economy_list'][2]['year'].'年</td>
			</tr>
			<tr>
				<td class="table-label">销售收入（万元）</td>
				<td>'.$this->getEconomyResult($arr['economy_list'][0]['content_list'], APPLY_ECONOMY_TYPE['one']).'</td>
				<td>'.$this->getEconomyResult($arr['economy_list'][1]['content_list'], APPLY_ECONOMY_TYPE['one']).'</td>
				<td>'.$this->getEconomyResult($arr['economy_list'][2]['content_list'], APPLY_ECONOMY_TYPE['one']).'</td>
			</tr>
			<tr>
				<td class="table-label">总产值（万元）</td>
				<td>'.$this->getEconomyResult($arr['economy_list'][0]['content_list'], APPLY_ECONOMY_TYPE['two']).'</td>
				<td>'.$this->getEconomyResult($arr['economy_list'][1]['content_list'], APPLY_ECONOMY_TYPE['two']).'</td>
				<td>'.$this->getEconomyResult($arr['economy_list'][2]['content_list'], APPLY_ECONOMY_TYPE['two']).'</td>
			</tr>
			<tr>
				<td class="table-label">营业收入（万元）</td>
				<td>'.$this->getEconomyResult($arr['economy_list'][0]['content_list'], APPLY_ECONOMY_TYPE['three']).'</td>
				<td>'.$this->getEconomyResult($arr['economy_list'][1]['content_list'], APPLY_ECONOMY_TYPE['three']).'</td>
				<td>'.$this->getEconomyResult($arr['economy_list'][2]['content_list'], APPLY_ECONOMY_TYPE['three']).'</td>
			</tr>
			<tr>
				<td class="table-label">主营业务收入（万元）</td>
				<td>'.$this->getEconomyResult($arr['economy_list'][0]['content_list'], APPLY_ECONOMY_TYPE['four']).'</td>
				<td>'.$this->getEconomyResult($arr['economy_list'][1]['content_list'], APPLY_ECONOMY_TYPE['four']).'</td>
				<td>'.$this->getEconomyResult($arr['economy_list'][2]['content_list'], APPLY_ECONOMY_TYPE['four']).'</td>
			</tr>
			<tr>
				<td class="table-label">净利润（万元）</td>
				<td>'.$this->getEconomyResult($arr['economy_list'][0]['content_list'], APPLY_ECONOMY_TYPE['five']).'</td>
				<td>'.$this->getEconomyResult($arr['economy_list'][1]['content_list'], APPLY_ECONOMY_TYPE['five']).'</td>
				<td>'.$this->getEconomyResult($arr['economy_list'][2]['content_list'], APPLY_ECONOMY_TYPE['five']).'</td>
			</tr>
			<tr>
				<td class="table-label">出口总额（万美元）</td>
				<td>'.$this->getEconomyResult($arr['economy_list'][0]['content_list'], APPLY_ECONOMY_TYPE['six']).'</td>
				<td>'.$this->getEconomyResult($arr['economy_list'][1]['content_list'], APPLY_ECONOMY_TYPE['six']).'</td>
				<td>'.$this->getEconomyResult($arr['economy_list'][2]['content_list'], APPLY_ECONOMY_TYPE['six']).'</td>
			</tr>
			<tr>
				<td class="table-label">纳税额（万元）</td>
				<td>'.$this->getEconomyResult($arr['economy_list'][0]['content_list'], APPLY_ECONOMY_TYPE['seven']).'</td>
				<td>'.$this->getEconomyResult($arr['economy_list'][1]['content_list'], APPLY_ECONOMY_TYPE['seven']).'</td>
				<td>'.$this->getEconomyResult($arr['economy_list'][2]['content_list'], APPLY_ECONOMY_TYPE['seven']).'</td>
			</tr>
		</table>
		<!-- 项目申报情况 -->
		<div class="title-2">项目申报情况</div>
		<table class="xkd-table marginT10">
			<tr>
				<td colspan="4" class="table-label">一、企业主营业务介绍</td>
			</tr>
			<tr>
				<td colspan="4">'.$arr['business_content'].'</td>
			</tr>
			<tr>
				<td colspan="4" class="table-label">二、项目建设（计划）主要内容（含投资、主要产品及其产能等）</td>
			</tr>
			<tr>
				<td colspan="4">'.$arr['plan_content'].'</td>
			</tr>
			<tr>
				<td colspan="4" class="table-label">三、项目审批或核准、备案情况</td>
			</tr>
			<tr>
				<td class="table-label">批复机关</td>
				<td>'.$arr['approval_organ'].'</td>
				<td class="table-label">批文文号</td>
				<td>'.$arr['approval_number'].'</td>
			</tr>
			<tr>
				<td colspan="4" class="table-label">四、经认证的资格、资质、证书及称呼</td>
			</tr>
			<tr>
				<td colspan="4">'.$arr['qualifications'].'</td>
			</tr>
			<tr>
				<td colspan="4" class="table-label">五、申报政策条款</td>
			</tr>
			<tr>
				<td colspan="4">'.$arr['provisions'].'</td>
			</tr>
			<tr>
				<td class="table-label">申请扶持资金计算依据（标准）</td>
				<td>'.$arr['apply_criteria'].'</td>
				<td class="table-label">申请扶持资金金额（万元）</td>
				<td>'.$arr['apply_money'].'</td>
			</tr>
			<tr>
				<td colspan="4" class="table-label">六、其他说明</td>
			</tr>
			<tr>
				<td colspan="4">'.$arr['other_notes'].'</td>
			</tr>
		</table>
		<div class="title-2">本次附件清单</div>
		<!-- 本次附件清单 -->
		<div class="marginT10">
			<table class="xkd-table">
				<thead>
					<tr>
						<th width="10%">序号</th>
						<th width="20%">附件名称</th>
						<th width="20%">是否必备材料</th>
						<th>材料</th>
					</tr>
				</thead>
				<tbody>
					'.$this->getConfigList($arr['config']).'
				</tbody>
			</table>
		</div>
		';

		if ($hasDepartment) {
			if (!empty($arr['expert_mark'])) {
				$html .= '
<div class="title-2">部门评审意见</div>
<!-- 部门评审意见 -->
<table class="marginT10 xkd-table">
	<tr>
		<td colspan="4" class="table-label">部门评审意见：</td>
	</tr>
	<tr>
		<td width="10%" class="table-label">专家意见</td>
		<td>'.$arr['expert_mark'].'</td>
		<td width="10%" class="table-label">部门意见</td>
		<td>'.$arr['department_mark'].'</td>
	</tr>
</table>
<div class="clearfix" style="text-align: right; margin-top: 20px">
	<div style="float: right; width: 300px; text-align: left;">部门名称：</div>
</div>
<div class="clearfix" style="text-align: right; margin-top: 20px">
	<div style="float: right; width: 300px; text-align: left;">签名：</div>
</div>
				';
			} else {
				$html .= '
<div class="title-2">部门评审意见</div>
<!-- 部门评审意见 -->
<table class="marginT10 xkd-table">
	<tr>
		<td colspan="2" class="table-label">部门评审意见：</td>
	</tr>
	<tr>
		<td width="10%" class="table-label">部门意见</td>
		<td>'.$arr['department_mark'].'</td>
	</tr>
</table>
<div class="clearfix" style="text-align: right; margin-top: 20px">
	<div style="float: right; width: 300px; text-align: left;">部门名称：</div>
</div>
<div class="clearfix" style="text-align: right; margin-top: 20px">
	<div style="float: right; width: 300px; text-align: left;">签名：</div>
</div>
				';
			}
		}
		
		$html .= '
</div>
</body>
</html>';
		
		return $this->createPdf([
			'html' => $html,
			'business_id' => $arr['business_id']
		]);
	}
	
	public function getEconomyResult($arr, $type) {
		$result = '';
		if (!empty($arr)) {
			foreach ($arr as $key => $value) {
				if ($value['type'] == $type) {
					$result = $value['content'];
					break;
				}
			}
		}
		
		return $result;
	}
	
	
	public function getConfigList($arr) {
		$result = '';
		if (!empty($arr)) {
			foreach ($arr as $key => $value) {
				$index = $key + 1;
				$fileInfo = '';
				if (!empty($value['file_list'])) {
					$fileList = $value['file_list'];
					
					$fileInfo = $fileList[0]['file_name'] . '(上传时间'.date('Y-m-d H:i:s', $fileList[0]['created_at']).')';
				}
				
				$result .= '
					<tr>
						<td style="text-align: center">'.$index.'</td>
						<td style="text-align: center">'.$value['name'].'</td>
						<td style="text-align: center">'.$value['is_need_name'].'</td>
						<td>
							<ul>
								<li>'.$fileInfo.'</li>
							</ul>
						</td>
					</tr>
					';
			}
		}
	
		return $result;
	}
	
	/**
	 * 生成pdf
	 * post
	 */
	public function createPdf($arr)
	{
		if (empty($arr['html']) || empty($arr['business_id'])) {
			return false;
		}

		$params = [
			'html' => $arr['html'], 
			'business_id' => $arr['business_id']
		];
		
		try {
			
			$result = Pdf::store($params);
			
			return $result;
		} catch (Exception $e){
			Log::error('PdfRepository createPdf' . $e->getMessage());
			return false;
		}
	}
	
	/**
	 * 获取pdf
	 * post
	 */
	public function getPdf($arr)
	{
		if (empty($arr['business_id'])) {
			return false;
		}
	
		$params = [
        'business_id' => $arr['business_id']
		];

		try {
			$result = Pdf::result($params);
				
			return $result;
		} catch (Exception $e){
			Log::error('PdfRepository createPdf' . $e->getMessage());
			return false;
		}
	}

	// 申报pdf
	public function applyCreate($arr)
    {
        $html =  $this->buildHtml($arr);
        $business_id = array_get($arr, 'business_id', '');
        $params = [
            'html' => $html,
            'business_id' => $business_id,
        ];
        try {
            $flag = $this->createPdf($params);
        } catch (\Exception $e) {
            Log::error('applyCreate error: '. $e->getMessage());
        }
    }

    // 暂时不用做处理 blade 模板
    public function dealData($arr)
    {
        $economy_list = array_get($arr, 'economy_list', []);
        if (empty($economy_list)) {
            return $arr;
        }

        foreach ($economy_list as $key => $value) {
            $value['content_list_type'] = [];
            foreach ($value['content_list'] as $kc => $vc) {
                $value['content_list_type'][$vc['type']] = $vc['content'];
            }
            $economy_list[$key]['content_list_type'] =  $value['content_list_type'];
        }

        $arr['economy_list'] = $economy_list;

        // file
        if (!empty($arr['config'])) {

            foreach ($arr['config'] as $ka => $va) {
                $arr['config'] = '';
            }
            if (!empty($value['file_list'])) {
                $fileList = $value['file_list'];

                $fileInfo = $fileList[0]['file_name'] . '(上传时间'.date('Y-m-d H:i:s', $fileList[0]['created_at']).')';
            }
        }
        return $arr;
    }

    // 构建html
    public function buildHtml($arr)
    {
//        return view('pdf.applyDetail', $arr);
        $approvalType =  trans('constant.approval_type');
        $html = '
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Document</title>
	<style type="text/css">
		* {
			margin: 0;
			padding: 0;
			font-size: 14px;
		}
		.page-title {
			font-size: 22px;
			font-weight: bold;
		}
		.text-center {
			text-align: center
		}
		.display-inline {
			display: inline;
		}
		.marginT10 {
			margin-top: 10px;
		}
		.xkd-table {
			width: 100%;
			border-collapse:collapse;
		}

		table.xkd-table, th, td, tr {
			border: 1px solid #DCDFE6;
		}
		table.xkd-table, td {
			padding: 10px;
		}
		table.xkd-table .table-label {
			background: #FBFBFB;
			font-weight: bold;
			width: 20%;
		}
		th {
			padding: 10px;
			background: #FBFBFB;
		}
		table.xkd-table .label-without-width {
			background: #FBFBFB;
			font-weight: bold;
		}
		.title-2 {
			font-size: 18px;
			font-weight: bold;
			margin-top: 20px;
			margin-bottom: 20px;
		}

		ul,li {
			list-style: none;
		}
		
		.clearfix:after {
			visibility: hidden;
			display: block;
			font-size: 0;
			content: " ";
			clear: both;
			height: 0;
		}
	</style>
</head>
<body>
	<div class="print-container">
		<div class="page-title text-center">'.$arr['title'].'</div>
		<div class="row marginT10">
			<div class="label display-inline">政策类型：</div>
			<div class="value display-inline">'.$arr['policy_name'].'</div>
		</div>
		<div class="row marginT10">
			<div class="label display-inline">支持项目：</div>
			<div class="value display-inline">'.$arr['project_name'].'</div>
		</div>
		<!-- 企业单位信息 -->
		<div class="title-2">企业基本情况</div>
		<table class="marginT10 xkd-table">
			<tr>
				<td class="table-label">单位名称</td>
				<td>'.$arr['enterprise_name'].'</td>
				<td class="table-label">组织机构代码</td>
				<td>'.$arr['organization_code'].'</td>
			</tr>
			<tr>
				<td class="table-label">注册地址</td>
				<td>'.$arr['regist_address'].'</td>
				<td class="table-label">注册时间</td>
				<td>'.date('Y-m-d', $arr['regist_time']).'</td>
			</tr>
			<tr>
				<td class="table-label">经营（办公）地址</td>
				<td>'.$arr['business_address'].'</td>
				<td class="table-label">经营（办公）面积</td>
				<td>'.$arr['business_area'].'m²</td>
			</tr>
			<tr>
				<td class="table-label">注册资本</td>
				<td>'.$arr['regist_capital'].'万元</td>
				<td class="table-label">统一社会信用代码</td>
				<td>'.$arr['unified_credit_code'].'</td>
			</tr>
			<tr>
				<td class="table-label">行业类别</td>
				<td colspan="3">'.implode('，', $arr['industry_text']).'</td>
			</tr>
			<tr>
				<td class="table-label">单位员工总数</td>
				<td>'.$arr['employee_number'].'人</td>
				<td class="table-label">本科以上学历人数</td>
				<td>'.$arr['employee_degree'].'人</td>
			</tr>
			<tr>
				<td class="table-label">大专学历人数</td>
				<td>'.$arr['employee_junior'].'人</td>
				<td class="table-label">其他学历人数</td>
				<td>'.$arr['employee_other'].'人</td>
			</tr>
		</table>
		<!-- 单位联系人信息 -->
		<div class="title-2">单位联系人信息</div>
		<table class="marginT10 xkd-table">
			<tr>
				<td class="label-without-width">法人代表姓名</td>
				<td>'.$arr['legal_name'].'</td>
				<td class="label-without-width">手机号码</td>
				<td>'.$arr['legal_phone'].'</td>
				<td class="label-without-width">微信号</td>
				<td>'.$arr['legal_wechat'].'</td>
			</tr>
			<tr>
				<td class="label-without-width">单位负责人姓名</td>
				<td>'.$arr['charge_name'].'</td>
				<td class="label-without-width">手机号码</td>
				<td>'.$arr['charge_phone'].'</td>
				<td class="label-without-width">微信号</td>
				<td>'.$arr['charge_wechat'].'</td>
			</tr>
			<tr>
				<td class="label-without-width">联系人姓名</td>
				<td>'.$arr['contact_name'].'</td>
				<td class="label-without-width">手机号码</td>
				<td>'.$arr['contact_phone'].'</td>
				<td class="label-without-width">微信号</td>
				<td>'.$arr['contact_wechat'].'</td>
			</tr>
		</table>
		<!-- 主要经济指标 -->
		<div class="title-2">主要经济指标</div>
		<table class="marginT10 xkd-table" style="text-align:center;">
			<tr>
				<td class="table-label"></td>
				<td>'.$arr['economy_list'][0]['year'].'年</td>
				<td>'.$arr['economy_list'][1]['year'].'年</td>
				<td>'.$arr['economy_list'][2]['year'].'年</td>
			</tr>
			<tr>
				<td class="table-label">销售收入（万元）</td>
				<td>'.$this->getEconomyResult($arr['economy_list'][0]['content_list'], APPLY_ECONOMY_TYPE['one']).'</td>
				<td>'.$this->getEconomyResult($arr['economy_list'][1]['content_list'], APPLY_ECONOMY_TYPE['one']).'</td>
				<td>'.$this->getEconomyResult($arr['economy_list'][2]['content_list'], APPLY_ECONOMY_TYPE['one']).'</td>
			</tr>
			<tr>
				<td class="table-label">总产值（万元）</td>
				<td>'.$this->getEconomyResult($arr['economy_list'][0]['content_list'], APPLY_ECONOMY_TYPE['two']).'</td>
				<td>'.$this->getEconomyResult($arr['economy_list'][1]['content_list'], APPLY_ECONOMY_TYPE['two']).'</td>
				<td>'.$this->getEconomyResult($arr['economy_list'][2]['content_list'], APPLY_ECONOMY_TYPE['two']).'</td>
			</tr>
			<tr>
				<td class="table-label">营业收入（万元）</td>
				<td>'.$this->getEconomyResult($arr['economy_list'][0]['content_list'], APPLY_ECONOMY_TYPE['three']).'</td>
				<td>'.$this->getEconomyResult($arr['economy_list'][1]['content_list'], APPLY_ECONOMY_TYPE['three']).'</td>
				<td>'.$this->getEconomyResult($arr['economy_list'][2]['content_list'], APPLY_ECONOMY_TYPE['three']).'</td>
			</tr>
			<tr>
				<td class="table-label">主营业务收入（万元）</td>
				<td>'.$this->getEconomyResult($arr['economy_list'][0]['content_list'], APPLY_ECONOMY_TYPE['four']).'</td>
				<td>'.$this->getEconomyResult($arr['economy_list'][1]['content_list'], APPLY_ECONOMY_TYPE['four']).'</td>
				<td>'.$this->getEconomyResult($arr['economy_list'][2]['content_list'], APPLY_ECONOMY_TYPE['four']).'</td>
			</tr>
			<tr>
				<td class="table-label">净利润（万元）</td>
				<td>'.$this->getEconomyResult($arr['economy_list'][0]['content_list'], APPLY_ECONOMY_TYPE['five']).'</td>
				<td>'.$this->getEconomyResult($arr['economy_list'][1]['content_list'], APPLY_ECONOMY_TYPE['five']).'</td>
				<td>'.$this->getEconomyResult($arr['economy_list'][2]['content_list'], APPLY_ECONOMY_TYPE['five']).'</td>
			</tr>
			<tr>
				<td class="table-label">出口总额（万美元）</td>
				<td>'.$this->getEconomyResult($arr['economy_list'][0]['content_list'], APPLY_ECONOMY_TYPE['six']).'</td>
				<td>'.$this->getEconomyResult($arr['economy_list'][1]['content_list'], APPLY_ECONOMY_TYPE['six']).'</td>
				<td>'.$this->getEconomyResult($arr['economy_list'][2]['content_list'], APPLY_ECONOMY_TYPE['six']).'</td>
			</tr>
			<tr>
				<td class="table-label">纳税额（万元）</td>
				<td>'.$this->getEconomyResult($arr['economy_list'][0]['content_list'], APPLY_ECONOMY_TYPE['seven']).'</td>
				<td>'.$this->getEconomyResult($arr['economy_list'][1]['content_list'], APPLY_ECONOMY_TYPE['seven']).'</td>
				<td>'.$this->getEconomyResult($arr['economy_list'][2]['content_list'], APPLY_ECONOMY_TYPE['seven']).'</td>
			</tr>
		</table>
		<!-- 项目申报情况 -->
		<div class="title-2">项目申报情况</div>
		<table class="xkd-table marginT10">
			<tr>
				<td colspan="4" class="table-label">一、企业主营业务介绍</td>
			</tr>
			<tr>
				<td colspan="4">'.$arr['business_content'].'</td>
			</tr>
			<tr>
				<td colspan="4" class="table-label">二、项目建设（计划）主要内容（含投资、主要产品及其产能等）</td>
			</tr>
			<tr>
				<td colspan="4">'.$arr['plan_content'].'</td>
			</tr>
			<tr>
				<td colspan="4" class="table-label">三、项目审批或核准、备案情况</td>
			</tr>
			<tr>
				<td class="table-label">批复机关</td>
				<td>'.$arr['approval_organ'].'</td>
				<td class="table-label">批文文号</td>
				<td>'.$arr['approval_number'].'</td>
			</tr>
			<tr>
				<td colspan="4" class="table-label">四、经认证的资格、资质、证书及称呼</td>
			</tr>
			<tr>
				<td colspan="4">'.$arr['qualifications'].'</td>
			</tr>
			<tr>
				<td colspan="4" class="table-label">五、申报政策条款</td>
			</tr>
			<tr>
				<td colspan="4">'.$arr['provisions'].'</td>
			</tr>
			<tr>
				<td class="table-label">申请扶持资金计算依据（标准）</td>
				<td>'.$arr['apply_criteria'].'</td>
				<td class="table-label">申请扶持资金金额（万元）</td>
				<td>'.$arr['apply_money'].'</td>
			</tr>
			<tr>
				<td colspan="4" class="table-label">六、其他说明</td>
			</tr>
			<tr>
				<td colspan="4">'.$arr['other_notes'].'</td>
			</tr>
		</table>
		<div class="title-2">本次附件清单</div>
		<!-- 本次附件清单 -->
		<div class="marginT10">
			<table class="xkd-table">
				<thead>
					<tr>
						<th width="10%">序号</th>
						<th width="20%">附件名称</th>
						<th width="20%">是否必备材料</th>
						<th>材料</th>
					</tr>
				</thead>
				<tbody>
					'.$this->getConfigList($arr['config']).'
				</tbody>
			</table>
		</div>
		';

        if (!empty($arr['approval_list'])) {
            foreach ($arr['approval_list'] as $key => $value) {
                if (array_get($value, 'approval_type') == APPROVAL_TYPE['five']) {
                    continue;
                }
                $html .= '<br/><table style="border: 1px solid #DCDFE6; border-collapse: collapse; width: 100%; font-size:14px;">
              <tr style="background: #FBFBFB; height: 40px;">
                <td
                  colspan="2"
                  style="padding: 10px 20px; border: 1px solid #DCDFE6;"
                >
                  <div
                    class="display-flex justify-content-space-between"
                  >
                    <div>
                      <button style="color: #42B436; height: 32px; border-radius: 4px; padding: 0 10px;background-color: rgba(66,180,54,.1); border: 1px solid rgba(66,180,54,.2); margin-right: 10px; font-size: 12px; font-weigth: 500">
                      '.array_get($approvalType, $value['approval_type'], '').'</button>'.array_get( $value,'department_name', "").'评审意见为：
                    </div>               
                  </div>
                </td>
              </tr>'.$this->buildHtmlCoordination($value).'
              <tr>
                <td style="padding: 10px 20px; border: 1px solid #DCDFE6; background: #FBFBFB; width: 233.33px;text-align: right; padding-right: 33px;">
                专家意见</td>
                <td style="padding: 10px 20px; border: 1px solid #DCDFE6;">'.array_get( $value,'expert_mark', '').'</td>
              </tr>
              <tr>
                <td style="padding: 10px 20px; border: 1px solid #DCDFE6; background: #FBFBFB; width: 233.33px;text-align: right; padding-right: 33px;">
                部门评审意见</td>
                <td style="padding: 10px 20px; border: 1px solid #DCDFE6;">'.array_get( $value,'department_mark', '').'</td>
              </tr>
              <tr>
                <td style="padding: 10px 20px; border: 1px solid #DCDFE6; background: #FBFBFB; width: 233.33px;text-align: right; padding-right: 33px;">部门佐证资料</td>
                <td style="padding: 10px 20px; border: 1px solid #DCDFE6;">'.array_get($value['file_list'][0]??[], 'file_name', '').'</td>
              </tr>
            </table>';
            }
        }

        if (array_get($arr, 'apply_status', 0) == APPLY_STATUS['nine']) {
            $html .= '<br/><table style="border: 1px solid #DCDFE6; border-collapse: collapse; width: 100%; font-size:14px;">'.
                $this->buildHtmlDefer(array_get($arr, 'defer_mark', '')).'
      <tr>
        <td style="padding: 10px 20px; border: 1px solid #DCDFE6; background: #FBFBFB; width: 233.33px;text-align: right; padding-right: 33px;">拨款反馈</td>
        <td style="padding: 10px 20px; border: 1px solid #DCDFE6;">
          <p>申报状态：申报成功</p>
          <p>获得支持（万元）：'.array_get($arr, 'support_content', '').'</p>
          <p>拨款状态：已拨款</p>
          <p>拨款时间：'. date('Y-m-d', $arr['allocation_time']).'</p>
        </td>
      </tr>
    </table>';
        }

        $html .= '
</div>
</body>
</html>';
        return $html;

    }

    /**
     * FUNCTION_NAME : buildHtmlCoordination
     * author : jp
     * 协同部门
     * @param $value
     * @return string
     */
    public function buildHtmlCoordination($value)
    {
        if (array_get($value, 'approval_type', 0) != APPROVAL_TYPE['three']) {
            return '';
        }
        $str = ' <tr>
                <td style="padding: 10px 20px; border: 1px solid #DCDFE6; background: #FBFBFB; width: 233.33px;text-align: right; padding-right: 33px;">
                部门重点审核事项</td>
                <td style="padding: 10px 20px; border: 1px solid #DCDFE6;">'.array_get( $value,'remark', '').'</td>
              </tr>';
        return $str;
    }

    /**
     * FUNCTION_NAME : buildHtmlDefer
     * author : jp
     * 延时拨款
     * @param $defer
     * @return string
     */
    public function buildHtmlDefer($defer)
    {
        if (empty($defer)) {
            return '';
        }
        $str = ' <tr>
                <td style="padding: 10px 20px; border: 1px solid #DCDFE6; background: #FBFBFB; width: 233.33px;text-align: right; padding-right: 33px;">
                延时拨款原因</td>
                <td style="padding: 10px 20px; border: 1px solid #DCDFE6;">'.$defer.'</td>
              </tr>';
        return $str;
    }

    // 申报表创建
    public function approvalCreate($arr)
    {
        $business_id = businessId();
        $arr['business_id'] = $business_id;
        $res = $this->createApprovalPdf($arr, false);
        if (!empty($res)) {
            $where = [
                'id' => $arr['id'],
            ];
            $update = [
                'business_id' => $business_id,
            ];
            try {
                ApplyModel::where($where)->update($update);
            } catch (\Exception $e) {
                Log::error('createApprovalPdf error: '. $e->getMessage());
            }
        }
    }


	
}