<template>
	<header id="header-wapper">
		<div class="header-tips">
			<div class="tips-cotainer">
				<p class="tip">
					<span>欢迎光临！</span>
				</p>
				<!-- <div class="statement-cotainer">
					<img
						class="statement-img"
						src="~assets/images/ic-national_emblem@2x.png"
					>
					<span class="statement-text">产服通智慧企业服务平台</span>
				</div> -->
			</div>
		</div>
		<div class="header-logo">
			<div class="logo-cotainer">
				<div class="logo-image-box">
					<div 	class="logo">
						产服通智慧企业服务平台
					</div>
					<p>(公测版)</p>
				</div>
				<ul class="user-container">
					<li>
						<img
							class="ic-shouji"
							src="~/assets/images/home-ic-shouji@2x.png"
						>
					</li>
					<li>
						<el-popover
							ref="popover"
							placement="bottom"
							trigger="hover"
						>
							<div>
								<img
									src="../../assets/images/erweima.png"
									class="popover-img"
									style="margin:0; width:124px; height:111px"
								>
								<p
									class="popover-tip"
									text-align="center"
									style="text-align:center; padding-top: 10px;"
								>关注微信公众号</p>
							</div>
						</el-popover>
						<img
							class="ic-weixin"
							src="~/assets/images/home-ic-weixin@2x.png"
						>
					</li>
					<template v-if="!isLogin">
						<!-- <li>
							<p
								class="tip tip-login"
								@click="handleToLogin"
							>登录</p>
						</li> -->
						<li>
							<p
								class="tip tip-register"
								@click="goTencent"
							>登录</p>
						</li>
					</template>
					<template v-else>
						<li>
							<p
								class="tip tip-login clear-login"
								@click="handleLogout"
							>退出登录</p>
						</li>
					</template>
				</ul>
			</div>
		</div>
		<!-- 导航栏 -->
		<div class="nav-bar">
			<ul class="nav-cotainer">
				<li
					class="nav-item"
					:class="{'active': item.path === path || (item.path !== '/' && path.startsWith(item.path))}"
					v-for="(item, index) in navList"
					:key="index"
				>
					<div
						@click="handlePath(item.path)"
					>
						{{item.title}}
						<el-badge
							:is-dot="haveUnreadMessage"
							v-if="item.title == '个人中心'"
						>
						</el-badge>
					</div>
				</li>
			</ul>
		</div>

		<div
			:class="{
				'banner-container': true,
				'home-banner-container': path === '/',
			}"
			v-if="isShowBanner"
		>
		</div>

		<!-- 搜索框 -->
		<div
			v-if="isShowSearch"
			class="search-container"
		>
			<div
				class="search-box"
				:class="{'search-box-bg': path === '/agent'}"
				v-if="path !== '/'"
			>
				<div class="input-box">
					<el-input
						class="search"
						:placeholder="searchPlaceHolder"
						v-model="searchContent"
						@keyup.enter.native="handleSearch"
					>
						<el-button
							slot="append"
							icon="el-icon-search"
							@click="handleSearch"
						/>
					</el-input>
				</div>
			</div>
		</div>
		<!-- 关注行业 -->
		<industry-edit-dialog
			:visible.sync="industryDialogVisible"
			:industryOptions="industryOptions"
			:industryDetail="industryDetail"
		/>
	</header>
</template>
<script>
import {
	FETCH_UNREAD_MESSAGE_COUNT,
	FLLOW_INDUSTRY_DETAIL,
	INDUSTRY_LIST,
	USER_ACCOUNT_LOGIN
} from '@/utils/urls';
import storage from '@/utils/storage';
import IndustryEditDialog from '@/components/butler/industry-edit-dialog';

const hasSearchPaths = ['/policy', '/notice', '/declaration', '/agent', '/butler', '/butler/enterprise_collect', '/butler/project_push', '/butler/support_info', '/share'];

export default {
	components: {
		IndustryEditDialog
	},
	data() {
		return {
			industryDialogVisible: false,
			industryOptions: [],
			industryDetail: {},
			path: '',
			searchContent: '',
			unreadMessageCount: 0,
			butlerPath: [
				'/butler/enterprise_collect',
				'/butler/project_push',
				'/butler/problem_feedback',
				'/butler/support_info',
				'/butler/enterprise_info',
				'/butler/industry_concer'
			]
		};
	},
	created() {
		this.showIndustryDialogIfNeed();
	},
	computed: {
		isShowSearch() {
			return hasSearchPaths.includes(this.path);
		},
		isShowBanner() {
			return this.path === '/' || this.path === '/personal' || this.path === '/personal/record' || this.path === '/personal/mine';
		},
		isLogin() {
			return storage.getItem('token');
		},
		searchPlaceHolder() {
			if (this.path === '/') {
				return '搜索政策、通知、申报、活动';
			} else if (this.path === '/policy') {
				return '搜索你要找的政策';
			} else if (this.path === '/notice') {
				return '搜索公示公告名称';
			} else if (this.path === '/declaration') {
				return '搜索项目名称';
			} else if (this.path === '/agent') {
				return '搜索机构名';
			} else if (this.path === '/butler/enterprise_collect') {
				return '搜索企业意见征集';
			} else if (this.path === '/butler') {
				return '搜索申报项目、行业动态、会议通知';
			} else if (this.path === '/butler/support_info') {
				return '搜索项目';
			} else if (this.path === '/share') {
				return '搜索活动';
			}

			return '';
		},
		// 是否有未读消息
		haveUnreadMessage() {
			return this.unreadMessageCount > 0;
		},
		navList() {
			let navList = [
				{
					title: '首页',
					path: '/',
				},
				{
					title: '政策查询',
					path: '/policy'
				},
				{
					title: '公示公告',
					path: '/notice'
				},
				{
					title: '政策申报',
					path: '/declaration'
				},
				{
					title: '管家服务',
					path: '/butler'
				},
				{
					title: '中介服务',
					path: '/agent'
				},
				{
					title: '共享空间',
					path: '/share'
				}
			];

			if (this.isLogin) {
				navList.push({
					title: '个人中心',
					path: '/personal'
				});
			}

			return navList;
		}
	},
	methods: {
		// 是否需要显示行业弹窗
		showIndustryDialogIfNeed() {
			if (!this.isLogin) {
				return;
			}

			if (this.path !== '/butler') {
				// 非管家服务页面不能重复打开弹窗
				if (sessionStorage.isShownIndustryDialog) {
					return;
				}
			} else {
				// 管家服务页面不能重复打开弹窗
				if (sessionStorage.isShownIndustryDialogInButler) {
					return;
				}
			}
			// 查询网络数据，检查是否已关注行业
			this.$axios.get(FLLOW_INDUSTRY_DETAIL)
				.then(res => {
					let industryInfo = res || {};

					// 未关注行业需要打开弹窗
					if (!industryInfo.main || !industryInfo.main.id) {
						this.showIndustryDialog(industryInfo);
					}
				}).then(error => {
					console.log(error);
				});
		},
		// 显示行业关注弹窗
		showIndustryDialog(industryInfo) {
			// 先查询出行业选项列表再打开弹窗
			this.$axios.get(INDUSTRY_LIST)
				.then(industryOptions => {
					// 记录打开状态
					sessionStorage.isShownIndustryDialog = true;
					if (this.path === '/butler') {
						sessionStorage.isShownIndustryDialogInButler = true;
					}
					// 显示弹窗
					this.industryDialogVisible = true;
					this.industryOptions = industryOptions || [];
					this.industryDetail = industryInfo || {};
				}).catch(error => {
					console.log(error);
				});
		},
		// 处理模块跳转
		handlePath(path) {
			this.$router.push(path);
			this.searchContent = '';
		},
		// 获取未读的消息数
		fetchUnreadMessageCount() {
			this.$axios.get(FETCH_UNREAD_MESSAGE_COUNT)
				.then(unreadCount => {
					this.unreadMessageCount = unreadCount;
					this.$bus.emit('onUnreadCountChange', unreadCount);
				})
				.catch(error => {
					console.log(error);
				});
		},
		// 登录
		handleToLogin() {
			// this.$router.push({name: 'login'});
			this.tencentLogin();
		},
		// 登出
		handleLogout() {
			storage.removeItem('token');
			storage.removeItem('user_info');
			this.$cookies.remove('uin', {domain: '.cloud.tencent.com'});
			// if (this.$route.name == 'index') {
			// 	window.location.reload();
			// } else {
			// 	this.$router.push({name: 'index'});
			// }
			window.location.href = '/';
		},
		// 注册
		handleRegister() {
			this.$router.push({name: 'register'});
		},
		// 搜索
		handleSearch() {
			let keyword = this.searchContent && this.searchContent.trim();
			let query = keyword ? {keyword} : {};


			if (this.path == '/agent') {
				this.$router.push({
					path: '/agent/evaluation_list',
					query
				});
			} else if (this.path == '/share') {
				this.$router.push({
					path: '/share/activity',
					query
				});
			} else {
				this.$router.push({
					path: this.path,
					query
				});
			}
		},
		// 处理刷新后添加斜杠，导航不高亮问题
		handleRoutePath(path) {
			let handledPath = path.replace(/\/$/gi, '');

			this.path = handledPath || '/';
		},
		goTencent() {
			this.tencentLogin();
		}
	},
	watch: {
		$route(to) {
			this.handleRoutePath(to.path);
			this.showIndustryDialogIfNeed();
			// 刷新时清空输入框内容
			this.searchContent = this.$route.query.keyword;
		}
	},
	async mounted() {
		// 刷新时绑定路由路径
		this.handleRoutePath(this.$route.path);

		// 根据腾讯云cookies登录逻辑;
		// let _cookies = this.$cookies.get('uin');

		// console.log(_cookies);
		// if (_cookies) {
		// 	let params = {
		// 		uid: _cookies,
		// 		type: 1
		// 	};

		// 	await this.$axios.post(USER_ACCOUNT_LOGIN, params).then(({token}) => {
		// 		if (token) {
		// 			storage.setItem('token', token);
		// 		}
		// 	}).catch(res => {
		// 		console.log(res);
		// 	});
		// }

		// 已登录需要获取未读消息数
		if (this.isLogin) {
			this.fetchUnreadMessageCount();
		}

		// 监听未读消息数更新
		this.$bus.on('onUnreadCountChange', (unreadCount) => {
			this.unreadMessageCount = unreadCount;
		});

		// 搜索时如果keyword还存在，变回显搜索内容
		this.$bus.on('changeSearchContent', (keyword) => {
			this.searchContent = keyword;
		});
	}
};
</script>


<style lang="less" scope>
@import "~assets/css/common_avairail";
#header-wapper {
  margin-bottom: 20px;
  .header-tips {
    width: 100%;
    background: @menuHoverBgColor;
    height: 49px;
    min-width: 1162px;
    .tips-cotainer {
      width: 1024px;
      height: 100%;
      min-width: 1162px;
      margin: auto;
      display: flex;
      justify-content: space-between;
      align-items: center;
      .statement-cotainer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        .statement-img {
          width: 26px;
          height: 26px;
          border-radius: 20px;
          background: #cccccc;
          margin-right: 5px;
        }
        .statement-text {
          font-size: 14px;
          font-family: Microsoft YaHei;
        }
      }
    }
  }

  // 头部LOGO样式
  .header-logo {
    height: 98px;
    width: 100%;
    min-width: 1162px;
    margin: auto;
    background: @backGroundColor;
    .logo-cotainer {
      width: 1162px;
      min-width: 1162px;
      height: 100%;
      margin: auto;
      display: flex;
      align-items: center;
      justify-content: space-between;
      .logo-image-box {
        display: flex;
        align-items: center;
        p {
          padding-left: 20px;
          font-size: 28px;
          color: @tipsColor;
        }
      }
      .logo {
        height: 76px;
        font-size: 30px;
        line-height: 76px;
        font-weight: 500;
        color: @primaryColor;
      }
      .logo-text {
        font-size: 20px;
        font-weight: 500;
      }
      .user-container {
        height: 98px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        li {
          margin-left: 30px;
        }
        .ic-shouji {
          width: 15px;
          height: 23px;
        }
        .ic-weixin {
          width: 26px;
          height: 21px;
        }
        .tip {
          font-size: 16px;
          font-family: Microsoft YaHei;
          font-weight: 400;
          color: @primaryColor;
          padding: 6px 12px;
          border: 1px solid @primaryColor;
          border-radius: 4px;
          cursor: pointer;
        }
        .el-popover {
          text-align: center;
        }
      }
    }
  }

  // 导航样式
  .nav-bar {
    background: @primaryColor;
    height: 55px;
    .nav-cotainer {
      width: 1162px;
      margin: auto;
      display: flex;
      justify-content: space-between;
      .nav-item {
        width: 146px;
        height: 55px;
        text-align: center;
        line-height: 55px;
        color: @backGroundColor;
        font-size: 18px;
        font-family: Microsoft YaHei;
        font-weight: 400;
        :hover {
          cursor: pointer;
        }
      }
    }
    .active {
      background: #27AA3D;
      color: @backGroundColor;
      font-size: Bold;
    }
  }

  // 搜索样式
  .search-container {
    margin-top: 20px;
    .search-box {
      padding: 20px 0;
      width: 1162px;
      margin: 0 auto;
      box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.05);
      border: 1px solid @backGroundColor;
      background: @backGroundColor;
      .input-box {
        width: 550px;
        margin: 0 auto;
        .search {
          text-align: center;
          .el-input__inner {
            background: #ffffff;
            height: 49px;
            border: 1px solid @primaryColor;
            font-family: MicrosoftYaHei-Bold;
            padding-right: 0;
            position: relative;
          }
          .el-button {
            width: 65px;
            font-size: 24px;
            color: @backGroundColor;
            background: @primaryColor;
            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px;
          }
        }
      }
      .search-options {
        width: 550px;
        margin: 0 auto;
        color: #818181;
        font-weight: 500;
        padding: 10px 0;
        font-size: 14px;
        .search-item {
          font-family: Microsoft YaHei;
          font-weight: 400;
          color: @textColor;
          cursor: pointer;
          padding-left: 5px;
        }
        .search-item:hover {
            color: @primaryColor;
          }
        .search-item:active {
            color: @primaryColor;
          }
      }
      .el-input-group__append {
        border: 1px solid  @primaryColor;
        background:  @primaryColor;
      }
    }
    .search-box-bg {
      background: url('~assets//images/search-agent.png');
      background-size: 100% 100%;
    }
  }
  .banner-container {
    height: 342px;
    margin: 0;
    background: url("~assets/images/bg-person-new.png");
    background-size: 100% 100%;

  }
  // 首页定制搜索样式
  .home-banner-container {
    height: 262px;
    background-image: url("~assets/images/bg-home-new.png");
    background-size: 100% 100%;
  }
  .bg-person {
    background: url("~assets/images/bg-person-new.png");
    background-size: 100% 100%;
  }
  .clear-login:hover {
    background:  @primaryColor;
    color: #ffffff !important;
  }
}
</style>

