/*
 *  文件上传 -- 未测试
 *  url:请求地址
 *  params:参数
 * */
export function fileUpload(axios, url, params = {}) {
	return new Promise((resolve, reject) => {
		axios({
			url: url,
			method: 'post',
			data: params,
			headers: {'Content-Type': 'multipart/form-data'}
		}).then(response => {
			resolve(response);
		}).catch(error => {
			reject(error);
		});
	});
}

/**
 * 文件下载
 *
 * @param {*} url
 * @param {*} params
 * @param {*} title
 */
export function fileDownLoad(axios, url, params = {}, title) {
	return new Promise((resolve, reject) => {
		axios({
			url,
			method: 'post',
			data: params,
			headers: {'Content-Type': 'application/json'},
			responseType: 'arraybuffer'
		}).then(res => {
			let headers = res.headers;
			let blob = new Blob([res.data], {
				type: headers['content-type']
			});
			let link = document.createElement('a');

			link.href = window.URL.createObjectURL(blob);
			if (!title) {
				const fileName = headers['content-disposition'];

				title = fileName.includes('filename=') ? fileName.split('=')[1] : '下载的表单文件';
			}
			link.download = title;
			link.click();
			resolve();
		}).catch(error => {
			reject(error);
		});
	});
}

export default {
	fileUpload,
	fileDownLoad
};
