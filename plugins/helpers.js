import Vue from 'vue';

/**
 * 富文本省略显示
 *
 * @param {*} htmlRichText
 * @param {*} lengthLimit
 * @param {*} ellipsisSuffix
 */
export function richTextToEllipsis(htmlRichText, lengthLimit = 150, ellipsisSuffix = '...') {
	// 剔除空格和图片
	htmlRichText = htmlRichText.replace(/&nbsp;/ig, '').replace(/<img.+>/ig, '');

	if (lengthLimit >= htmlRichText.length) {
		return htmlRichText;
	}

	let htmlSymbolStack = [];
	let wordCounter = 0;
	let index = 0;

	while (wordCounter < lengthLimit && index < htmlRichText.length) {
		let currentChar = htmlRichText[index];

		index++;
		if (currentChar === '<') {
			let htmlTagStart = index;

			// Jump to the end of the tag
			while (htmlRichText[index] !== '>' && index < htmlRichText.length) {
				index++;
			}

			let htmlTagEnd = index++;
			let htmlTagString = htmlRichText.substring(htmlTagStart, htmlTagEnd);

			// If it's not a self-closing tag, add it to stack
			if (!htmlTagString.endsWith('/')) {
				// Check if it's the closed tag
				if (htmlTagString.startsWith('/')) {
					// Check if the top element of the stack is paired.
					if (htmlSymbolStack[htmlSymbolStack.length - 1] === htmlTagString.replace('/', '')) {
						// If it is paired, it will directly pop the top element of the htmlSymbolStack
						htmlSymbolStack.pop();
					} else {
						// Else means, there is a HTML tag pairing syntax problem with the source string
						console.log(
							'Please check the source HTML string, there is a HTML tag pairing syntax problem.'
						);
					}
				} else {
					htmlSymbolStack.push(htmlTagString);
				}
			}
		} else {
			wordCounter++;
		}
	}

	let result = htmlRichText.substring(0, index);

	let closeElement;

	// eslint-disable-next-line no-cond-assign
	while (closeElement = htmlSymbolStack.pop()) {
		if (!htmlSymbolStack.length) {
			result += ellipsisSuffix;
		}
		result += ('</' + closeElement + '>');
	}
	return result;
}

/**
 * 高亮显示金额
 *
 * @param {*} content
 * @param {*} highlightColor
 */
export function highlightMoneyAmount(content, highlightColor = '#3895F1') {
	if (!content) {
		return;
	}

	return content.replace(/(\d+[万|亿])/g, `<span style="color: ${highlightColor}">$1</span>`);
}

// 登录到腾讯云
export function tencentLogin(url) {
	if (!url) {
		url = window.location.href;
	}
	if (url.indexOf('t_auth=1') == -1) {
		if (url.indexOf('?') != -1) {
			url += '&t_auth=1';
		} else {
			url += '?t_auth=1';
		}
	}
	url = encodeURIComponent(url);

	window.open(`https://cloud.tencent.com/login?s_url=${url}`, '_self');
}

let helpers = {
	richTextToEllipsis,
	highlightMoneyAmount,
	tencentLogin
};

export default () => {
	Object.keys(helpers).forEach(key => {
		Vue.prototype[key] = helpers[key];
	});
};
