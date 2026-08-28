<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Apply\YoutuRepository;
use App\Common\Code;
use App\Repositories\Apply\ApprovalAcceptRepository;
use App\Repositories\Apply\ApplyChartRepository;
use App\Repositories\Apply\ApplyCheckRepository;
use App\Repositories\Apply\ApplyRepository;
use App\Repositories\Apply\AliyunRepository;
use App\Repositories\Staff\ResourceRepository;
use App\Repositories\Staff\StaffTokenRepository;
use App\Repositories\PdfRepository;
use App\Models\ApplyModel;
use App\Models\ApplyEconomyModel;
use App\Models\ApplyFileModel;


class TestController extends Controller
{
	
	/**
	 * add
	 */
	public function testLogin(Request $request)
	{
		$companyName = '上海胜邦房产经纪有限公司';
		
		$auth_org = [];
		$url = 'http://open.api.tianyancha.com/services/v3/newopen/searchV2.json?word='.urlencode($companyName);
		$header = array('Authorization:OvxYUMkYgaiZ');
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		$data = curl_exec($curl);
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
// 		$data = json_decode($data,true);
		
		dd($data);
		
		
		if (isset($data['data']) && !empty($data['data'])) {
			foreach ($data['data'] as $key => $value) {
				// 处理天眼查返回的数据
				$value['name'] = str_replace('<em>', '', $value['name']);
				$value['name'] = str_replace('</em>', '', $value['name']);
				$auth_org[] = ['company_name' =>  $value['name']];
			}
		}
		return $auth_org;
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		$html = '
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Document</title>
	<style>
		* {
			color: #333;
		}
		.container {
			max-width: 1366px;
			margin: 0 auto;
		}
		.title {
			font-size: 30px;
			text-align: center;
			font-weight: bold;
			padding: 30px;
			margin-bottom: 30px;
			border-bottom: 3px solid #333;
		}
		.table {
			width: 100%;
			border-spacing: 0;
			border-collapse: collapse;
		}
		.table td {
			padding: 10px;
			border: 1px #333 solid;
		}
		.footer {
			margin-top: 100px;
		}
		.m-l-40 {
			margin-left: 40px;
		}
		.m-t-20 {
			margin-top: 20px;
		}
		.m-l-r-5 {
			margin: 0 5px;
		}
		.underline{
			text-decoration: underline;
		}
		.display-flex {
			display: box;
			display: -webkit-box;
			display: -moz-box;
			display: -ms-flexbox;
			display: -webkit-flex;
			display: flex;
		}

		.flex-1 {
			-webkit-box-flex: 1;
			-moz-box-flex: 1;
			-webkit-flex: 1;
			-ms-flex: 1;
			flex: 1;
		}

		.justify-content-center {
			-webkit-justify-content: center;
			-moz-justify-content: center;
			-ms-justify-content: center;
			-o-justify-content: center;
			justify-content: center;
		}

		.justify-content-between {
			-webkit-justify-content: space-between;
			-moz-justify-content: space-between;
			-ms-justify-content: space-between;
			-o-justify-content: space-between;
			justify-content: space-between;
		}

		.justify-content-around {
			-webkit-justify-content: space-around;
			-moz-justify-content: space-around;
			-ms-justify-content: space-around;
			-o-justify-content: space-around;
			justify-content: space-around;
		}

		.align-items-center {
			-webkit-align-items: center;
			-moz-align-items: center;
			-ms-align-items: center;
			-o-align-items: center;
			align-items: center;
		}
	</style>
</head>

<body>
	<div class="container">
		<div class="title">补充协议</div>
		<table class="table">
			<tr>
				<td>
					协议签订日期：2019-09-09
				</td>
			</tr>
			<tr>
				<td>
					甲方：2019-09-09
				</td>
			</tr>
			<tr>
				<td>
					<div class="display-flex justify-content-between">
						<span>甲方：2019-09-09</span>
						<span>统一社会信用代码/身份证号: asdavuy</span>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div>
						甲乙双方于<span class="underline m-l-r-5">2019</span class="underline m-l-r-5">年<span class="underline m-l-r-5">08</span>月<span class="underline m-l-r-5">09</span>日 签订了合同编号为<span class="underline m-l-r-5">FYDD-FH-20190805173836-995</span>的《孵化管理服务协议》，（ 以下简称“原协议”），根据《中华人民共和国合同法》及相关法律规定，现双方在平等、友好、自愿基础上，协商一致，就原协议的相关约定达成补充/变更条款，以资双方共同遵守。
					</div>
				</td>
			</tr>
		</table>
		<div class="footer">
			<div class="display-flex justify-content-between">
				<div class="flex-1">
					<div class="m-t-20">甲方（签章）：</div>
					<div class="m-t-20">签约代表人：</div>
					<div class="m-t-20">
						<span>日期：</span>
						<span class="m-l-40">年</span>
						<span class="m-l-40">月</span>
						<span class="m-l-40">日</span>
					</div>
				</div>
				<div class="flex-1">
					<div class="m-t-20">乙方（签章）：</div>
					<div class="m-t-20">签约代表人：</div>
					<div class="m-t-20">
						<span>日期：</span>
						<span class="m-l-40">年</span>
						<span class="m-l-40">月</span>
						<span class="m-l-40">日</span>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>

</html>
			
			';
		app(PdfRepository::class)->createPdf([
			'html' => $html,
			'business_id' => businessId()
		]);
		die;
		
		$r = '成都市促进国内外高校院都市促进国内外高校院所科技成果在蓉转移转化若干政策';
// 		echo mb_substr($r, 0, 17, 'utf-8') . '...';
		echo getStrLength($r);
		die;
	
		$applyObj = app(ApplyRepository::class)->detailApply(['id' => 79]);
		$applyObj['business_id'] = businessId();
		$result = app(PdfRepository::class)->createApprovalPdf($applyObj);
		$applyObj['business_id'] = businessId();
		$result = app(PdfRepository::class)->createApprovalPdf($applyObj, true);
		dd($result);
		echo md5(17708112019);
		die;
		$a = [1, 2, 3];
		$b = [2, 4];
		
		$c = array_merge(array_diff($a,$b),array_diff($b,$a));
		
		dd($c);
// 		setLoginStaff(['id' => 1]);
// 		app()->instance(LOGIN_STAFF_KEY, ['id' => 1]);
// 		dd(getLoginStaff());
// 		$data = $request->all();
// 		dd($data);

// 		$result = app(ResourceRepository::class)->permissionList([
// 			'staff_id' => 10
// 		]);
		$result = app(StaffTokenRepository::class)->changeToken([]);
		return codeRender(Code::OK, $result);
	}
	
	// 发票识别测试接口
	public function testInvoice(Request $request) {
		$data = $request->all();
		$urlTmp = 'https://xkd-saas-base.oss-cn-beijing.aliyuncs.com/dev-wenjiang/20190723/8/rF6ag8bDm8YDnZ5LLmgRuzueLsRu7evqzu4f1V0H.png';
		
		$url = empty($data['url']) ? $urlTmp : $data['url'];
		$result = app(YoutuRepository::class)->ocrInvoice($url);
		return codeRender(Code::OK, $result);
	}
	
	// 发票真假
	public function aliInvoice(Request $request) {
		$data = $request->all();
		
		$data['invoice_code'] = $data['invoice_code'] ?? '5100182130';
		$data['invoice_checkcode'] = $data['invoice_checkcode'] ?? '';
		$data['invoice_number'] = $data['invoice_number'] ?? '12425488';
		$data['invoice_billing_date'] = $data['invoice_billing_date'] ?? '2018-12-07';
		$data['invoice_money'] = $data['invoice_money'] ?? '72874.29';

		$result = app(AliyunRepository::class)->checkInvoice($data);
		return codeRender(Code::OK, $result);
	}

	public function testAcceptList() {
// 		echo hash("sha256", '18081840922druid');
// 		die;
// echo date('Y-m-d.His', -2209017600);
// // die;
		
// 		echo strtotime('1900-01-01');
// 		die;
// 		$result = app(ApplyChartRepository::class)->getApplyByEnterpriseId([
// 			'select_type' => 3,
// 			'enterprise_id' => 1
// 		]);

// 		preg_match($pattern, $subject)

// 		$result = app(ApplyCheckRepository::class)->checkApproval();

// 		$result = app(ApplyRepository::class)->detail([
// 			'id' => 3
// 		]);

		$result = app(ApplyCheckRepository::class)->checkApproval();
		
		return codeRender(Code::OK, $result);
	}
	
	public function testAcceptStore(Request $request) {
		$data = $request->all();
		$result = app(ApprovalAcceptRepository::class)->store($data);
	
		return codeRender(Code::OK, $result);
	}
	
	public function testAcceptRenew(Request $request) {
		$data = $request->all();
		$result = app(ApprovalAcceptRepository::class)->renew($data);
	
		return codeRender(Code::OK, $result);
	}
	
	// 预审核脚本 - 15分钟？
	public function checkApply(Request $request) {
		$result = app(ApplyCheckRepository::class)->checkApply();
		return codeRender(Code::OK, $result);
	}
	
	// 检查主审部门、协同部门、园区管委会的审批时间
	public function checkApproval(Request $request) {
		$result = app(ApplyCheckRepository::class)->checkApproval();
		return codeRender(Code::OK, $result);
	}
	
	// 补充资料发送消息提醒 - 1分钟检查一次？
	public function checkMaterial(Request $request) {
		$result = app(ApplyCheckRepository::class)->checkMaterial();
		return codeRender(Code::OK, $result);
	}
	
	// pdf
	public function checkApplyPdf(Request $request) {
		// 申请表
		$where = [];
		$where[] = ['apply_status', '!=', 1];
		$where[] = ['pdf_url', '=', ''];
		$applyList = ApplyModel::where($where)
			->get()
			->toArray();
	
		if (empty($applyList)) {
			return 'apply is empty';
		}
	
		$success = 0;
		
		foreach ($applyList as $key1 => $apply) {
			// 行业类别处理
			$apply['industry_id'] = empty($apply['industry_id']) ?
				[] : explode('|', $apply['industry_id']);
			$apply['industry_text'] = empty($apply['industry_text']) ?
				[] : explode('|', $apply['industry_text']);
			
			// 经济指标
			$resultEco = ApplyEconomyModel::where(['apply_id' => $apply['id']])
				->orderBy('year', 'asc')
				->get()
				->toArray();
			
			if (empty($resultEco)) {
				continue;
			}
				
			// 按照日期组装
			$yearList = [];
			foreach ($resultEco as $key => $value) {
				if (!in_array($value['year'], $yearList)) {
					$yearList[] = $value['year'];
				}
			}
			
			// 组装数据
			$economyList = [];
			foreach ($yearList as $key => $value) {
				$tmpList = [];
				foreach ($resultEco as $key2 => $value2) {
					if ($value === $value2['year']) {
						$tmpList[] = $value2;
					}
				}
			
				$economyList[] = [
					'year' => $value,
					'content_list' => $tmpList
				];
			}
			$apply['economy_list'] = $economyList;
			
			// 附件快照
			$fileConfig = empty($apply['config']) ? [] : json_decode($apply['config'], true);
			
			$resultFile = ApplyFileModel::where(['apply_id' => $apply['id']])
				->orderBy('id', 'asc')
				->get([
					'id',
					'file_name',
					'file_url',
					'file_type',
					'project_materials_id',
					'create_at',
					'create_at AS created_at'
				])
				->toArray();
			
			if (!empty($resultFile)) {
				foreach ($fileConfig as $key => $value) {
					$tmpList = [];
					foreach ($resultFile as $key2 => $value2) {
						if ($value['id'] === $value2['project_materials_id']) {
							$tmpList[] = $value2;
							unset($resultFile[$key2]);
						}
					}
			
					$value['file_list'] = $tmpList;
					$fileConfig[$key] = $value;
				}
			}
			
			$apply['config'] = $fileConfig;
			$apply['business_id'] = businessId();
			
			
			ApplyModel::where([
				'id' => $apply['id']
			])->update([
				'business_id' => $apply['business_id']
			]);
			
			// pdf调用
			app(PdfRepository::class)->createApprovalPdf($apply);
			
			$success++;
		}
		
		echo '总数:' . count($applyList) . '   成功:' . $success;
	}
}
