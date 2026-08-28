<?php
// 基础配置
const BASE_NAME = 'wenjiang'; // 业务名称
const BASE_URL = 'https://druid.bgt.work'; // 域名地址
const BASE_BEARER = 'bearer'; // auth的前缀

// 最大分页
const LIMIT_PAGE_SIZE=1000;

// 验证码5分钟有效
const CODE_EXPIRES = 300;

// PC 运营端-token 有效期 1个月
const TOKEN_EXPIRE = 2592000;
// 官网token 有效期 7天
const TOKEN_EXPIRE_HOME = 604800;

// 是否系统保留1否2是
const RESERVED_NO = 1;
const RESERVED_YES = 2;

// 登录的员工信息注入容器key
const LOGIN_STAFF_KEY = 'login_staff_key';
// 登录官网用户
const LOGIN_HOME_KEY = 'login_home_key';
// 登录的员工部门信息注入容器key
const LOGIN_STAFF_DEPARTMENT_KEY = 'login_staff_department_key';

// 审批缓存
const CACHE_APPROVAL_CONFIG = 'cache_approval_config';

// 工作日和周末缓存
const CACHE_ATTENDENCE_EXCEPT = 'cache_attendence_except';

// 审批两个工作日
const APPROVAL_WORK_DAY = 2;

/**运营端常量**/
// 员工编号
const STAFF_NUMBER = 1;

// 政策是否过期
const EXPIRED = [
    'init' => -1, // 未开始
    'no' => 0, // 未过期
    'yes' => 1, // 过期
];

// 申报通知状态 1-申报中 2-即将申报 3-申报已经截止
const ANNOUNCE_STATUS = [
    'enter' => 1,
    'wait' => 2,
    'over' => 3,
];


// 默认排序
const SORT = [
    'desc' => 1, // 降序
    'asc' => 2, // 升序
];

// 发文时间排序
const SORT_PUB = [
    'desc' => 1, // 降序
    'asc' => 2, // 升序
];

// 大数据表 是否处理
const BIG_DATA_HANDLE = [
    'no' => 0, // 未处理
    'yes' => 1, // 已处理
];

// 项目材料 必备
const MATERIALS_NEED = [
    'yes' => 1, // 必备
    'or' => 2, // 据实提供
];

// 项目材料类型 0-补充材料 1-其他材料 2-发票 3-身份证 4-营业执照
const MATERIALS_TYPE = [
    'default' => 0,
    'other' => 1,
    'invoice' => 2,
    'identity' => 3,
    'business' => 4,
];

// 企业和用户的关联 0-未关联 1-关联
const USER_ENTERPRISE_RELATION_STATUS = [
    'no' => 0,
    'yes' => 1,
];

// 纳税统计口径 1-全口径 2-本级
const TAX_TYPE = [
    'all' => 1,
    'local' => 2
];


// 系统消息类型 1-站内信 2-短信
const MESSAGE_TYPE = [
    'message' => 1,
    'sms' => 2,
];

/**官网常量以HOME开头**/

// 重置的初始密码
const USER_INIT_PWD = '88888888';

// 反馈类型
const FEEDBACK_TYPE = [
    'suggest' => 1, // 建议
    'complaint' => 2, // 投诉
    'consult' => 3, // 咨询
];

// 反馈回复
const FEEDBACK_REPLY = [
    'user' => 0, // 默认 0
    'staff' => 1, // 员工回复1
];

// 反馈装态
const FEEDBACK_STATUS = [
    'is' => 1, // 已处理
    'not' => 2, // 待处理
];

// 站内消息类型
const USER_MESSAGE_TYPE = [
    'announce' => 1, // 反馈消息通知
];

// 消息来源类型
/**
 * 来源模块  0 -系统通知 1-反馈 （官网）
 * 2申报受理 3主审部门审核 4协同部门审核 5指挥部决策 6拨款 7审核通知 8申报消息 9协同部门评审完成 10企业补充资料 （运营端）
 * 11申报审核 12打款公示 （官网） 13 中介机构举报或投诉审核
 * 14 申报资料订正请求通知 15 申报资料订正通知 16 资料订正完成通知 17 申报撤回通知
 * 18 申报资料订正通知（官网） 19 申报资料订正生效通知 （官网）20 申报资料重新订正通知（官网）
 * 21 申报资料订正作废通知  22 申报资料订正作废通知 (官网)
 * 23 申报资料订正审核通知
 * 24 员工注册通知
 */
const USER_MESSAGE_SOURCE = [
    'system' => 0, // 系统通知
    'feedback' => 1, // 反馈
    'two' => 2,
	'three' => 3,
	'four' => 4,
	'five' => 5,
	'six' => 6,
	'seven' => 7,
	'eight' => 8,
	'nine' => 9,
	'ten' => 10,
	'eleven' => 11,
	'twelve' => 12,
	'agent_complaint' => 13,
	'apply_correct' => 14,
	'apply_correct_audit' => 15,
	'apply_correct_success' => 16,
	'apply_revocation' => 17,
	'user_apply_correct' => 18,
	'user_apply_correct_success' => 19,
	'user_apply_correct_again' => 20,
	'apply_correct_invalid' => 21,
	'user_apply_correct_invalid' => 22,
	'apply_correct_audit_wait' => 23,
	'staff_register' => 24,
];

// 消息状态 1 未处理 2 已处理
const USER_MESSAGE_READ = [
	'not' => 1, // 待处理
	'is' => 2, // 已处理
];

// 消息指定的用户类型 1-user 2-staff
const MESSAGE_USER_TYPE = [
    'user' => 1,
    'staff' => 2,
];

// 用户禁用
const USER_FORBIDDEN = [
    'no' => 0,
    'yes' => 1,
];

// 图片验证码有效期 分钟数
const CAPTCHA_EXPIRES = 5;

// 登录日志类型
const LOGIN_LOG_TYPE = [
    'user' => 1, // 用户
    'staff' => 2, // 员工
];

// 登录类型
const LOGIN_TYPE = [
    'pc' => 1, // pc登录
    'mini' => 2, // 小程序登录
];

// 对象类型
CONST OBJ_TYPE = [
    'macro_policy' => 1, // 宏观政策
    'sup_policy' => 2, // 扶持政策
    'imple_regu' => 3, // 实施细则
    'announce' => 4, // 申报通知
    'sup_item' => 5, // 扶持条款
    'regu_item' => 6 , // 细则条款
    'publicity' => 7, // 公示信息 活动公示公告
    'project' => 8, // 项目管理
    'unscramble' => 9, // 政策解读
    'approval' => 10, //10 拨款公示公告
    'enterprise' => 11, // 企业
    'user' => 12, //  用户管理
    'feedback' => 13, //  用户反馈
    'information_industry' => 14, //  行业动态
    'information_meeting' => 15, //  会议通知
    'agent' => 16, //  中介
];


/**政策相关**/

// 是否发布
const PUBLISH_STATUS = [
    'yes' => 1, // 发布
    'no' => 0, // 未发布
];



/**redis 常量  以redis开始**/
const REDIS_CAPTCHA = 'captcha:';

// 天眼查基础
const REDIS_TY = 'ty:';
// 天眼查列表
const REDIS_TY_LIST ='ty:list:';
// 天眼查详情
const REDIS_TY_DETAIL ='ty:detail:';

// 企查查查基础
const REDIS_QCC = 'qichacha:';
// 天眼查列表
const REDIS_QCC_LIST ='qichacha:list:';
// 天眼查详情
const REDIS_QCC_DETAIL ='qichacha:detail:';
const REDIS_QCC_DETAIL_FULL ='qichacha:detail:full:';

//八爪鱼token
const REDIS_BAZHUAYU_TOKEN = 'bazhuayu:token';
const REDIS_BAZHUAYU_GROUP = 'bazhuayu:group';


// 状态1草稿2待系统预处理3待受理4不受理5待主审部门审核6线下会审中7待指挥部审核
// 8待拨款9已拨款10主审部门不通过11线下会审不通过12指挥部不通过
const APPLY_STATUS = [
	'one' => 1,
	'two' => 2,
	'three' => 3,
	'four' => 4,
	'five' => 5,
	'six' => 6,
	'seven' => 7,
	'eight' => 8,
	'nine' => 9,
	'ten' => 10,
	'eleven' => 11,
	'twelve' => 12
];

// 发票检查状态1识别失败2假发票3名称重复检查4其他项目使用重复检查
const APPLY_EXCEPTION_TYPE = [
	'one' => 1,
	'two' => 2,
	'three' => 3,
	'four' => 4
];

// 发票检查状态1待检查2异常3正常
const APPLY_CHECK_STATUS = [
	'init' => 1,
	'error' => 2,
	'normal' => 3
];

//经济指标类型1销售收入2总产值3营业收入4主营业务收入5净利润6出口总额7纳税额
const APPLY_ECONOMY_TYPE = [
	'one' => 1,
	'two' => 2,
	'three' => 3,
	'four' => 4,
	'five' => 5,
	'six' => 6,
	'seven' => 7
];

//审批类型1企业服务部2主审部门3协同部门4指挥部5园区办公室
const APPROVAL_TYPE = [
	'one' => 1,
	'two' => 2,
	'three' => 3,
	'four' => 4,
	'five' => 5
];

//审批状态:1待处理2已处理
const APPROVAL_STATUS = [
	'one' => 1,
	'two' => 2
];

// 主审部门的审批类型：审计操作0不需要1需要审计2审计延时
const APPROVAL_AUDIT_TYPE = [
	'NO' => 0,
	'YES' => 1,
	'TIMEOUT' => 2
];

// 配置类型1园区管委会2非审计类主审部门3审计类主审部4审计类延长时间
const APPROVAL_CONFIG_TYPE = [
	'one' => 1,
	'two' => 2,
	'three' => 3,
	'four' => 4
];

//审批理由和补充资料表类型1企业服务不受理2园区办公室延时拨款3主审部门补充资料4协同部门补充资料
const APPROVAL_MARK_TYPE = [
	'one' => 1,
	'two' => 2,
	'three' => 3,
	'four' => 4
];

/**
 * 通知内容 
 * 1 给主审部门发送 2 其他推送部门发送 3 企业服务中心受理-发企业 4 企业服务中心不受理-发企业
 * 5 给协同部门发送通知 6 主审部门补充资料-发企业 7协同部门补充资料-发企业 8协同部门提交意见发送主审部门
 * 9 主审部门通过发送给园区办公室的拨款通知 10主审部门通过-发企业 
 * 11 线下会审通过发送给园区办公室的拨款通知 12线下会审通过-发企业 
 * 13 指挥部通过发送给园区办公室的拨款通知 14指挥部门通过-发企业
 * 15 主审部门通过提交意见发送指挥部  16 主审部门通过提交意见-发企业
 * 17 主审部门不通过-发企业 18 主审部门线下会审不通过-发企业 19 指挥部审核不通过-发企业
 * 20 园区办公室延时拨款-发企业
 * 21 园区办公室拨款反馈-发企业
 * 22 预审核完成 发区企业服务中心
 * 23 主审部门提前通知 发主审部门
 * 24 协同部门提前通知 发协同部门
 * 25 当企业在pc端进行了资料补充后发给主审部门和协同部门
 * 26 园区管委会办公室提前发通知 发办公室
 * 27 主审部门/企业服务中心发起资料订正 发给企业服务中心
 * 28 企业服务中心批准/不批准 发送给发起部门
 * 29 当企业服务中心点击订正通过 发送给发起部门
 * 30 企业撤回申报时 发给企业服务中心
 * 31 资料订正消息 发企业
 * 32 资料重新订正消息 发企业
 * 33 资料订正生效消息 发企业
 * 34 资料订正作废通知 发送给发起部门
 * 35 资料订正作废通知 发企业
 * 36 资料订正待审核通知 发企业服务中心
 */
const APPROVAL_MESSAGE_CONTENT = [
	'one' => 1,
	'two' => 2,
	'three' => 3,
	'four' => 4,
	'five' => 5,
	'six' => 6,
	'seven' => 7,
	'eight' => 8,
	'nine' => 9,
	'ten' => 10,
	'eleven' => 11,
	'twelve' => 12,
	'thirteen' => 13,
	'fourteen' => 14,
	'fifteen' => 15,
	'sixteen' => 16,
	'seventeen' => 17,
	'eighteen' => 18,
	'nineteen' => 19,
	'twenty' => 20,
	'twentyone' => 21,
	'twentytwo' => 22,
	'twentythree' => 23,
	'twentyfour' => 24,
	'twentyfive' => 25,
	'twentysix' => 26,
	'twentyseven' => 27,
	'twentyeight' => 28,
	'twentynine' => 29,
	'thirty' => 30,
	'thirtyone' => 31,
	'thirtytwo' => 32,
	'thirtythree' => 33,
	'thirtyfour' => 34,
	'thirtyfive' => 35,
	'thirtysix' => 36,
];

// 操作类型1操作人员2监督人员3普通人员
const STAFF_OPERTOR_TYPE = [
	'one' => 1,
	'two' => 2,
	'three' => 3
];

// 部门类型1区企业服务中心2普通部门3园区管委会企服中心4指挥部5园区管委会办公室
const DEPARTMENT_TYPE = [
	'one' => 1,
	'two' => 2,
	'three' => 3,
	'four' => 4,
	'five' => 5
];

/**
 * 审批意见
 * opinion_type
 * 1协同部门提交意见
 * 2主审部门审核通过意见、线下会审通过意见、指挥部审核通过提交意见
 * 3主审部门审核不通过意见 、线下会审不通过意见、指挥部审核不通过提交意见
 * 4主审部门提交指挥部填写意见
 */
const APPROVAL_OPTION_TYPE = [
	'one' => 1,
	'two' => 2,
	'three' => 3,
	'four' => 4
];

// 定义新政策的天数
const NEW_DAY = 7;

//  是否 是新
const NEW_STATUS = [
    'is' => 1,
    'not' => 0,
];


// 操作日志类型 1-add 2- update 3-delete
const ACTIVITY_TYPE = [
    'created' => 1,
    'updated' => 2,
    'deleted' => 3,
];

// 操作日志subject类型
const ACTIVITY_SUBJECT_TYPE = [
    'macro_policy' => 1, // 宏观政策
    'sup_policy' => 2, // 扶持政策
    'imple_regu' => 3, // 实施细则
    'announce' => 4, // 申报通知
    'sup_item' => 5, // 扶持条款
    'regu_item' => 6 , // 细则条款
    'publicity' => 7, // 公示信息 活动公示公告
    'project' => 8, // 项目管理
    'unscramble' => 9, // 政策解读
    'approval' => 10, //10 拨款公示公告
    'enterprise' => 11, // 企业
    'user' => 12, //  企业用户管理
    'feedback_suggest' => 13, //  用户建议
    'feedback_complaint' => 14, //  用户投诉
    'feedback_consult' => 15, //  用户咨询
	'department' => 16, // 部门管理
	'staff' => 17, // 员工管理
	'role' => 18, // 角色管理
	'role_type' => 19, // 角色组管理
	'agent' => 20, // 中介机构管理
	'agent_guid' => 21, // 中介服务指南
	'apply' => 22, // 申报
	'apply_supplement' => 23, // 发票补录
	'agent_notify' => 24, // 中介服务通知
	'agent_comment' => 25, // 中介机构用户评价
	'agent_complaint' => 26, // 中介机构投诉
	'agent_credit' => 27, // 中介机构信用行为
	'steward_opinion' => 28, // 管家服务意见征集
	'steward_information' => 29, // 管家服务信息动态
	'steward_push' => 30, // 管家服务推送
	'share_activity' => 31, // 共享空间活动
];

// 企业中心 申报相关列表
const ENTERPRISE_CENTER_APPLY_LIST = [
    'apply' => 1, // 申报记录
    'info' => 2,  // 申报信息
    'support' => 3, // 享受支持情况
];

// 企业联系人 职责
const LINKMAN_DUTY = [
    'legal' => 1, // 法人
    'leader' => 2, // 单位负责人
    'link' => 3, // 联系人
];

// 默认的信用信息 1-信用行为 2-行政处罚信息
const CREDIT_CLASS_FIRST_ID_DEFAULT = 1;
const CREDIT_CLASS_SECOND_ID_DEFAULT = 2;

// 状态1待提交补充材料发送一次提醒2发送二次提醒3已提交补充材料
const MATERIAL_SEND_STATUS = [
	'one' => 1,
	'two' => 2,
	'three' => 3,
];

// 所选日期加上当前的结束时间 即23:59:59
const ADD_END_TIME = 86399;

// 终端设备
const TERMINAL = [
    'web' => 1,
];

// 短信模板
const SMS_TEMPLATE = [
	'one' => 'SMS_171113425',
	'two' => 'SMS_171113427',
	'three' => 'SMS_171113433',
	'four' => 'SMS_171356160',
	'five' => 'SMS_171113448',
	'six' => 'SMS_172007461',
	'seven' => 'SMS_171119116',
	'eight' => 'SMS_171114129',
	'nine' => 'SMS_171119119',
	'ten' => 'SMS_171114150',
	'eleven' => 'SMS_171114152',
	'twelve' => 'SMS_171119131',
	'thirteen' => 'SMS_171119566',
	'fourteen' => 'SMS_171114158',
	'fifteen' => 'SMS_171114160',
	'sixteen' => 'SMS_172012533',
	'seventeen' => 'SMS_171114161',
	'eighteen' => 'SMS_171114162',
	'nineteen' => 'SMS_171119141',
	'twenty' => 'SMS_171119143',
	'twentyone' => 'SMS_171005123',
	'twentytwo' => 'SMS_171114717', // 验证码短信 - 您的验证码是' . $code . '有效期为5分钟，若非本人操作，请勿泄漏
// 	'twentythree' => 23,
// 	'twentyfour' => 24,
// 	'twentyfive' => 25,
// 	'twentysix' => 26,
    'steward_push_project' => 'SMS_177255090', // 项目推送
    'steward_push_information_industry' => 'SMS_177250146', // 行业动态推送
    'steward_push_information_meeting' => 'SMS_177250148', // 会议通知推送
 	'twentyseven' => 'SMS_177538590',
 	'twentyeight' => 'SMS_177543501',
 	'twentynine' => 'SMS_177548486',
 	'thirty' => 'SMS_177548483',
 	'thirtyone' => 'SMS_177548488',
 	'thirtytwo' => 'SMS_179295756',
 	'thirtythree' => 'SMS_179285720',
 	'thirtyfour' => 'SMS_177553520',
 	'thirtyfive' => 'SMS_177543507',
 	'thirtysix' => 'SMS_177543503',
    'staff_register' => 'SMS_181854794', // 后台用户注册
    'staff_send_role' => 'SMS_181862334', // 给员工发送权限通知

];

// 前端发送验证码的判断 1-登录/ 忘记密码  2- 注册
const HOME_SMS_CODE = [
    'login' => 1,
    'register' => 2
];

// 是否下一页
const NEXT_PAGE = [
    'yes' => 1,
    'no' => 2
];

// 用户解绑步骤
const UNBUNDLING_STEP_FIRST = 1;
const UNBUNDLING_STEP_SECOND = 2;

// 创建者字段
const CREATED_STAFF_ID = 'created_staff_id';
// 发布人字段
const PUBLISH_STAFF_ID = 'publish_staff_id';

// 中介机构配置的类型
const AGENT_SETUP_TYPE = [
    'agent' => 1, // 中介服务
    'enterprise' => 2, // 机构入驻登记方式
    'complaint' => 3, // 评价规则
    'supervise' => 4, // 监督管理
];
// ocr 是否成功
const APPLY_EXCEPTION_OCR= [
    'success' => 1,
    'fail' => 2,
];

// 是否一年内发票
const APPLY_EXCEPTION_YEAR = [
    'yes' => 1,
    'not' => 2,
];

// 真假发票
const APPLY_EXCEPTION_TRUTH = [
    'yes' => 1,
    'not' => 2,
    'or' => 3
];

// 重复申报
const APPLY_EXCEPTION_REPEAT_APPLY = [
    'yes' => 1,
    'no' => 2,
];

// 重复
const APPLY_EXCEPTION_REPEAT = [
    'yes' => 1,
    'no' => 2,
];

// 异常状态  1-正常 2-异常 3-检查中（未检查完）
const APPLY_EXCEPTION_STATUS = [
    'success' => 1,
    'fail' => 2,
    'in' => 3,
];

// 不审理时，是否不存档
const MARK_REFRESH = 1;

// 是否展示 1-
const IS_SHOW = [
    'yes' => 1,
    'no' => 0,
];

// 分值是否计算
const IS_CALCULATE = [
    'yes' => 1,
    'no' => 0,
];

// 查询包含删除的数据
const QUERY_TRASHED = 1;

// 失信行为 1 一般失信行为 2 严重失信行为
const AGENT_CREDIT_TYPE = [
    'normal' => 0,
    'general' => 1,
    'serious' => 2,
];

// 审核通过
const IS_AUDIT = [
    'init' => 2,
    'yes' => 1,
    'no' => 3,
];

// 举报  1-有效举报 2-无效举报 3-恶意举报
const AGENT_COMPLAINT_TYPE = [
    'valid' => 1,
    'invalid' => 2,
    'spite' => 3,
];

// 中介投诉  1-已处理 2-待处理
const AGENT_COMPLAINT_STATUS = [
    'wait' => 2,
    'success' => 1,
];

// 中介投诉处理
const IS_DISPOSE = [
    'user' => 0, // 默认 用户
    'staff' => 1, // 1 员工
];

const STARS = [
    'one' => 1,
    'two' => 2,
    'three' => 3,
    'four' => 4,
    'five' => 5,
];

// 服务指南类型 1-中介服务简介 2-机构入驻登记方式 3-评价规则 4-监督管理
const AGENT_GUIDE_TYPE = [
    'one' => 1,
    'two' => 2,
    'three' => 3,
    'four' => 4,
];
// 是否补录申报
const APPLY_SUPPLEMENT = [
    'no' => 0,
    'yes' => 1,
];

// 预审核状态 1-等待 2-处理完成
const PRE_AUDIT_STATUS = [
    'wait' => 1,
    'already' => 2,
];

/********管家服务***/

// 意见类型 1-调查问卷 2-其他
const STEWARD_OPINION_TYPE = [
    'question' => 1,
    'other' => 2,
];

// 信息类型 14-行业动态 15-会议通知
const STEWARD_INFORMATION_TYPE = [
    'industry' => 14,
    'meeting' => 15,
];

// 推送方式 1-根据行业推送 2-推送给全部认证企业 3-推送给全部注册用户
const STEWARD_PUSH_TYPE = [
    'industry' => 1,
    'authentication' => 2,
    'register' => 3,
];

// 推送类型 8-项目申报推荐 14-行业动态推荐 15-会议通知推荐
const STEWARD_PUSH_OBJ_TYPE = [
    'project'   => 8,
    'industry'  => 14,
    'meeting'   => 15,
];

/*****共享空间*****/
// 活动类型 1-普通活动、2-在线培训、3-企业沙龙、4-论坛展会
const SHARE_ACTIVITY_TYPE = [
    'general'  =>  1,
    'training'  =>  2,
    'salon'  =>  3,
    'show'  =>  4,
];

// 活动状态 1-报名中 2-报名已截止 3-已结束
const SHARE_ACTIVITY_STATUS = [
    'on' => 1,
    'off' => 2,
    'over' => 3,
];

// 活动报名状态 1-已报名 2-未报名
const SHARE_ACTIVITY_APPLY_STATUS = [
    'yes' => 1,
    'no' => 2,
];

// 关注的行业类型 1-主行业 2-副行业 3-关注的行业
const USER_FOLLOW_INDUSTRY_TYPE = [
    'main' => 1,
    'vice' => 2,
    'follow' => 3,
];

// 不分页的标识
const NOT_PER_PAGE = -1;
// 关注行业注入的key
const IS_FOLLOW_INDUSTRY = 'is_follow_industry';
// 申报是否可撤销 0-不可撤销 1-撤销
const APPLY_ABLE_REVOCATION = [
    'no' => 0,
    'yes' => 1,
];

//订正 状态 1-待批准 2-不批准 3-待订正 4-待审核 5-订正无效 6-重新订正（这里需要再次生成新的记录 废弃） 7-订正完成 8-订正作废
const APPLY_CORRECT_STATUS = [
    'one' => 1,
    'two' => 2,
    'three' => 3,
    'four' => 4,
    'five' => 5,
    'six' => 6,
    'seven' => 7,
    'eight' => 8,
];

// 对应用户提交的申报的5个步骤 1-草稿 2 企业基本信息  3 项目申报   4 上传附件 5 补充资料
const APPLY_SUBMIT_SETUP = [
    'one' => 1,
    'two' => 2,
    'three' => 3,
    'four' => 4,
    'five' => 5,
];

// 资料订正附件删除
const APPLY_CORRECT_FILE_TYPE = [
    'created' => 1,
    'deleted' => 2,
    'no_change' => 3,
];

// 资料订正 是否有检查的 0-没有 1-待检查 2-检查完毕
const APPLY_CORRECT_IS_CHECK = [
    'no' => 0,
    'yes' => 1,
    'success' => 2,
];

// 中介机构服务是发提交电子材料 0-未提供 1-提供
const AGENT_SUBMIT_MATERIAL = [
    'no' => 0,
    'yes' => 1,
];


// 用户列表排序方式 1 注册时间倒序 2 认证时间倒序
const USER_LIST_ORDER_TYPE = [
    'one' => 1,
    'two' => 2,
];


// 推荐方式 0-系统 1-人员
const PUSH_RECORD_TYPE = [
    'system' => 0,
    'staff' => 1
];
