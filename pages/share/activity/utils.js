import {
	chd_Information,
	chd_declare,
	chd_enterprise_service,
	chd_enterprise,
	chd_innovate,
	chd_reform,
	chd_science,
	chd_sww,
	chd_zbjjpt,
	sj_Information,
	sj_businesshall,
	sj_development_reform,
	sj_enterprise,
	sj_affairs,
	sj_government,
	sj_kjt,
	sj_tzxm,
	sj_scipspc,
	chd_government,
	qj_jinwenjiang,
	city_fgw,
	city_gov,
	city_hbgj,
	city_jxj,
	city_kct,
	city_kjj,
	city_qyfw,
	city_swj,
	city_zxqyw,
	province_gov,
	province_fgw,
	province_jxj,
	province_kjt,
	province_szwfw,
	province_zscq,
	province_zxqy,
	wj_rs,
	wj_wrs,
	wj_yxc,
	ewm_service,
	ewm_jinwenjiang,
	ewm_jx,
	ewm_innovate

} from '~/assets/images';

export const municipalLinks = [
	{
		title: '成都市人民政府门户网站',
		img: chd_government,
		link: 'http://www.chengdu.gov.cn/',
		codeimg: city_gov,
	},
	{
		title: '成都市经济和信息化局',
		img: chd_Information,
		link: 'http://cdjx.chengdu.gov.cn/',
		codeimg: city_jxj,
	},
	{
		title: '成都市企业服务平台',
		img: chd_enterprise_service,
		link: 'http://qyfw.cdjx.chengdu.gov.cn/',
		codeimg: city_qyfw,
	},
	{
		title: '成都市和发展改革委员会',
		img: chd_reform,
		link: 'http://cddrc.chengdu.gov.cn/',
		codeimg: city_fgw,
	},
	{
		title: '成都市商务局',
		img: chd_sww,
		link: 'http://sww.chengdu.gov.cn/',
		codeimg: city_swj,
	},
	{
		title: '总部经济信息平台',
		img: chd_zbjjpt,
		link: 'http://sww.chengdu.gov.cn/cdswh/c118687/zbjjpt.shtml',
		codeimg: '',
	},
	{
		title: '成都市科学技术局',
		img: chd_science,
		link: 'http://cdst.chengdu.gov.cn/',
		codeimg: city_kjj,
	},
	{
		title: '成都市科技项目申报系统',
		img: chd_declare,
		link: 'http://kjxm.cdst.chengdu.gov.cn/egrantweb/',
		codeimg: province_gov,
	},
	{
		title: '成都市创新创业服务平台',
		img: chd_innovate,
		link: 'http://www.cdkjfw.com//',
		codeimg: city_kct,
	},
	{
		title: '成都中小企业',
		img: chd_enterprise,
		link: 'http://www.cdsme.com/',
		codeimg: city_zxqyw,
	}
];

export const provincialLinks = [
	{
		title: '四川省人民政府门户网站',
		img: sj_government,
		link: 'http://www.sc.gov.cn/',
		codeimg: province_gov,
	},
	{
		title: '四川省经济和信息化厅',
		img: sj_Information,
		link: 'http://jxt.sc.gov.cn/',
		codeimg: province_jxj,
	},
	{
		title: '四川省发展和改革委员会',
		img: sj_development_reform,
		link: 'http://fgw.sc.gov.cn/',
		codeimg: province_fgw,
	},
	{
		title: '四川省商务厅',
		img: sj_businesshall,
		link: 'http://swt.sc.gov.cn/',
		codeimg: province_gov,
	},
	{
		title: '四川省科学技术厅',
		img: sj_kjt,
		link: 'http://kjt.sc.gov.cn/',
		codeimg: province_kjt,
	},
	{
		title: '四川省政务服务网',
		img: sj_affairs,
		link: 'http://www.sczwfw.gov.cn',
		codeimg: province_szwfw,
	},
	{
		title: '四川省投资项目在线审批监管平台',
		img: sj_tzxm,
		link: 'http://tzxm.sczwfw.gov.cn/',
		codeimg: '',
	},
	{
		title: '四川高新中小企业服务中心',
		img: sj_enterprise,
		link: 'http://www.htsme.com/',
		codeimg: province_zxqy,
	},
	{
		title: '四川省知识产权服务促进中心',
		img: sj_scipspc,
		link: 'http://scipspc.sc.gov.cn/',
		codeimg: province_zscq,
	}
];
export const qrCodeList = [
	{
		title: '温江创新',
		img: ewm_innovate,
	},
	{
		title: '温江经信',
		img: ewm_jx,
	},
	{
		title: '温江金信',
		img: ewm_jinwenjiang,
	},
	{
		title: '温江服务',
		img: ewm_service,
	},
	{
		title: '温江医学城',
		img: wj_yxc,
	},
	{
		title: '温江微人社',
		img: wj_wrs,
	},
	{
		title: '温江人社',
		img: wj_rs,
	},
];

export const districtLinks = [
	{
		title: '金温江环保管家一站式服务平台',
		img: qj_jinwenjiang,
		link: 'http://www.huanbaoguanjia.vip/',
		codeimg: city_hbgj,
	}
];
export const timeSlot = [
	{
		value: '',
		label: '全部',
	},
	{
		value: 1,
		label: '今天',
	},
	{
		value: 2,
		label: '明天',
	},
	{
		value: 3,
		label: '本周',
	},
	{
		value: 4,
		label: '本月',
	}
];

export const activityStatus = [
	{
		value: '',
		label: '全部',
	},
	{
		value: 1,
		label: '报名中',
	},
	{
		value: 2,
		label: '报名已截止',
	},
	{
		value: 3,
		label: '已结束',
	}
];
export const applyStatus = [
	{
		value: '',
		label: '全部',
	},
	{
		value: 1,
		label: '已报名',
	},
	{
		value: 2,
		label: '未报名',
	},
];

export default {
	municipalLinks,
	districtLinks,
	provincialLinks,
	applyStatus,
	activityStatus,
	timeSlot,
	qrCodeList
};
