import {
	downloadFile
} from './urls.js';
export default {
	methods: {
		downloadFile(url) {
			// downloadFile({
			// 	url: url
			// });
			window.open(downloadFile() + '?url=' + url);
		}
	}
};
