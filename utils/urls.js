// 代理配置
// const API_HOST = process.env.NODE_ENV === 'development' ? '/api' : process.env.VUE_APP_API;
// const API_HOST = '//service.wenjiang.gov.cn/backend';

// const UPLOAD_HOST = process.env.NODE_ENV === 'development' ? '/api' : process.env.VUE_APP_UPLOAD;
// const UPLOAD_HOST = '//service.wenjiang.gov.cn/upload/api';
// const UPLOAD_HOST = '//upload.service.dev-wenjiang.heroera.com/api';

let API_HOST;
let UPLOAD_HOST;
let downloadApi;

if (process.env.NODE_ENV === 'qa') {
	console.log(process.env.NODE_ENV);
	API_HOST = '//backend-chacha-tencent-qa.d.cloud.tencent.com';
	UPLOAD_HOST = '//upload-chacha-tencent-qa.d.cloud.tencent.com/api';
	downloadApi = '//upload-chacha-tencent-qa.d.cloud.tencent.com/api/download';
} else if (process.env.NODE_ENV === 'development') {
	console.log(process.env.NODE_ENV);
	console.log('API_HOST', API_HOST);
	downloadApi = '//user_pc.frontend.sandbox-wenjiang.hi-coffice.com/upload/api/download';
	API_HOST = '/api';
	UPLOAD_HOST = '//upload-chacha-tencent-dev.d.cloud.tencent.com/api';
} else if (process.env.NODE_ENV === 'dev') {
	console.log(process.env.NODE_ENV);
	API_HOST = '//backend-chacha-tencent-prod.heroera.com';
	UPLOAD_HOST = '//upload-chacha-tencent-prod.heroera.com/api';
	downloadApi = '//upload-chacha-tencent-prod.heroera.com/api/api/download';
} else if (process.env.NODE_ENV === 'sandbox-wenjiang') {
	API_HOST = '//backend.backend.sandbox-wenjiang.hi-coffice.com';
	UPLOAD_HOST = '//upload.service.sandbox-wenjiang.hi-coffice.com/api/upload';
	downloadApi = '//upload.service.sandbox-wenjiang.hi-coffice.com/api/download';
} else if (process.env.NODE_ENV === 'production') {
	console.log(process.env.NODE_ENV);
	API_HOST = '//backend-chacha-tencent-prod.heroera.com';
	UPLOAD_HOST = '//upload-chacha-tencent-prod.heroera.com/api';
	downloadApi = '//upload-chacha-tencent-prod.heroera.com/api/download';
}

/**
 * 上传文件、图片
 */
export const UPLOAD_FILE = UPLOAD_HOST + '/upload';

/**
 * 用户注册
 *
 */
export const USER_REGISTER = API_HOST + '/home/common/register';

/**
 * 请求验证码图片
 *
 */
export const FETCH_CAPTCHACODE = API_HOST + '/home/common/captcha';

/**
 * 忘记密码
 *
 */
export const UPDATE_PASSWORD = API_HOST + '/home/common/forgetPwd';

/**
 * 账号登录
 *
 */
export const USER_ACCOUNT_LOGIN = API_HOST + '/home/common/login';

/**
 * 校验图形验证码
 *
 */
export const CHECK_CAPTCHA_CODE = API_HOST + '/home/common/captcha';

/**
 * 发送短信验证码
 *
 *
 */
export const SEND_MS_CODE = API_HOST + '/home/common/smsCode';

/**
 * 校验短信验证码
 *
 */
export const CHECK_MS_CODE = API_HOST + '/home/common/checkSmsCode';

/**
 * 手机号登录
 *
 */
export const USER_MOBILE_LOGIN = API_HOST + '/home/common/mobileLogin';

/**
 * 企业认证
 *
 */
export const CERTIFICATION = API_HOST + '/home/enterprise/authentication';

/**
 * 政策查询列表
 *
 */
export const FETCH_POLICY_LIST = API_HOST + '/home/policy/list';

/**
 * 查询一级行业
 *
 */
export const FETCH_FIRST_INDUSTRY_LIST = API_HOST + '/home/industry/firstIndustry';

/**
 * 获取未读消息数
 *
 */
export const FETCH_UNREAD_MESSAGE_COUNT = API_HOST + '/home/message/unReadNum';

/**
 * 获取个人中心消息列表
 *
 */
export const FETCH_MESSAGE_LIST = API_HOST + '/home/message/list';

/*
 * 个人中心-消息列表-详情
 *
 */
export const FETCH_MESSAGE_DETAIL = API_HOST + '/home/message/detail';

/**
 * 个人中心-用户信息
 *
 */
export const FETCH_USER_INFO = API_HOST + '/home/user/detail';

/**
 * 个人中心-修改密码
 *
 */
export const CHANGE_PASSWORD = API_HOST + '/home/user/changePassword';

/**
 * 个人中心-修改邮箱
 *
 */
export const CHANGE_EMAIL = API_HOST + '/home/user/changeEmail';

/**
 * 个人中心-解绑手机号-第一步
 *
 */
export const UNBUNGDING_PHONE_FIRST = API_HOST + '/home/user/unbundlingFirst';

/**
 * 个人中心-解绑手机号=第二步
 *
 */
export const UNBUNGDING_PHONE_SECOND = API_HOST + '/home/user/unbundlingSecond';

/**
 * 用户反馈
 *
 */
export const USER_FEED_BACK = API_HOST + '/home/feedback/store';


/**
 * 项目申报-详情
 *
 */
export const DECLARATION_DETAIL = API_HOST + '/home/project/detail';

/**
 * 项目申报-列表
 *
 */
export const FEATCH_DECLARATION_LIST = API_HOST + '/home/project/list';

/**
 * 获取资讯列表
 *
 */
export const FWTCH_INFO_LIST = API_HOST + '/home/information';


/**
 * 最新政策列表
 *
 */
export const FEAT_POLICY_LIST = API_HOST + '/home/index/policy';

/**
 * 政策查询列表-详情
 *
 */
export const QUERY_POLICY_DTAIL = API_HOST + '/home/policy/detail';

/**
 * 政策查询列表-政策详情-政策解读
 *
 */
export const QUERY_POLICY_EXPLAIN = API_HOST + '/home/unscramble/detail';

/**
 * 最新公示公告
 *
 */
export const FEAT_NOTICE_LIST = API_HOST + '/home/index/publicity';

/**
 * 最新申报项目
 *
 */
export const FEAT_PROJECT_LIST = API_HOST + '/home/index/project';

/**
 * 公示公告列表-活动详情
 *
 */
export const PUBLIC_ACTIVE_DETIAL = API_HOST + '/home/publicity/publicity';

/**
 * 公示公告列表-拨款详情
 *
 */
export const APPROVAL_DETIAL = API_HOST + '/home/publicity/approval';

/**
 * 公示公告列表-申报详情
 *
 */
export const ANNOUNCE_DETIAL = API_HOST + '/home/publicity/announce';

/**
 * 公示公告列表
 *
 */
export const QUERY_NOTICE_LIST = API_HOST + '/home/publicity/list';

/**
 * 项目申报-企业信息
 *
 */
export const ENTERPRISE_APPLY_DETAIL = API_HOST + '/home/enterprise/applyDetail';

/**
 * 行业信息
 *
 */
export const FEATCH_INDUSTRY = API_HOST + '/home/industry';

/**
 * 申报详情
 *
 */
export const FETCH_DECLARE_DETAIL = API_HOST + '/home/apply/detail';


/**
 * 申请表列表
 */
export const APPLY_LIST = API_HOST + '/home/apply/list';

/**
 * 申请表详情
 */
export const APPLY_DETAIL = API_HOST + '/home/apply/detail';

/**
 * 保存申请表
 */
export const APPLY_STORE = API_HOST + '/home/apply/store';
/**
 * 修改申请表
 */
export const APPLY_UPDATE = API_HOST + '/home/apply/update';

/**
 * 删除草稿
 */
export const DELETE_DRAFT = API_HOST + '/home/apply/delete';

/**
 * 数据字典
 */
export const APPLY_CONFIG = API_HOST + '/home/config/config';


/**
 * 政策类型
 */

// export const applyProject(params) {
// 	return http.get(API_HOST + '/home/apply/detail', params);
// }
/**
 * 中介服务首页--通知
 */

export const AGENTNOTIFY_LIST = API_HOST + '/home/agentNotify/list';

/**
 * 中介服务详情
 */

export const AGENTNOTIFY_DETAIL = API_HOST + '/home/agentNotify/index';

/**
 * 中介服务指南
 */

export const AGENT_GUIDE = API_HOST + '/home/agent/guide';

/**
 * 中介服务类型
 */

export const AGENT_TYPE = API_HOST + '/home/agent/type';

/**
 * 中介机构列表
 */

export const AGENT_LIST = API_HOST + '/home/agent/list';

/**
 * 中介机构详情
 */

export const AGENT_DETAIL = API_HOST + '/home/agent/detail';


/**
 * 中介机构评论列表
 */
export const AGENT_COMMENT_LIST = API_HOST + '/home/agent/comment';

/**
 * 中介机构评信用行为列表
 */
export const AGENT_CREDIT = API_HOST + '/home/agent/credit';

/**
 * 中介机构评论
 */
export const AGENT_CMMENT = API_HOST + '/home/agent/comment';

/**
 * 中介机构投诉
 */
export const AGENT_COMPLAINT = API_HOST + '/home/agent/complaint';


/**
 * 中介机构异常列表
 */
export const CREDIT_LIST = API_HOST + '/home/agent/credit/list';

/**
 * 中介
 * 四项
 */
export const AGENTSETUP_DETAIL = API_HOST + '/home/agent/guide';


/**
 * 通知详情
 *
 */
export const NOTICE_DETAIL = API_HOST + '/home/agentNotify/index';

export const MOLD_TYPE_LIST = API_HOST + '/home/mold';


/**
 * 管家服务-项目推送-小喇叭
 *
 */

export const PROJECT_PUSH_TRUMPET = API_HOST + '/home/steward/push/trumpet';


export const USER_ENTERPRISE = API_HOST + '/home/user/enterprise';
/**
 * 管家服务-关注行业详情
 *
 */
export const FLLOW_INDUSTRY_DETAIL = API_HOST + '/home/user/follow/industry';

/**
 * 管家服务-关注行业
 *
 */
export const FLLOW_INDUSTRY = API_HOST + '/home/user/follow/industry/save';

/**
 * 管家服务-取消关注
 *
 */
export const DELETE_CONCER_INDUSTRY = API_HOST + '/home/user/follow/industry/delete';


/**
 * 管家服务-项目申报
 *
 */
export const PROJECT_RECOMMEND = API_HOST + '/home/steward/project/list';


/**
 * 管家服务-动态信息
 *
 */
export const INFORMATION_LIST = API_HOST + '/home/steward/information/list';

/**
 * 管家服务-动态信息
 *
 */
export const INFORMATION_DETAIL = API_HOST + '/home/steward/information';


/**
 * 管家服务-动态信息详情
 *
 */
export const OPTION_DETAIL = API_HOST + '/home/steward/opinion';
/**
 * 管家服务-行业
 *
 */
export const INDUSTRY_LIST = API_HOST + '/home/industry/v2';

/**
 * 管家服务-征集列表
 *
 */
export const OPTION_LIST = API_HOST + '/home/steward/opinion/list';

/**
 * 管家服务-提交意见
 *
 */
export const OPTION_SUBMIT = API_HOST + '/home/steward/opinion/submit';

/**
 * 管家服务-问题反馈
 *
 */
export const FEEDBACK_LIST = API_HOST + '/home/feedback/list';

/**
 * 管家服务-企业支出情况列表
 *
 */
export const APPLY_SUPPORT_LIST = API_HOST + '/home/steward/apply/support/list';

/**
 * 管家服务-企业支出概览
 *
 */
export const SUPPORT_OVERVIEW = API_HOST + '/home/steward/apply/support/overview';

/**
 * 共享空间-列表
 *
 */
export const ACTIVITY_LIST = API_HOST + '/home/share/activity/list';

/**
 * 活动报名
 *
 */
export const ACTIVITY_SUBMIT = API_HOST + '/home/share/activity/submit';


/**
 * 共享空间-活动详情
 *
 */
export const ACTIVITY_DETAIL = API_HOST + '/home/share/activity';

/**
 * 收藏
 *
 */
export const COLLECTION_SAVE = API_HOST + '/home/user/collection/save';

/**
 * 取消收藏
 *
 */
export const COLLECTION_CANCEL = API_HOST + '/home/user/collection/cancel';


/**
 * 收藏列表
 *
 */
export const FETCH_COLLECTION_LIST = API_HOST + '/home/user/collection/list';

/**
 * 订正记录列表
 *
 */
export const FETCH_COREECT_LIST = API_HOST + '/home/apply/correct/list';


/**
 * 订正记录详情
 *
 */
export const FETCH_COREECT_DETAIL = API_HOST + '/home/apply/correct/detail';

/**
 * 撤销申报
 *
 */
export const CANCEL_APPLY_REVOCATION = API_HOST + '/home/apply/revocation';


/**
 * 订正申报详情
 *
 */
export const CORRECT_APPLY_DETAIL = API_HOST + '/home/apply/correct/apply/detail';


/**
 * 订正更新
 *
 */
export const CORRECT_UPDATE = API_HOST + '/home/apply/correct/update';

// 中介服务

export const AGENT_TYPE_RESERVED = API_HOST + '/home/agent/type/reserved';

export function downloadFile() {
	return downloadApi;
}

