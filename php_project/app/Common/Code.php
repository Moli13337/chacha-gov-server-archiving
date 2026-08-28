<?php

namespace App\Common;

/**
 * 错误码
 */
class Code
{
	const OK = 200; // 请求并处理成功
	const CLIENT_ERROR = 801; //请求错误


	const AUTH_BEARRE_ERROR = 901; // token前缀不对
	const AUTH_TOKEN_EMPTY_ERROR = 902; // token无效
	const AUTH_TOKEN_EXPIRE_ERROR = 903; // token已过期
	const AUTH_PERMISSION_EMPTY_ERROR = 904; // 路由没有设置权限
	
	const LOGIN_PERMISSION_ONE = 910; // 用户被删除
	const LOGIN_PERMISSION_TWO = 911; // 用户还未分配角色
	const LOGIN_PERMISSION_THREE = 912; // 用户对应的角色还未分配权限
	const LOGIN_PERMISSION_FOUR = 913; // 用户无该接口的权限
	const LOGIN_PERMISSION_FIVE = 914; // 用户还未分配菜单权限，请联系管理员
	const LOGIN_PERMISSION_SIX = 915; // 用户还未分配接口权限，请联系管理员
	
	const FAIL = 11000; // 操作失败
	const PARAM_ERROR = 11001; // 请求参数有误
	const DB_ERROR= 11002; // 数据库操作失败
	const SERVER_ERROR= 11003; // 服务器内部错误
	const CHECK_EMPTY_ERROR = 11004; // 不能为空
	const CHECK_UNIQUE_ERROR = 11005; // 不能重复
	const CHECK_LENGTH_ERROR = 11006; // 数据超出长度
	const CHECK_OPERATE_ERROR = 11007; // 操作数据不存在

	const LOGIN_MOBILE_ERROR = 11100; // 账号不能为空
	const LOGIN_MOBILE_LENGTH_ERROR = 11101; // 账号长度不合法
	const LOGIN_MOBILE_EMPTY_ERROR = 11102; // 账号不存在
	const LOGIN_PASSWORD_ERROR = 11103; // 密码不能为空
	const LOGIN_STAFF_EMPTY_ERROR = 11104; // 账号密码不存在
	const LOGIN_STAFF_OPERATE_ERROR = 11105; // 账号暂无绑定企业
	const LOGIN_STAFF_COMPANY_ERROR = 11106; // 企业已过期，请联系管理员续约
	
	const LOGIN_SEND_CODE_ERROR = 11107; // 验证码异常
	const LOGIN_CODE_INVALID_ERROR = 11108; // 验证码无效
	const LOGIN_PASSWORD_MATCH_ERROR = 11109; // 两次密码不一致
	
	const LOGIN_MOBILE_EXIST_ERROR = 11110; // 该账号已存在，请更换
	const LOGIN_OLD_PASSWORD_ERROR = 11111; // 旧密码不正确

	const LOGIN_FORBIDDEN_ERROR = 11112; // 账号被禁用
    const LOGIN_OLD_PASSWORD_REPEAT_ERROR = 11113; // 新旧密码一致，请更改

    const LOGIN_PASSWORD_CONFIRM_ERROR = 11114; // 密码不正确
    const USER_UNBUNDLING_ERROR = 11115; // 解绑超时，请重新开始



    // 登录 注册 12

    // 企业认证 13
    const ENTERPRISE_INPUT_ERROR = 130001;
    const ENTERPRISE_BIZ_ERROR = 130002;
    const ENTERPRISE_NAME_ERROR = 13003;
    const ENTERPRISE_CREDIT_ERROR = 13004;
    const ENTERPRISE_LEGAL_ERROR = 13005;
    const ENTERPRISE_AUTH_FAIL_ERROR = 13006;
    const ENTERPRISE_USER_EXIST_ERROR = 13007;
    const ENTERPRISE_AUTH_ERROR = 13008;
    const ENTERPRISE_NOT_EXIST_ERROR = 13009;
    const ENTERPRISE_QR_READER_ERROR = 13010; // 解析营业执照有误

    const ENTERPRISE_USER_RELATION_NO_CHANGE_ERROR = 14001;


    // 政策解读
    const UNSCRAMBLE_POLICY_EXIST_ERROR = 15001;

    // 审批部门
    const APPROVAL_STAFF_EXIST_ERROR = 21000; // 主审部门人员为空
    const APPROVAL_REPEAT_ERROR = 21001; // 当前审批流程已经存在
    const APPROVAL_TIME_ERROR = 21002; // 协同部门的开始时间必须小于结束时间
    const APPROVAL_TYPE_ERROR = 21003; // 当前审批类型报错 - >部门审核已结束
    const APPROVAL_DEPARTMENT_ERROR = 21004; // 登录人员不是部门的操作人员
    const APPROVAL_MARK_REPEAT_ERROR = 21005; // 您已经填写过延时审批，请勿重复提交
    const APPROVAL_EXIST_ERROR = 21006; // 当前审批不存在
    const APPROVAL_DEPARTMENT_SAME_ERROR = 21007; // 你所属部门与审批部门不一致
    const APPROVAL_PREPARE_CHECKING_ERROR = 21008; // 待系统预处理

    // 内容检查
    const TEXT_CHECK_TITLE_ERROR = 22000; // 标题含有敏感词汇
    const TEXT_CHECK_CONTENT_ERROR = 22001; // 内容含有敏感词汇
    
    // 组织架构
    const RBAC_DEPARTMENT_NAME_UNIQUE_ERROR = 30000; // 部门名称不能重复
    const RBAC_STAFF_MOBILE_UNIQUE_ERROR = 30001; // 电话号码不能重复
    const RBAC_OPERATOR_ONE_UNIQUE_ERROR = 30002; // 操作人员只能有一个
    const RBAC_OPERATOR_ONE_EXIST_ERROR = 30003; // 操作人员已经存在
    const RBAC_OPERATOR_ONE_DELETE_ERROR = 30004; // 操作包含部门操作人员不能删除
    const RBAC_DEPARTMENT_TYPE_UNIQUE_ERROR = 30005; // 部门类型不能重复
    const RBAC_ROLE_NAME_UNIQUE_ERROR = 30006; // 角色名称不能重复
    const RBAC_ROLE_TYPE_NAME_UNIQUE_ERROR = 30007; // 角色组名称不能重复
    const RBAC_SUPER_ADMIN_DELETE_ERROR = 30008; // 该员工为超级管理员，不能删除
    const RBAC_STAFF_DELETE_ERROR = 30009; // 该员工为部门操作员，不能被删除

    const HOME_SMS_CODE_LOGIN_ERROR = 40001; // 该账号未注册，请先注册
    const HOME_SMS_CODE_REGISTER_ERROR = 40002; // 该账号已注册

    const QR_CODE_READER_ERROR = 41001; // 二维码解析失败
    
    const APPLY_DELETE_STATUS_ERROR = 51000; // 申请表不是草稿状态不能删除
    const APPROVAL_SAVE_STATUS_ERROR = 51001; // 审批状态还未结束，不能上传
    const APPROVAL_ENTER_USER_ERROR = 51002; // 申请表的企业管理人员信息被删除

    const OCR_VAT_INVOICE_ERROR = 61001; // ocr 增值税发票识别失败

    const APPLY_SUPPLEMENT_EXIST_ERROR = 21009;// 申报补录不存在

    const AGENT_NOT_EXIST_ERROR = 71001;// 中介机构不存在
    const AGENT_CREDIT_SHOW_NOT_AUDIT_ERROR = 71002;// 审核通过后才可显示
    const AGENT_PUBLISH_CREDIT_SERIOUS_ERROR = 71003;// 严重失信不能上架
    const AGENT_COMPLAINT_STATUS_WAIT_ERROR = 71004;// 已处理，请勿重复处理
    const AGENT_TYPE_DELETE_RESERVED = 71005; // 系统保留服务类型不可删除
    const AGENT_TYPE_DELETE_AGENT = 71006; // 该服务类型已关联了中介
    const AGENT_TYPE_UPDATE_RESERVED = 71007; // 系统保留服务类型不可编辑



    const SHARE_ACTIVITY_NOT_PUBLISH_STATUS = 80001; // 活动未发布
    const SHARE_ACTIVITY_SUBMIT_REPEAT = 80002; // 您已报名，请勿重复报名
    const SHARE_ACTIVITY_OFF = 80003; // 报名已截止
    const SHARE_ACTIVITY_OVER = 80004; // 活动已结束
    const SHARE_ACTIVITY_SUBMIT_FULL = 80005; // 报名人数已满
    const SHARE_ACTIVITY_DELETE_EXIST_USER_ERROR = 80006; // 已有用户报名，不能删除

    const STEWARD_FOLLOW_NO_ERROR = 100001; // 请先关注行业
    const STEWARD_PUSH_OBJ_ERROR = 100003; // 推送标题不存在
    const STEWARD_PUSH_OBJ_STATUS_ERROR = 100004; // 未发布不能推送
    const STEWARD_PUSH_USER_EMPTY_ERROR = 100005; // 推送对象不能为空

    const APPLY_REVOCATION_CANCEL = 21010; // 该申报不能撤销
    const APPLY_CORRECT_SAVE_ERROR = 23001; // 已有正在进行的订正，请勿重复发起
    const APPLY_CORRECT_USER_SAVE_ERROR = 23002; // 您不能提交资料订正
    const APPLY_CORRECT_DEPARTMENT_SAVE_ERROR = 23003; // 你所在部门不能提交资料订正请求
    const APPLY_CORRECT_DEPARTMENT_OPERATOR_ERROR = 23004; // 你所在部门不能操作资料订正
    const APPLY_CORRECT_OPERATOR_STATUS_ERROR = 23005; // 该资料订正的状态不允许变更
    const APPLY_CORRECT_OPERATOR_PASS_ERROR = 23006; // 用户还未提交订正资料
    const APPLY_CORRECT_APPROVAL_ERROR = 23007; // 该申报的订正资料尚有未预处理的发票，请等待发票处理后再次进行审批
    const APPLY_CORRECT_APPROVAL_ONE_ERROR = 23008; // 该申报的订正资料待企业服务中心批准
    const APPLY_CORRECT_APPROVAL_THREE_ERROR = 23009; // 该申报的订正资料待用户提交
    const APPLY_CORRECT_APPROVAL_FOUR_ERROR = 23010; // 该申报的订正资料待企业服务中心审核
    const APPLY_CORRECT_EXIST_ERROR = 23011; // 订正不存在

    const USER_COLLECTION_EXIST_ERROR = 101001; // 您已经收藏
    const USER_COLLECTION_NO_EXIST_ERROR = 101002; // 请先收藏


}