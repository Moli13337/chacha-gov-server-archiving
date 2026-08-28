let enterpriseMap =
{
	enterprise_name: '请填写单位名称!',
	// 注册地址
	regist_address: '请填写注册地址！',
	// 注册时间
	regist_time: '请填写注册时间！',
	// 注册资本
	regist_capital: '请填写注册资本！',
	// 经营（办公）地址
	business_address: '请填写经营（办公）地址！',
	// 经营（办公）面积
	business_area: '请填写经营（办公）面积！',
	// 统一信用代码
	unified_credit_code: '请填写统一信用代码！',
	// 组织机构代码
	organization_code: '请填写组织机构代码！',
	// 行业类别
	industry_type: '请填写行业类别！',
	// 单位员工总数
	employee_number: '请填写单位员工总数！',
	// 本科以上学历人数
	employee_degree: '请填写本科以上学历人数！',
	// 大专学历人数
	employee_junior: '请填写大专学历人数！',
	// 其他学历人数
	employee_other: '请填写其他学历人数！',
};

let contactMap = {
	// 法定代表人
	legal_name: '请填写法定代表人！',
	// 法人手机号
	legal_phone: '请填写法人手机号！',
	// 法人微信号
	legal_wechat: '请填写法人微信号！',
	// 单位负责人姓名
	charge_name: '请填写单位负责人姓名！',
	// 负责人手机号
	charge_phone: '请填写负责人手机号！',
	// 负责人微信
	charge_wechat: '请填写负责人微信！',
	// 联系人姓名
	contact_name: '请填写联系人姓名！',
	// 联系人手机号
	contact_phone: '请填写联系人手机号！',
	// 联系人微信
	contact_wechat: '请填写联系人微信！',
};

let economyMap = {
	1: '请填写销售收入！',
	2: '请填写总产值！',
	3: '请填写营业收入！',
	4: '请填写主营业务收入！',
	5: '请填写净利润！',
	6: '请填写出口总额！',
	7: '纳税额！'
};

let enterpriseDataMap = {
	// 项目ID
	project_id: {
		target: 'declarationInfo',
		key: 'project_id'
	},
	// 项目名称
	project_name: {
		target: 'declarationInfo',
		key: 'name'
	},
	// 政策类型
	policy_name: {
		target: 'declarationInfo',
		key: 'mold_name'
	},
	// 企业ID
	enterprise_id: {
		target: 'enterpriseInfo',
		key: 'id'
	},
	// 企业名称
	enterprise_name: {
		target: 'enterpriseInfo',
		key: 'name'
	},
	// 统一社会信用代码
	unified_credit_code: {
		target: 'enterpriseInfo',
		key: 'unified_credit_code'
	},
	// 组织机构代码
	organization_code: {
		target: 'enterpriseInfo',
		key: 'organization_code'
	},
	// 注册资本
	regist_capital: {
		target: 'enterpriseInfo',
		key: 'regist_capital'
	},
	// 注册地址
	regist_address: {
		target: 'enterpriseInfo',
		key: 'regist_address'
	},
	// 注册时间
	regist_time: {
		target: 'enterpriseInfo',
		key: 'regist_time'
	},
	// 经营（办公）面积
	business_area: {
		target: 'enterpriseInfo',
		key: 'business_area'
	},
	// 经营（办公）地址
	business_address: {
		target: 'enterpriseInfo',
		key: 'business_address'
	},
	// 单位员工总数
	employee_number: {
		target: 'enterpriseInfo',
		key: 'employee_number'
	},
	// 本科以上学历人数
	employee_degree: {
		target: 'enterpriseInfo',
		key: 'employee_degree'
	},
	// 大专学历人数
	employee_junior: {
		target: 'enterpriseInfo',
		key: 'employee_junior'
	},
	// 其他学历人数
	employee_other: {
		target: 'enterpriseInfo',
		key: 'employee_other'
	},
	// 上传附件
	config: {
		target: 'declarationInfo',
		key: 'materials'
	},
};
let contactINfoMap = {
	// 法定代表人
	legal_name: 'legal_represent',
	// 法人手机号
	legal_phone: 'legal_phone',
	// 法人微信号
	legal_wechat: 'legal_wechat',
	// 单位负责人姓名
	charge_name: 'charge_name',
	// 负责人手机号
	charge_phone: 'charge_phone',
	// 负责人微信
	charge_wechat: 'charge_wechat',
	// 联系人姓名
	contact_name: 'contact_name',
	// 联系人手机号
	contact_phone: 'contact_phone',
	// 联系人微信
	contact_wechat: 'contact_wechat',
};

export {
	contactMap,
	enterpriseMap,
	economyMap,
	enterpriseDataMap,
	contactINfoMap
};
