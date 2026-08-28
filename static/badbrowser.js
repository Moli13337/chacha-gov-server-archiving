(function () {
	if (!Array.prototype.indexOf) {
		Array.prototype.indexOf = (function (Object, max, min) {
			'use strict';

			return function indexOf(member, fromIndex) {
				if (this === null || this === undefined) {
					throw TypeError(
						'Array.prototype.indexOf called on null or undefined'
					);
				}

				var that = Object(this),
					Len = that.length >>> 0,
					i = min(fromIndex | 0, Len);

				if (i < 0) { i = max(0, Len + i); } else if (i >= Len) { return -1; }

				if (member === void 0) {
					for (; i !== Len; ++i) { if (that[i] === void 0 && i in that) { return i; } } // undefined
				} else if (member !== member) {
					for (; i !== Len; ++i) { if (that[i] !== that[i]) { return i; } } // NaN
				} else {
					for (; i !== Len; ++i) { if (that[i] === member) { return i; } }
				} // all else

				return -1; // if the value was not found, then return -1
			};
		})(Object, Math.max, Math.min);
	}

	var Browser =
        window.Browser ||
        (function (window) {
        	var document = window.document;

        	var navigator = window.navigator;

        	var agent = navigator.userAgent.toLowerCase();

        	// IE8+支持.返回浏览器渲染当前文档所用的模式
        	// IE6,IE7:undefined.IE8:8(兼容模式返回7).IE9:9(兼容模式返回7||8)
        	// IE10:10(兼容模式7||8||9)

        	var IEMode = document.documentMode;

        	// chorme

        	var chrome = window.chrome || false;

        	var System = {
        		// user-agent
        		agent: agent,
        		// 是否为IE
        		isIE: (/msie/).test(agent),
        		// Gecko内核
        		isGecko: agent.indexOf('gecko') > 0 && agent.indexOf('like gecko') < 0,
        		// webkit内核
        		isWebkit: agent.indexOf('webkit') > 0,
        		// 是否为标准模式
        		isStrict: document.compatMode === 'CSS1Compat',
        		// 是否支持subtitle
        		supportSubTitle: function () {
        			return 'track' in document.createElement('track');
        		},
        		// 是否支持scoped
        		supportScope: function () {
        			return 'scoped' in document.createElement('style');
        		},
        		// 获取IE的版本号
        		ieVersion: function () {
        			try {
        				return agent.match(/msie ([\d.]+)/)[1] || 0;
        			} catch (e) {
        				console.log('error');
        				return IEMode;
        			}
        		},
        		// Opera版本号
        		operaVersion: function () {
        			try {
        				if (window.opera) {
        					return agent.match(/opera.([\d.]+)/)[1];
        				} else if (agent.indexOf('opr') > 0) {
        					return agent.match(/opr\/([\d.]+)/)[1];
        				}
        			} catch (e) {
        				console.log('error');
        				return 0;
        			}
        		},
        		// 描述:version过滤.如31.0.252.152 只保留31.0
        		versionFilter: function () {
        			if (arguments.length === 1 && typeof arguments[0] === 'string') {
        				var version = arguments[0];
        				var start = version.indexOf('.');

        				if (start > 0) {
        					var end = version.indexOf('.', start + 1);

        					if (end !== -1) {
        						return version.substr(0, end);
        					}
        				}
        				return version;
        			} else if (arguments.length === 1) {
        				return arguments[0];
        			}
        			return 0;
        		}
        	};

        	try {
        		// 浏览器类型(IE、Opera、Chrome、Safari、Firefox)
        		System.type = System.isIE ?
        			'IE' :
        			window.opera || agent.indexOf('opr') > 0 ?
        				'Opera' :
        				agent.indexOf('chrome') > 0 ?
        					'Chrome' : // safari也提供了专门的判定方式
        					window.openDatabase ?
        						'Safari' :
        						agent.indexOf('firefox') > 0 ?
        							'Firefox' :
        							'unknow';

        		// 版本号
        		System.version =
                    System.type === 'IE' ?
                    	System.ieVersion() :
                    	System.type === 'Firefox' ?
                    		agent.match(/firefox\/([\d.]+)/)[1] :
                    		System.type === 'Chrome' ?
                    			agent.match(/chrome\/([\d.]+)/)[1] :
                    			System.type === 'Opera' ?
                    				System.operaVersion() :
                    				System.type === 'Safari' ?
                    					agent.match(/version\/([\d.]+)/)[1] :
                    					'0';

        		// 浏览器外壳
        		System.shell = function () {
        			// 遨游浏览器
        			if (agent.indexOf('maxthon') > 0) {
        				System.version =
                            agent.match(/maxthon\/([\d.]+)/)[1] || System.version;
        				return '傲游浏览器';
        			}
        			// QQ浏览器
        			if (agent.indexOf('qqbrowser') > 0 && System.type !== 'IE') {
        				System.version =
                            agent.match(/qqbrowser\/([\d.]+)/)[1] || System.version;
        				return 'QQ浏览器';
        			}

        			// 搜狗浏览器
        			if (agent.indexOf('se 2.x') > 0) {
        				return '搜狗浏览器';
        			}

        			// Chrome:也可以使用window.chrome && window.chrome.webstore判断
        			if (chrome && System.type !== 'Opera') {
        				var external = window.external;

        				var clientInfo = window.clientInformation;

        				// 客户端语言:zh-cn,zh.360下面会返回undefined

        				var clientLanguage = clientInfo.languages;

        				// 猎豹浏览器:或者agent.indexOf("lbbrowser")>0
        				if (external && 'LiebaoGetVersion' in external) {
        					return '猎豹浏览器';
        				}
        				// 百度浏览器
        				if (agent.indexOf('bidubrowser') > 0) {
        					System.version =
                                agent.match(/bidubrowser\/([\d.]+)/)[1] ||
                                agent.match(/chrome\/([\d.]+)/)[1];
        					return '百度浏览器';
        				}
        				// 360极速浏览器和360安全浏览器
        				if (
        					System.supportSubTitle() &&
                            typeof clientLanguage === 'undefined'
        				) {
        					// object.key()返回一个数组.包含可枚举属性和方法名称
        					var storeKeyLen = Object.keys(chrome.webstore).length;

        					var v8Locale = 'v8Locale' in window;

        					return storeKeyLen > 1 ? '360极速浏览器' : '360安全浏览器';
        				}
        				return 'Chrome';
        			}
        			return System.type;
        		};

        		// 浏览器名称(如果是壳浏览器,则返回壳名称)
        		System.name = System.shell();
        		// 对版本号进行过滤过处理
        		System.version = System.versionFilter(System.version);
        	} catch (e) {
        		console.log('error');
        	}
        	return {
        		client: System
        	};
        })(window);

	var browserlist = {
		Chrome: 39,
		IE: 10,
		搜狗浏览器: 48,
		遨游浏览器: 5,
		QQ浏览器: 9,
		猎豹浏览器: 48,
		'360极速浏览器': 48,
		'360安全浏览器': 48
	};

	var tpl = '';

	tpl += '		<div id="browser-blocker" class="browser-blocker-wrapper">';
	tpl += '			<div class="modal">';
	tpl += '				<div class="header">';
	tpl += '					<h1 class="title">您的浏览器需要更新</h1>';
	tpl += '					<p class="message">您可以继续浏览，但可能会无法正常使用</p>';
	tpl += '				</div>';
	tpl += '				<p class="browsers">';
	tpl += '						<a title="Download Google Chrome" href="https://www.google.com/chrome/" target="_blank">';
	tpl += '							<img src="https://cdnjs.cloudflare.com/ajax/libs/browser-logos/46.1.0/chrome/chrome_64x64.png" alt="Google Chrome">';
	tpl += '						</a>';
	tpl += '						<a title="Download Mozilla Firefox" href="https://www.mozilla.org/en-US/firefox/" target="_blank">';
	tpl += '							<img src="https://cdnjs.cloudflare.com/ajax/libs/browser-logos/46.1.0/firefox/firefox_64x64.png" alt="Mozilla Firefox">';
	tpl += '						</a>';
	tpl += '						<a title="Download Opera" href="http://www.opera.com/download" target="_blank">';
	tpl += '							<img src="https://cdnjs.cloudflare.com/ajax/libs/browser-logos/46.1.0/opera/opera_64x64.png" alt="Opera">';
	tpl += '						</a>';
	tpl += '						<a title="Download Safari" href="https://www.apple.com/safari/" target="_blank">';
	tpl += '							<img src="https://cdnjs.cloudflare.com/ajax/libs/browser-logos/46.1.0/safari/safari_64x64.png" alt="Safari">';
	tpl += '						</a>';
	tpl += '						<a title="Download Internet Explorer" href="https://support.microsoft.com/en-us/help/17621/" target="_blank">';
	tpl += '							<img src="https://cdnjs.cloudflare.com/ajax/libs/browser-logos/46.1.0/archive/internet-explorer_9-11/internet-explorer_9-11_64x64.png" alt="Internet Explorer">';
	tpl += '						</a>';
	tpl += '				</p>';
	tpl += '				<div class="footer">';
	tpl += '						<button id="btn-continue" class="button">';
	tpl += '							继续访问';
	tpl += '						</button>';
	tpl += '				<\/div>';
	tpl += '			</div>';
	tpl += '			<div class="backdrop" />';
	tpl += '		</div>';

	var client = Browser.client || {};
	var name = client.name;
	var version = client.version;

	var invalidBrowser = !browserlist[name];
	var isCompatible = browserlist[name] && (browserlist[name] * 1) <= (version * 1);

	if (invalidBrowser || !isCompatible) {
		var onloadCb = function () {
			var text = document.createElement('div');

			text.innerHTML = tpl;
			document.body.appendChild(text);

			var btn = document.getElementById('btn-continue');

			if (btn) {
				if (btn.addEventListener) {
					btn.addEventListener('click', function () {
						document.getElementById('browser-blocker').style.display = 'none';
					});
				} else {
					btn.attachEvent('onclick', function () {
						document.getElementById('browser-blocker').style.display = 'none';
					});
				}
			}
		};

		window.onload = new function () {
			if (!document.body) {
				var pageIsReady = setInterval(function () {
					if (document.body) {
						clearInterval(pageIsReady);

						onloadCb();
					}
				}, 100);
			} else {
				onloadCb();
			}
		}();
	}
})();
