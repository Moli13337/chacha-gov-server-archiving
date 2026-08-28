import Vue from 'vue';
import storage from '@/utils/storage';
import {
	tencentLogin
} from '@/plugins/helpers.js';

export default ({$axios, redirect, app}) => {
	// 请求超时时间
	$axios.timeout = 3000;
	$axios.onRequest(config => {
		if (!app.$cookies.get('uin')) {
			storage.removeItem('token');
		}
		let token = storage.getItem('token');

		if (token && token != 'undefined') {
			// 让每个请求携带token
			config.headers['Authorization'] = 'bearer ' + token;
		}

		return config;
	});

	$axios.onResponse(response => {
		// 统一处理状态
		const res = response.data;

		if (res.code === 200) {
			return Promise.resolve(res.data);
		}

		if (res.code === 11102) {
			// 账号不存在
			redirect('/register');
			return;
		}

		// 未登录或者登陆失效
		if (res.code === 903 || res.code === 902 || res.code === 901) {
			// 统一修改未登录的提示语
			res.message = '请先登录';
			// 清除登录相关的信息
			storage.removeItem('token');
			storage.removeItem('user_info');
			storage.removeItem('saveData');
			console.log('11111', res);
			// redirect('/login');
			tencentLogin();
			return Promise.reject(res);
		}

		// 未认证企业信息
		if (res.code === 13008) {
			Vue.prototype.$message.error('您暂无该企业权限，请先进行企业认证');
			redirect('/certification');
			return Promise.reject(res);
		}

		return Promise.reject(res);
	});

	$axios.onError(error => {
		const code = parseInt(error.code || (error.response && error.response.status) || -1);

		switch (code) {
			case -1:
				error.message = '网络超时';
				break;
			case 400:
				error.message = '错误请求';
				break;
			case 401:
				error.message = '未授权，请重新登录';
				break;
			case 403:
				error.message = '拒绝访问';
				break;
			case 404:
				error.message = '请求错误,未找到该资源';
				break;
			case 405:
				error.message = '请求方法未允许';
				break;
			case 408:
				error.message = '请求超时';
				break;
			case 413:
				error.message = '文件太大';
				break;
			case 500:
				error.message = '服务器端出错';
				break;
			case 501:
				error.message = '网络未实现';
				break;
			case 502:
				error.message = '网络错误';
				break;
			case 503:
				error.message = '服务不可用';
				break;
			case 504:
				error.message = '网络超时';
				break;
			case 505:
				error.message = 'http版本不支持该请求';
				break;
			default:
				error.message = `${error.message}`;
		}

		if (code === 401) {
			// return redirect('/login');
			tencentLogin();
			return;
		} else {
			return Promise.reject(error);
		}
	});
};
