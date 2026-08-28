<template>
	<div class="agent-container">
		<el-row
			class="row-container row-container-padding"
			:gutter="30"
		>
			<el-col
				:span="6"
				class="col-left"
			>
				<div
					class="left-logo"
					@click="handleToGuide('all')"
				>
					<img
						class="bg-guid"
						src="~assets/images/mask.png"
					>
				</div>
				<div class="flex-box">
					<p
						class="flex-item"
						@click="handleToGuide('introduce')"
					>中介服务简介</p>
					<p
						class="flex-item"
						@click="handleToGuide('institution_entry')"
					>入驻流程</p>
				</div>
				<div class="flex-box">
					<p
						class="flex-item"
						@click="handleToGuide('rules')"
					>评价机制</p>
					<p
						class="flex-item"
						@click="handleToGuide('supervision')"
					>服务监督</p>
				</div>
			</el-col>
			<el-col
				:span="18"
				class="col-right"
			>
				<div class="notice-title">
					<div class="notice-title-right">
						<img
							class="icon_notice"
							src="~assets/images/icon_notice.png"
						>
						通知公告
					</div>
					<div @click="handleMore('notice')">
						<img
							class="icon_more"
							src="~assets/images/icon_more.png"
						>
					</div>

				</div>
				<ListPanel
					:List="noticeList"
					:isShowLine="true"
					:isShowNew="true"
					type="notice"
					to="agent-detail"
				/>
			</el-col>
		</el-row>
		<div
			class="row-container"
		>
			<div class="notice-title notice-title-bg">
				<div class="notice-title-right">
					<img
						class="icon_arrow"
						src="~assets/images/icon_arrow.png"
					>
					中介机构推荐
				</div>
			</div>
			<el-row
				class="inner-row"
				:gutter="30"
			>
				<el-col
					:span="6"
				>
					<p
						class="sub-title-bg"
						:class="{'service-type-active': active == -1 }"
						@click="handleAgentList('',-1)"
					>中介机构资源库</p>
					<ul class="service-type-list">
						<li
							class="service-type"
							:class="{'service-type-active': active == index }"
							v-for="(item, index) in agentTypeList"
							:key="index"
							@click="handleAgentList(item.id, index)"
						>
							<div class="service-type-title">
								<img
									v-if="active == index "
									class="service-type-icon"
									:src="item.active"
								>
								<img
									v-else
									class="service-type-icon"
									:src="item.iconurl"
								>
								{{item.name}}
							</div>
							<p><i
								class="el-icon-arrow-right"
								:class="{'el-icon-arrow-right-active': active == index }"
							></i></p>
						</li>
					</ul>
				</el-col>
				<el-col :span="18">
					<div class="notice-title">
						<div>优质机构</div>

						<div @click="handleMore('agent')">
							<img
								class="icon_more"
								src="~assets/images/icon_more.png"
							>
						</div>
					</div>
					<evaluateList
						v-loading="loading"
						:agentList="agentList"
						:capthaPicture="capthaPicture"
						@changeCapthaPicture="fetchPictureCode"
					/>
				</el-col>
			</el-row>
		</div>
		<div
			class="row-container"
		>
			<div class="notice-title notice-title-bg">
				<div class="notice-title-right">
					<img
						class="icon_arrow"
						src="~assets/images/icon_arrow.png"
					>
					更多服务中介机构推荐
				</div>
			</div>
			<div class="more-agent">
				<div>
					<a
						target="_blank"
						href="http://zjcs.sczwfw.gov.cn/zjfw/"
					>
						<img
							class="link"
							src="~assets//images/image-link1.png"
						>
					</a>
				</div>
				<div>
					<a
						target="_blank"
						href="http://wszjfw.chengdu.gov.cn/cdzjcs/nxzjjg/pages/agencyResource/resourceLibrary.html#"
					>
						<img
							class="link"
							src="~assets//images/image-link2.png"
						>
					</a>
				</div>
			</div>
		</div>
		<div
			class="row-container"
		>

			<div class="notice-title notice-title-bg">
				<div class="notice-title-right">
					<img
						class="icon_arrow"
						src="~assets/images/icon_arrow.png"
					>
					监督运行管理
				</div>
			</div>
			<el-row :gutter="30">
				<el-col :span="12">
					<div class="notice-title notice-title-gray">
						<div class="notice-title-right">
							<img
								class="icon-break"
								src="~assets/images/icon_break.png"
							>
							一般警示名单
						</div>

						<div @click="handleMore('credit')">
							<img
								class="icon_more"
								src="~assets/images/icon_more.png"
							>
						</div>

					</div>
					<ListPanel
						:shoterTitle="true"
						:list="createdList"
						type="credit"
						to="agent-organ_detail"
					></ListPanel>
				</el-col>
				<el-col :span="12">
					<div class="notice-title notice-title-gray">
						<div class="notice-title-right">
							<img
								class="icon-break"
								src="~assets/images/icon_break.png"
							>
							严重警示名单
						</div>
						<div @click="handleMore('serious_credit')">
							<img
								class="icon_more"
								src="~assets/images/icon_more.png"
							>
						</div>
					</div>
					<ListPanel
						:shoterTitle="true"
						:list="seriousCreatedList"
						type="serious_credit"
						to="agent-organ_detail"
						:isclick="false"
					></ListPanel>
				</el-col>
			</el-row>
		</div>
	</div>
</template>
<script>
import {
	AGENTNOTIFY_LIST,
  AGENT_TYPE,
  AGENT_TYPE_RESERVED,
	AGENT_LIST,
	FETCH_CAPTCHACODE,
	CREDIT_LIST
} from '@/utils/urls.js';
import ListPanel from '@/components/list-panel.vue';
import {
	serviceTypeIcons
} from '@/utils/agent_icons';
import evaluateList from '@/components/evaluate-list.vue';

export default {
	components: {
		ListPanel,
		evaluateList
	},

	data() {
		return {
			agent_id: 1,
			loading: false,
			active: -1,
			keyword: '',
			serviceTypeIcons,
		};
	},

	// 初始化数据
	async asyncData({$axios, query}) {
		const requestParams = {
			per_page: 6,
			page: 1
		};


		const keywordParams = {
			is_excellent: 1,
			type_id: '',
		};

		if (query.keyword) {
			keywordParams.keyword = query.keyword;
		}

		return Promise.all([
			$axios.get(AGENTNOTIFY_LIST, {params: requestParams}),
			$axios.get(AGENT_TYPE_RESERVED),
			$axios.get(AGENT_LIST, {params: keywordParams}),
			$axios.get(FETCH_CAPTCHACODE),
			$axios.get(CREDIT_LIST, {params: {credit_type: 1}}),
			$axios.get(CREDIT_LIST, {params: {credit_type: 2}})
		])
			.then(([agentnotifyList, agentType, agentList, capthaPicture, createdList, seriousCreatedList]) => ({
				noticeList: agentnotifyList.data || [],
				agentType: agentType || [],
				agentList: agentList.data || [],
				createdList: createdList.data || [],
				seriousCreatedList: seriousCreatedList.data || [],
				capthaPicture: {
					img: capthaPicture.img,
					key: capthaPicture.key
				},
			}))
			.catch(e => {
				console.log(e);
			});
	},

	computed: {
		// 处理服务类型图标
		agentTypeList() {
			return this.agentType.map((item) => {
				this.serviceTypeIcons.forEach(subItem => {
					if (subItem.id == item.id) {
						item.iconurl = subItem.url;
						item.active = subItem.active;
						return item;
					}
				});
				return item;
			});
		}
	},

	methods: {

		// 请求图片验证码
		fetchPictureCode() {
			this.$axios.get(FETCH_CAPTCHACODE)
				.then(({img, key}) => {
					this.capthaPicture.img = img;
					this.capthaPicture.key = key;
				});
		},

		// 获取服务类型机构列表
		handleAgentList(id, index) {
			const requestParams = {
				is_excellent: 1,
			};

			if (id) {
				this.agent_id = id;
				requestParams.type_id = id;
			}
			this.active = index;
			this.loading = true;
			this.$axios.get(AGENT_LIST, {params: requestParams}).then(res => {
				this.agentList = res.data || [];
				this.loading = false;
			}).catch(error => {
				console.log(error.message);
				this.loading = false;
			});
		},

		// 搜索机构列表
		searchAgent() {
			let keyword = this.$route.query.keyword;

			if (keyword) {
				const {href} = this.$router.resolve({
					path: '/agent/evaluation_list',
					query: {
						keyword: keyword,
					}
				});

				window.open(href, '_blank');
			} else {
				return false;
			}
		},

		// 查看更多
		handleMore(event) {
			let routeData;

			switch (event) {
				case 'notice':
					routeData = this.$router.resolve({
						path: '/agent/notice_list',
					});
					break;
				case 'agent':
					routeData = this.$router.resolve({
						path: '/agent/evaluation_list',
						query: {
							type_id: this.agent_id
						}
					});
					break;
				case 'credit':
					routeData = this.$router.resolve({
						path: '/agent/dishonesty',
					});
					break;
				case 'serious_credit':
					routeData = this.$router.resolve({
						path: '/agent/serious_dishonesty',
					});
					break;
				default:
					break;
			}


			window.open(routeData.href, '_blank');
		},

		// 服务指南跳转
		handleToGuide(event) {
			let routeData;

			switch (event) {
				case 'all':
					routeData = this.$router.resolve({
						name: 'agent-service_guide-type',
						params: {
							type: 1
						}
					});

					break;
				case 'introduce':
					routeData = this.$router.resolve({
						name: 'agent-service_guide-type',
						params: {
							type: 1
						}
					});

					break;
				case 'institution_entry':
					routeData = this.$router.resolve({
						name: 'agent-service_guide-type',
						params: {
							type: 2
						}
					});
					break;
				case 'rules':
					routeData = this.$router.resolve({
						name: 'agent-service_guide-type',
						params: {
							type: 3
						}
					});
					break;
				case 'supervision':
					routeData = this.$router.resolve({
						name: 'agent-service_guide-type',
						params: {
							type: 4
						}
					});
					break;
				default:
					break;
			}

			window.open(routeData.href, '_blank');
		}
	},
	watch: {
		$route() {
			// 监听路由变化
			this.searchAgent();
		}
	},
};
</script>
<style lang="less">
@import '~assets/css/common_avairail.less';
  .agent-container {
    background: @backGroundColor;
    box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.05);
    .img-box {
      width: 400px;
      margin: auto;
      text-align: center;
    }
    .tip-bold {
      font-size: 21px;
      font-weight: 500;
      padding: 10px 0;
    }
    .tip-small {
      font-size: 16px;
      color: @labelText;
    }
    .bg-guid {
      width: 100%;
      cursor: pointer;
    }
    .row-container-padding {
      padding: 0 35px 0 20px;
      .el-col {
        height: 100%;
      }
    }
    .row-container {
      margin-bottom: 20px;
    }
    .icon-item {
      padding: 20px;
    }
    .left-logo{
      background: url('~assets/images/bg-guid.png');
      background-size: 100% 100%;
      position: relative;
      .image-tip {
        color: #fff;
        font-size: 30px;
        font-weight: 500;
        position: absolute;
        top: 50%;
        margin-top: -21px;
        margin-left: -60px;
        left: 50%;

      }
    }
    .col-right {
      padding: 0 !important;
      border:2px solid rgba(212,231,248,1);
      background:linear-gradient(180deg,rgba(255,255,255,1) 0%,rgba(230,241,253,1) 59%,rgba(198,223,255,1) 100%);
    }
     .notice-title {
        padding: 0 20px;
        font-size:16px;
        font-weight:bold;
        color:rgba(3,109,180,1);
        height:35px;
        line-height: 35px;
        background: url('~assets/images/title-tip.png');
        background-size: 100% 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        .notice-title-right {
          display: flex;
          align-items: center;
        }
        .icon_arrow{
          width:14px;
          height:10px;
          margin-right: 10px;
        }
        .icon_notice {
          width:16px;
          height:18px;
          margin-right: 10px;
        }
        .icon_more {
          width: 43px;
          height: 16px;
          cursor: pointer;
        }
      }
      .notice-title-bg {
        height: 45px;
        line-height: 45px;
        background: url('~assets/images/agent-bg-title.png');
        background-size: 100% 100%;
        font-size:18px;
        font-weight:bold;
        color:rgba(255,255,255,1);
      }
      .notice-title-gray {
        background: url('~assets/images/agent-bg-sub-title.png');
        background-size: 100% 100%;
        height: 42px;
        margin-top: 20px;
        display: flex;
        align-items: center;
        .icon-break {
          width: 15px;
          height: 18px;
          margin-right: 10px;
        }
      }
      .inner-row {
        padding: 20px 20px 0 20px;
        height: 100%;
      }
      .sub-title-bg {
        height: 40px;
        background: url('~assets/images/agent-bg-sub-title.png');
        background-size: 100% 100%;
        font-weight:bold;
        color:rgba(3,109,180,1);
        line-height: 35px;
        padding-left: 20px;
        margin-bottom: 10px;
      }
      .service-type-list {
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
      }
      .service-type {
        height: 35px;
        background:rgba(238,238,238,1);
        margin-bottom: 14px;
        line-height: 35px;
        padding: 0 20px;
        display: flex;
				justify-content: space-between;
				cursor: pointer;
        .service-type-title {
          display: flex;
          align-items: center;
        }
        .el-icon-arrow-right {
          color: @primaryColor;
        }
        .el-icon-arrow-right-active{
          color: #fff;
        }
        .service-type-icon {
          width: 15px;
          height: 17px;
          margin-right: 10px;
        }
      }
    .service-type-active {
      background: #036DB4;
      color: #fff;
    }
    .flex-box {
      display: flex;
      justify-content: space-between;
      margin-top: 10px;
      .flex-item {
        width: 120px;
        height: 35px;
        line-height: 35px;
        text-align: center;
        background:rgba(3,109,180,1);
        font-size:14px;
        font-weight:400;
        color:rgba(255,255,255,1);
        cursor: pointer;
      }
    }
    .more-agent {
      padding: 20px;
      display: flex;
      justify-content: space-between;
      .link {
        width: 550px;
        height: 100px;
      }
    }


  }
</style>

