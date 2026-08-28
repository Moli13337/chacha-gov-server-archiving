import storage from '@/utils/storage';
import routers from '@/config/routes';
import {
	USER_ACCOUNT_LOGIN
} from '@/utils/urls';
import {
	tencentLogin
} from '@/plugins/helpers.js';


export default async function ({app, route}) {
	// 判断当前页面需要权限
	// let item = routers.find(item => item.path === route.path || item.name === route.name);
	let _cookie = app.$cookies.get('uin');

	// 判断是否是从腾讯过来的页面 query t_auth = 1;
	if (route && route.query && route.query.t_auth == 1) {
		if (!_cookie) {
			tencentLogin();
			return false;
		} else {
			let params = {
				uid: _cookie,
				type: 1
			};

			await app.$axios.post(USER_ACCOUNT_LOGIN, params).then(({token}) => {
				token && storage.setItem('token', token);
				return true;
			}).catch(res => {
				console.log(res);
			});
		}
	} else {
		// if (item && item.needAuth) {
		// 	if (!_cookie) {
		// 		tencentLogin();
		// 		return false;
		// 	} else {
		// 		return true;
		// 	}
		// }
	}
}
