<?php
namespace App\Common;
/**
 * 权限命名规则
 * [资源名称]_[操作]_[操作细分]_[其他]
 * 例如 : TICKET_VIEW , TICKET_VIEW_ALL,REPORT_VIEW_AREA ,REPORT_VIEW_ALL 查看全部区域的报告
 * Class PermissionConstant
 * @package App\Constants
 */
class Permission
{
	
// 	SQL示例
// 	资源类型
// 	insert into `resource_type` (`name`, `alias`, `description`, `updated_at`, `created_at`)
// 	VALUES ('部门管理', 'department-manage', '部门管理', '1559119073', '1559119073');

// 	资源
// 	insert into `resource` (`resource_type_id`, `name`, `alias`, `number`, `description`, `updated_at`, `created_at`)
// 	VALUES ('2', '查看列表', 'role-type-list', '1000006', '查看角色组列表信息', '1559119073', '1559119073');
// 	insert into `resource` (`resource_type_id`, `name`, `alias`, `number`, `description`, `updated_at`, `created_at`)
// 	VALUES ('2', '新增', 'role-typestore', '1000007', '新增角色组信息', '1559119073', '1559119073');
// 	insert into `resource` (`resource_type_id`, `name`, `alias`, `number`, `description`, `updated_at`, `created_at`)
// 	VALUES ('2', '修改', 'role-type-update', '1000008', '修改角色组信息', '1559119073', '1559119073');
// 	insert into `resource` (`resource_type_id`, `name`, `alias`, `number`, `description`, `updated_at`, `created_at`)
// 	VALUES ('2', '删除', 'role-type-delete', '100009', '删除角色组信息', '1559119073', '1559119073');
	
	/***PC端***/
	
	// 不校验权限
	const PERMISSION_ALL = '1000000';
	
	// 员工列表分页 
	const STAFF_VIEW_PAGE = '1000001';
	// 员工列表全部
	const STAFF_VIEW_ALL = '1000002';
	// 员工详情
	const STAFF_VIEW_DETAIL = '1000003';
	// 员工新增
	const STAFF_VIEW_STORE = '1000004';
	// 员工修改
	const STAFF_VIEW_UPDATE = '1000005';
	// 员工删除
	const STAFF_VIEW_DELETE = '1000006';
	
	// 部门列表分页
	const DEPARTMENT_VIEW_PAGE = '1000007';
	// 部门列表全部
	const DEPARTMENT_VIEW_ALL = '1000008';
	// 部门详情
	const DEPARTMENT_VIEW_DETAIL = '1000009';
	// 部门新增
	const DEPARTMENT_VIEW_STORE = '1000010';
	// 部门修改
	const DEPARTMENT_VIEW_UPDATE = '1000011';
	// 部门删除
	const DEPARTMENT_VIEW_DELETE = '1000012';
	// 部门绑定员工
	const DEPARTMENT_VIEW_STAFF_BIND = '1000013';
	// 部门获取员工列表
	const DEPARTMENT_VIEW_STAFF_LIST = '1000014';
	// 部门删除员工
	const DEPARTMENT_VIEW_STAFF_DELETE = '1000015';
	// 部门修改操作人员
	const DEPARTMENT_VIEW_OPERATOR_UPDATE = '1000016';
	
	
	// 角色组列表
	const ROLE_TYPE_VIEW_LIST = '1000017';
	// 角色组新增
	const ROLE_TYPE_VIEW_STORE = '1000018';
	// 角色组修改
	const ROLE_TYPE_VIEW_UPDATE = '1000019';
	// 角色组删除
	const ROLE_TYPE_VIEW_DELETE = '1000020';
	
	// 角色列表
	const ROLE_VIEW_LIST = '1000021';
	// 角色新增
	const ROLE_VIEW_STORE = '1000022';
	// 角色修改
	const ROLE_VIEW_UPDATE = '1000023';
	// 角色删除
	const ROLE_VIEW_DELETE = '1000024';
	// 角色绑定员工
	const ROLE_VIEW_STAFF_BIND = '1000025';
	// 角色获取员工列表
	const ROLE_VIEW_STAFF_LIST = '1000026';
	// 角色删除员工
	const ROLE_VIEW_STAFF_DELETE = '1000027';
	// 角色绑定资源
	const ROLE_VIEW_RESOURCE_BIND = '1000028';
	// 角色绑定接口
	const ROLE_VIEW_API_BIND = '1000029';

	// 资源列表
	const RESOURCE_VIEW_LIST = '1000030';
	// 资源绑定角色
	const RESOURCE_VIEW_ROLE_BIND = '1000031';
	
	// 接口列表
	const API_VIEW_LIST = '1000032';
	// 接口绑定角色
	const API_VIEW_ROLE_BIND = '1000033';
	
	// 更换超级管理员
	const SUPERADMIN_VIEW_CHANGE = '1000034';
	
	// 操作日志列表
	const LOG_VIEW_LIST = '1000035';
	
	
	// 修改手机号发送验证码
	const CENTER_VIEW_MOBILE_UPDATE = '1000036';
	// 修改手机号校验验证码
	const CENTER_VIEW_MOBILE_CHECK = '1000037';
	// 修改密码
	const CENTER_VIEW_MOBILE_PASSWORD = '1000038';
	// 修改头像
	const CENTER_VIEW_MOBILE_PHOTO = '1000039';

	
	/**
	 * 申报
	 */
	// 申报申请表列表
	const APPLY_VIEW_LIST = '1000040';
	// 申报审批详情
	const APPLY_VIEW_DETAIL = '1000041';
	
	
	// 申报审批列表
	const APPROVAL_VIEW_LIST = '1000042';
	// 申报审批详情
	const APPROVAL_VIEW_DETAIL = '1000043';
	// 申报审批发票异常列表
	const APPROVAL_VIEW_EXCEPTION_LIST = '1000044';
	//申报审批受理
	const APPROVAL_VIEW_ACCEPT = '1000045';
	// 申报审批不受理、园区办公室延时拨款、主审部门和协同部门补充资料
	const APPROVAL_VIEW_MARK = '1000046';
	// 申报审批添加协同部门
	const APPROVAL_VIEW_COORDINATE = '1000047';
	// 申报审批需要审计参与和延长审核时间
	const APPROVAL_VIEW_AUDIT = '1000048';
	// 申报审批提交意见(协同部门、主审部门通过、线下会审通过、指挥部审核通过、主审部门不通过、线下会审不通
	const APPROVAL_VIEW_OPINION = '1000049';
	// 需要线下会审
	const APPROVAL_VIEW_OFFLINE = '1000050';
	// 拨款反馈
	const APPROVAL_VIEW_FEEDBACK = '1000051';
	// 申报审核时间配置列表
	const APPROVAL_VIEW_CONFIG_LIST = '1000052';
	// 申报审核时间配置修改
	const APPROVAL_VIEW_CONFIG_UPDATE = '1000053';
	// 待办列表
	const BACKLOG_VIEW_LIST = '1000054';
    // 待办已读
    const BACKLOG_VIEW_READ = '1000055';
    // 受理申报记录
    const WORKBENCH_APPLY_ACCEPT_LIST = '1000056';
    // 项目申报统计
    const WORKBENCH_APPLY_OVERVIEW = '1000057';
    // 政策数据爬取列表
    const BIG_DATA_LIST = '1000058';
    // 政策数据爬取分类
    const BIG_DATA_PARTITION = '1000059';
    // 政策数据爬取删除
    const BIG_DATA_DELETE = '1000060';
    // 政策数据爬取详情
    const BIG_DATA_DETAIL = '1000061';
    // 政策数据爬取待处理列表
    const BIG_DATA_UNHANDLE = '1000062';
    // 宏观政策列表
    const MACRO_POLICY_LIST = '1000063';
    // 宏观政策详情
    const MACRO_POLICY_DETAIL = '1000064';
    // 宏观政策新增
    const MACRO_POLICY_STORE = '1000065';
    // 宏观政策编辑
    const MACRO_POLICY_UPDATE = '1000066';
    // 扶持策列表
    const SUP_POLICY_LIST = '1000067';
    // 扶持政策详情
    const SUP_POLICY_DETAIL = '1000068';
    // 扶持政策新增
    const SUP_POLICY_STORE = '1000069';
    // 扶持政策编辑
    const SUP_POLICY_UPDATE = '1000070';
    // 实施细则列表
    const IMPLE_REGU_LIST = '1000071';
    // 实施细则详情
    const IMPLE_REGU_DETAIL = '1000072';
    // 实施细则新增
    const IMPLE_REGU_STORE = '1000073';
    // 实施细则编辑
    const IMPLE_REGU_UPDATE = '1000074';
    // 政策删除
    const POLICY_DELETE = '1000075';
    // 政策删除
    const POLICY_PUBLISH = '1000076';
    // 政策日志
    const POLICY_LOG = '1000077';
    // 通过政策名称获取政策列表
    const POLICY_LIST_BY_NAME = '1000078';
    // 通过政策ID获取政策简短信息
    const POLICY_DETAIL_BY_ID = '1000079';
    // 没有关联解读的政策
    const POLICY_LIFT_FOR_UNSCRAMBLE = '1000080';
    // 申报通知列表
    const ANNOUNCE_LIST = '1000081';
    // 申报通知详情
    const ANNOUNCE_DETAIL = '1000082';
    // 申报通知新增
    const ANNOUNCE_STORE = '1000083';
    // 申报通知编辑
    const ANNOUNCE_UPDATE = '1000084';
    // 活动公示公告列表
    const PUBLICITY_LIST = '1000085';
    //活动公示公告详情
    const PUBLICITY_DETAIL = '1000086';
    // 活动公示公告新增
    const PUBLICITY_STORE = '1000087';
    // 活动公示公告编辑
    const PUBLICITY_UPDATE = '1000088';
    // 项目列表
    const PROJECT_LIST = '1000089';
    // 项目详情
    const PROJECT_DETAIL = '1000090';
    // 项目新增
    const PROJECT_STORE = '1000091';
    // 项目编辑
    const PROJECT_UPDATE = '1000092';
    // 项目删除
    const PROJECT_DELETE = '1000093';
    // 项目发布
    const PROJECT_PUBLISH = '1000094';
    // 项目日志
    const PROJECT_LOG = '1000095';
    // 政策解读列表
    const UNSCRAMBLE_LIST = '1000096';
    // 政策解读详情
    const UNSCRAMBLE_DETAIL = '1000097';
    // 政策解读新增
    const UNSCRAMBLE_STORE = '1000098';
    // 政策解读编辑
    const UNSCRAMBLE_UPDATE = '1000099';
    // 政策解读删除
    const UNSCRAMBLE_DELETE = '1000100';
    // 政策解读发布
    const UNSCRAMBLE_PUBLISH = '1000101';
    // 政策解读日志
    const UNSCRAMBLE_LOG = '1000102';
    // 第三方查询的企业列表
    const ORGANIZATION_LIST = '1000103';
    // 第三方查询的企业详情
    const ORGANIZATION_DETAIL = '1000104';
    // 企业列表
    const ENTERPRISE_LIST = '1000105';
    // 企业详情
    const ENTERPRISE_DETAIL = '1000106';
    // 企业新增
    const ENTERPRISE_STORE = '1000107';
    // 企业编辑
    const ENTERPRISE_UPDATE = '1000108';
    // 企业发送信息
    const ENTERPRISE_SEND_MESSAGE = '1000109';
    // 企业经营状况
    const ENTERPRISE_BUSINESS = '1000110';
    // 企业税收列表
    const ENTERPRISE_TAX_LIST = '1000111';
    // 企业税收删除
    const ENTERPRISE_TAX_DELETE = '1000112';
    // 企业税收导入
    const ENTERPRISE_TAX_IMPORT = '1000113';
    // 企业人员概览
    const ENTERPRISE_EMPLOYEE_OVERVIEW = '1000114';
    // 企业联系人
    const ENTERPRISE_LINKMAN = '1000115';
    // 企业征信
    const ENTERPRISE_CREDIT = '1000116';
    // 企业申报记录
    const ENTERPRISE_APPLY_LIST = '1000117';
    // 企业申报信息
    const ENTERPRISE_APPLY_INFO = '1000118';
    // 企业申报支持
    const ENTERPRISE_APPLY_SUPPORT = '1000119';

    // 企业用户列表
    const USER_LIST = '1000120';
    // 企业用户详情
    const USER_DETAIL = '1000121';
    // 企业用户删除
    const USER_DELETE = '1000122';
    // 重置企业用户密码
    const USER_RESETPWD = '1000123';
    // 企业用户关联关系
    const USER_ENTERPRISE_RELATION = '1000124';
    // 企业用户禁用
    const USER_FORBIDDEN = '1000125';
    // 企业用户登录日志
    const USER_LOGIN_LOG = '1000126';

    // 反馈列表
    const FEEDBACK_LIST = '1000127';
    // 反馈回复
    const FEEDBACK_REPLY = '1000128';




    // 获取未关联的部门列表
    const DEPARTMENT_UNBIND_LIST_VIEW = '1000129';
    // 获取已经关联的部门列表
    const DEPARTMENT_BIND_LIST_VIEW = '1000130';
    // 部门关联新增
    const DEPARTMENT_BIND_STORE_VIEW = '1000131';
    // 部门关联删除
    const DEPARTMENT_BIND_DELETE_VIEW = '1000132';
	
	// 审核保存部门意见附件
    const APPROVAL_SAVE_FILE_VIEW = '1000133';
    // 审核删除部门意见附件
    const APPROVAL_DELETE_FILE_VIEW = '1000134';

    // 企业用户禁用
    const USER_CHANGE_MOBILE= '1000135';

    // 企业获得支持总览
    const ENTERPRISE_APPLY_SUPPORT_OVERVIEW= '1000136';
    // 项目申报统计（按项目）
    const WORKBENCH_APPLY_OVERVIEW_PROJECT= '1000137';
    // 项目申报统计（按项目）列表
    const WORKBENCH_APPLY_OVERVIEW_PROJECT_LIST= '1000138';
    // 重新预审核
    const APPROVAL_REFRESH_CHECK = '1000139';
    // 项目选择列表
    const PROJECT_LIST_CONDITION = '1000140';
    // 企业选择列表
    const ENTERPRISE_LIST_CONDITION = '1000141';
    // 发票补录新增
    const APPLY_SUPPLEMENT_STORE = '1000142';
    // 发票补录更新
    const APPLY_SUPPLEMENT_UPDATE = '1000143';
    // 发票补录删除
    const APPLY_SUPPLEMENT_DELETE = '1000144';
    // 发票补录列表
    const APPLY_SUPPLEMENT_LIST = '1000145';
    // 发票补录新增发票
    const APPLY_SUPPLEMENT_INVOICE_STORE = '1000146';
    // 发票补录发票列表
    const APPLY_SUPPLEMENT_INVOICE_LIST = '1000147';
    // 发票补录发票更新
    const APPLY_SUPPLEMENT_INVOICE_UPDATE = '1000148';
    // 发票补录发票删除
    const APPLY_SUPPLEMENT_INVOICE_DELETE = '1000149';

    // 中介服务指南新增
    const AGENT_GUID_STORE = '1000150';
    // 中介服务指南详情
    const AGENT_GUID_DETAIL = '1000151';
    // 中介服务指南编辑
    const AGENT_GUID_UPDATE = '1000152';

    // 中介服务通知新增
    const AGENT_NOTIFY_STORE = '1000153';
    // 中介服务通知编辑
    const AGENT_NOTIFY_UPDATE = '1000154';
    // 中介服务通知详情
    const AGENT_NOTIFY_DETAIL = '1000155';
    // 中介服务通知删除
    const AGENT_NOTIFY_DELETE = '1000156';
    // 中介服务通知列表
    const AGENT_NOTIFY_LIST = '1000157';

    // 中介机构新增
    const AGENT_STORE = '1000158';
    // 中介机构编辑
    const AGENT_UPDATE = '1000159';
    // 中介机构详情
    const AGENT_DETAIL = '1000160';
    // 中介机构删除
    const AGENT_DELETE = '1000161';
    // 中介机构列表
    const AGENT_LIST = '1000162';
    // 中介机构更新发布状态
    const AGENT_UPDATE_PUBLISH = '1000163';
    // 中介机构清空记录行为
    const AGENT_CLEAN_CREDIT = '1000164';

    // 中介机构评论新增
    const AGENT_COMMENT_STORE = '1000165';
    // 中介机构评论编辑
    const AGENT_COMMENT_UPDATE = '1000166';
    // 中介机构评论详情
    const AGENT_COMMENT_DETAIL = '1000167';
    // 中介机构评论删除
    const AGENT_COMMENT_DELETE = '1000168';
    // 中介机构评论列表
    const AGENT_COMMENT_LIST = '1000169';
    // 中介机构评论是否展示
    const AGENT_COMMENT_SHOW = '1000170';
    // 中介机构评论是否统计分值
    const AGENT_COMMENT_CALCULATE = '1000171';

    // 中介机构信用行为新增
    const AGENT_CREDIT_STORE = '1000172';
    // 中介机构信用行为编辑
    const AGENT_CREDIT_UPDATE = '1000173';
    // 中介机构信用行为详情
    const AGENT_CREDIT_DETAIL = '1000174';
    // 中介机构信用行为删除
    const AGENT_CREDIT_DELETE = '1000175';
    // 中介机构信用行为列表
    const AGENT_CREDIT_LIST = '1000176';
    // 中介机构信用行为是否展示
    const AGENT_CREDIT_SHOW = '1000177';
    // 中介机构信用行为审核
    const AGENT_CREDIT_AUDIT = '1000178';

    // 中介机构投诉与举报新增
    const AGENT_COMPLAINT_STORE = '1000179';
    // 中介机构投诉与举报编辑
    const AGENT_COMPLAINT_UPDATE = '1000180';
    // 中介机构投诉与举报详情
    const AGENT_COMPLAINT_DETAIL = '1000181';
    // 中介机构投诉与举报删除
    const AGENT_COMPLAINT_DELETE = '1000182';
    // 中介机构投诉与举报列表
    const AGENT_COMPLAINT_LIST = '1000183';
    // 中介机构投诉与举报处理
    const AGENT_COMPLAINT_DISPOSE = '1000184';

    /****管家服务****/
    // 意见征集新增
    const STEWARD_OPINION_STORE = '1000185';
    // 意见征集编辑
    const STEWARD_OPINION_UPDATE = '1000186';
    // 意见征集详情
    const STEWARD_OPINION_DETAIL = '1000187';
    // 意见征集列表
    const STEWARD_OPINION_LIST = '1000188';
    // 意见征集删除
    const STEWARD_OPINION_DELETE = '1000189';
    // 用户意见列表
    const STEWARD_OPINION_USER_LIST = '1000190';
    // 用户意见详情
    const STEWARD_OPINION_USER_DETAIL = '1000191';

    // 信息动态新增
    const STEWARD_INFORMATION_STORE = '1000192';
    // 信息动态编辑
    const STEWARD_INFORMATION_UPDATE = '1000193';
    // 信息动态详情
    const STEWARD_INFORMATION_DETAIL = '1000194';
    // 信息动态列表
    const STEWARD_INFORMATION_LIST = '1000195';
    // 信息动态删除
    const STEWARD_INFORMATION_DELETE = '1000196';

    // 推送新增
    const STEWARD_PUSH_STORE = '1000197';
    // 推送记录列表
    const STEWARD_PUSH_LIST = '1000198';
    // 推送详情
    const STEWARD_PUSH_DETAIL = '1000199';
    // 推送名单
    const STEWARD_PUSH_RECORD = '1000200';

    /***共享空间***/
    // 活动新增
    const SHARE_ACTIVITY_STORE = '1000201';
    // 活动编辑
    const SHARE_ACTIVITY_UPDATE = '1000202';
    // 活动详情
    const SHARE_ACTIVITY_DETAIL = '1000203';
    // 活动列表
    const SHARE_ACTIVITY_LIST = '1000204';
    // 活动删除
    const SHARE_ACTIVITY_DELETE = '1000205';
    // 活动报名列表
    const SHARE_ACTIVITY_APPLY_LIST = '1000206';

    // 用户信息 绑定管家
    const STEWARD_USER_BIND = '1000207';


    /*****pc******/
    // 未登录也可访问的
    const PERMISSION_NO_LOGIN = '900000';

    // 补充资料列表
    const APPLY_MATERIAL_LIST = '1000208';
    // 申报补充资料详情
    const APPLY_MATERIAL_DETAIL = '1000209';

    // 资料订正申请
    const APPROVAL_CORRECT_SAVE = '1000210';

    // 资料订正列表
    const APPROVAL_CORRECT_LIST = '1000211';
    // 资料订正详情
    const APPROVAL_CORRECT_DETAIL = '1000212';
    // 资料订正批准
    const APPROVAL_CORRECT_AGREE = '1000213';
    // 资料订不批准
    const APPROVAL_CORRECT_DISAGREE = '1000214';
    // 资料订正通过
    const APPROVAL_CORRECT_PASS = '1000215';
    // 资料订正重新订正
    const APPROVAL_CORRECT_AGAIN = '1000216';
    // 资料订正作废
    const APPROVAL_CORRECT_INVALID = '1000217';

    // 已选协同部门日志
    const APPLY_SELECT_COORDINATE_LOG = '1000218';
    // 已选协同部门列表
    const APPLY_SELECT_COORDINATE_LIST = '1000219';

    // 保存营业执照
    const ENTERPRISE_SAVE_LICENSE = '1000220';

    // 中介机构
    // 列表
    const AGENT_ENTERPRISE_LIST = '1000221';
    // 上下架
    const AGENT_ENTERPRISE_PUBLISH = '1000222';
    // 删除
    const AGENT_ENTERPRISE_DELETE = '1000223';

    // 中介服务类型配置
    // 列表
    const AGENT_TYPE_LIST = '1000224';
    // 新增一级
    const AGENT_TYPE_STORE = '1000225';
    // 新增二级
    const AGENT_TYPE_STORE_CHILDREN = '1000226';
    // 删除
    const AGENT_TYPE_DELETE = '1000227';
    //更新
    const AGENT_TYPE_UPDATE = '1000228';

    // 统计看板 企业列表
    const WORKBENCH_APPLY_ENTERPRISE_PROJECT = '1000229';

    // 反馈未回复
    const USER_FEEDBACK_TODO_NUMBER = '1000230';

    // 员工权限通知
    const STAFF_ROLE_SEND_SMS = '1000253';
}