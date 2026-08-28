import Vue from 'vue';
import fecha from 'fecha';
import moment from 'moment';

/**
 * 时间格式化
 *
 * @param {number|string} timestamp 时间戳
 * @param {string} format 格式
 */
export function formatDate(timestamp, format = 'YYYY/MM/DD') {
	let time = timestamp;

	// 校验参数
	if (!time || isNaN(time)) {
		return '';
	}

	// 如果传进来的是9位的时间戳
	if (time.toString().length === 10) {
		time = time + '000';
	}
	// 如果到这一步，依然不是13位的时间戳，说明数据有问题
	time = parseInt(time);
	if (time.toString().length != 13) {
		return '';
	}

	return fecha.format(time, format);
}

// 默认 YYYY-MM-DD
export function formatTime(val, format) {
	if (val) {
		return moment(new Date(val * 1000)).format(format ? format : 'YYYY-MM-DD');
	} else {
		return '';
	}
}


export function formatDeclara(status) {
	switch (status) {
		case 1:
			return '草稿';
		case 2:
			return '待系统预处理';
		case 3:
			return '待受理';
		case 4:
			return '不受理';
		case 5:
			return '待主审部门审核';
		case 6:
			return '线下会审中';
		case 7:
			return '待指挥部审核';
		case 8:
			return '待拨款';
		case 9:
			return '已拨款';
		case 10:
			return '主审部门不通过';
		case 11:
			return '线下会审不通过';
		case 12:
			return '指挥部不通过';
		default:
	}
}


let filters = {
	formatDate,
	formatDeclara,
	formatTime
};

Object.keys(filters).forEach(key => {
	Vue.filter(key, filters[key]);
});

export default filters;
