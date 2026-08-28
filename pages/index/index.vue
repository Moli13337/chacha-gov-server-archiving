<template>
	<div class="home-main-container">
		<!-- 主要内容部分 -->
		<div class="content-container">
			<!-- 最新政策 -->
			<el-row
				:gutter="20"
				class="content-row"
			>
				<el-col
					:span="12"
					class="info-col"
				>
					<div class="info-container">
						<div class="info-name">
							<p
								class="info-type"
							><img
								src="~assets/images/icon-laba.png"
								class="icon icon-laba"
							/>政策发布<span class="info-en">Policy Announcement</span>
							</p>
							<span
								@click="handelMorePolicy"
								class="look-more"
							>查看更多<i class="el-icon-d-arrow-right"></i></span>
						</div>
						<ul class="info-cotent">
							<li
								class="info-items"
								v-for="(item, index) in sortedPolicyList"
								:key="index"
								@click="handelPolicyDetail(item.enc_id)"
							>
								<div class="policy-title">
									<p
										class="info-title"
									><span class="icon"></span>
										<el-popover
											popper-class="popover"
											placement="bottom-start"
											width="500"
											trigger="hover"
											:content="item.name"
										>
											<span slot="reference">{{item.name}}</span>
										</el-popover>
									</p>
									<p class="red">{{item.is_new == 1? 'new': ''}}</p>
								</div>
								<span class="created_at">{{item.created_at | formatDate}}</span>

							</li>
						</ul>
					</div>
				</el-col>
				<el-col
					:span="12"
					class="info-col"
				>
					<div class="info-container">
						<div class="info-name">
							<p
								class="info-type"
							><img
								src="~assets/images/icon-dongtai.png"
								class="icon icon-laba"
							/>公示公告<span class="info-en">Public Announcement</span>
							</p>
							<span
								@click="handelMoreNotice"
								class="look-more"
							>查看更多<i class="el-icon-d-arrow-right"></i></span>
						</div>
						<ul class="info-cotent">
							<li
								class="info-items"
								v-for="(item, index) in sortedNoticeList"
								:key="index"
								@click="handelNoticeDetail(item.enc_id, item.obj_type)"
							>
								<div class=" policy-title">
									<p class="info-title">
										<span class="icon"></span>
										<el-popover
											popper-class="popover"
											placement="bottom-start"
											width="500"
											trigger="hover"
											:content="item.name"
										>
											<span slot="reference">
												<span class="notice-type">
													【{{item.obj_type == 7? '活动通知': item.obj_type == 10? '拨款通知': '申报通知'}}】
												</span>
												{{item.name}}
											</span>
										</el-popover>
									</p>
									<p class="red">{{item.is_new == 1? 'new': ''}}</p>
								</div>
								<span class="created_at">{{item.created_at | formatDate}}</span>
							</li>
						</ul>
					</div>
				</el-col>
			</el-row>
			<el-row
				:gutter="20"
				class="content-row"
			>
				<el-col
					:span="12"
					class="info-col"
				>
					<div class="info-container">
						<div class="info-name">
							<p
								class="info-type"
							><img
								src="~assets/images/icon-dongtai-policy.png"
								class="icon icon-laba"
							/>项目申报<span class="info-en">Subsidies Application</span>
							</p>
							<span
								@click="handelMoreDeclaration"
								class="look-more"
							>查看更多<i class="el-icon-d-arrow-right"></i></span>
						</div>
						<ul class="info-cotent">
							<li
								class="info-items"
								v-for="(item, index) in sortedProjectList"
								:key="index"
								@click="handleDeclareDetail(item.id)"
							>
								<div class="policy-title">
									<p class="info-title">
										<span class="icon"></span>
										<el-popover
											popper-class="popover"
											placement="bottom-start"
											width="500"
											trigger="hover"
											:content="item.name"
										>
											<span slot="reference">{{item.name}}</span>
										</el-popover>
									</p>
									<p class="red">{{item.is_new == 1? 'new': ''}}</p>
								</div>
								<span class="created_at">{{item.created_at | formatDate}}</span>
							</li>
						</ul>
					</div>
				</el-col>
				<el-col
					:span="12"
					class="info-col"
				>
					<div class="info-container">
						<div class="info-name">
							<p
								class="info-type"
							><img
								src="~assets/images/icon-fuwu.png"
								class="icon icon-laba"
							/>管家服务<span class="info-en">Private Notification</span>
							</p>
							<span
								@click="handelMoreButler"
								class="look-more"
							>查看更多<i class="el-icon-d-arrow-right"></i></span>
						</div>
						<ul class="info-cotent">
							<li
								class="info-items"
								v-for="(item, index) in optionList"
								:key="index"
								@click="handelButlerDetail(item.enc_id)"
							>
								<div class="policy-title">
									<p class="info-title"><span class="icon"></span>
										<el-popover
											popper-class="popover"
											placement="bottom-start"
											width="500"
											trigger="hover"
											:content="item.title"
										>
											<span slot="reference">{{item.title}}</span>
										</el-popover>
									</p>
									<p class="red">{{item.is_new == 1? 'new': ''}}</p>
								</div>
								<span class="created_at">{{item.created_at | formatDate}}</span>
							</li>
						</ul>
					</div>
				</el-col>
			</el-row>
			<el-row
				:gutter="20"
				class="content-row"
			>
				<el-col
					:span="12"
					class="info-col"
				>
					<div class="info-container">
						<div class="info-name">
							<p
								class="info-type"
							><img
								src="~assets/images/icon-agency.png"
								class="icon icon-laba"
							/>中介服务<span class="info-en">Serivce of Agency</span>
							</p>
							<span
								@click="handelMoreIntermediary"
								class="look-more"
							>查看更多<i class="el-icon-d-arrow-right"></i></span>
						</div>
						<ul class="info-cotent">
							<li
								class="info-items"
								v-for="(item, index) in agentList"
								:key="index"
								@click="handelagentDetail(item.enc_id)"
							>
								<div class="policy-title">
									<p class="info-title"><span class="icon"></span>
										<el-popover
											popper-class="popover"
											placement="bottom-start"
											width="500"
											trigger="hover"
											:content="item.title"
										>
											<span slot="reference">{{item.title}}</span>
										</el-popover>
									</p>
									<p class="red">{{item.is_new == 1? 'new': ''}}</p>
								</div>
								<span class="created_at">{{item.created_at | formatDate}}</span>
							</li>
						</ul>
					</div>
				</el-col>
				<el-col
					:span="12"
					class="info-col"
				>
					<div class="info-container">
						<div
							class="info-name"
						>
							<p class="info-type"><img
								src="~assets/images/icon-kongjian.png"
								class="icon icon-laba"
							/>共享空间<span class="info-en">Shared Space</span>
							</p>
							<span
								@click="handelMoreShared"
								class="look-more"
							>查看更多<i class="el-icon-d-arrow-right"></i></span>
						</div>
						<ul class="info-cotent">
							<li
								class="info-items"
								v-for="(item, index) in activityList"
								:key="index"
								@click="handelshareDetail(item.enc_id)"
							>
								<div class="policy-title">
									<p class="info-title"><span class="icon"></span>
										<el-popover
											popper-class="popover"
											placement="bottom-start"
											width="500"
											trigger="hover"
											:content="item.title"
										>
											<span slot="reference">{{item.title}}</span>
										</el-popover>
									</p>
									<p class="red">{{item.is_new == 1? 'new': ''}}</p>
								</div>
								<span class="created_at">{{item.publish_time | formatDate}}</span>
							</li>
						</ul>
					</div>
				</el-col>
			</el-row>
		</div>
	</div>
</template>

<script>
import {
	FEAT_POLICY_LIST,
	FEAT_NOTICE_LIST,
	FEAT_PROJECT_LIST,
	AGENTNOTIFY_LIST,
	OPTION_LIST,
	ACTIVITY_LIST
} from '@/utils/urls.js';
export default {
	layout: 'home',
	data() {
		return {
			title: '首页',
			state: '',
		};
	},
	computed: {
		// 政策列表排序
		sortedPolicyList() {
			return [...this.policyList].sort((item1, item2) => item1.date - item2.date).slice(0, 6);
		},

		// 公示公告列表排序
		sortedNoticeList() {
			return [...this.noticeList].sort((item1, item2) => item1.date - item2.date).slice(0, 6);
		},

		// 公示公告列表排序
		sortedProjectList() {
			return [...this.projectList].sort((item1, item2) => item1.date - item2.date).slice(0, 6);
		},
	},
	methods: {
		// 查看更多政策
		handelMorePolicy() {
			this.$router.push('/policy');
		},
		// 查看更多公告
		handelMoreNotice() {
			this.$router.push('/notice');
		},
		// 查看更多申报
		handelMoreDeclaration() {
			this.$router.push('/declaration');
		},
		// 查看管家服务
		handelMoreButler() {
			this.$router.push('/butler/enterprise_collect');
		},
		// 查看热门中介
		handelMoreIntermediary() {
			this.$router.push('/agent');
		},
		// 查看共享空间
		handelMoreShared() {
			this.$router.push('/share/activity');
		},
		// 查看政策详情
		handelPolicyDetail(id) {
			let routeData = this.$router.resolve({name: 'policy-detail', query: {id: id}});

			window.open(routeData.href, '_blank');
		},
		// 查看公示详情
		handelNoticeDetail(id, type) {
			if (type == 4) {
				let routeData = this.$router.resolve({name: 'notice-declare', query: {id: id}});

				window.open(routeData.href, '_blank');
			} else if (type == 7) {
				let routeData =	this.$router.resolve({name: 'notice-activity', query: {id: id}});

				window.open(routeData.href, '_blank');
			} else {
				let routeData =	this.$router.resolve({name: 'notice-appropriation', query: {id: id}});

				window.open(routeData.href, '_blank');
			}
		},
		// 查看申报详情
		handleDeclareDetail(id) {
			let routeData =	this.$router.resolve({name: 'declaration-detail', query: {id: id}});

			window.open(routeData.href, '_blank');
		},
		// 查看中介详情
		handelagentDetail(id) {
			let routeData =	this.$router.resolve({name: 'agent-detail', query: {id: id}});

			window.open(routeData.href, '_blank');
		},
		// 查看共享详情
		handelshareDetail(id) {
			let routeData =	this.$router.resolve({path: '/share/activity/activity_detail', query: {id: id}});

			window.open(routeData.href, '_blank');
		},
		// 管家详情
		handelButlerDetail(id) {
			let routeData =	this.$router.resolve({path: '/butler/butler_components/option_detail', query: {id: id, type: 'option'}});

			window.open(routeData.href, '_blank');
		},

	},

	// 进入页面前获取最新政策、最新公示公告、项目申报
	async asyncData({per_page = 6, $axios}) {
		let [policyData, noticeData, projectData, agentData, optionList, activityList] = await Promise.all([
			$axios.get(FEAT_POLICY_LIST, per_page),
			$axios.get(FEAT_NOTICE_LIST, per_page),
			$axios.get(FEAT_PROJECT_LIST, per_page),
			$axios.get(AGENTNOTIFY_LIST, {params: {per_page: per_page}}),
			$axios.get(OPTION_LIST, {params: {per_page: per_page}}),
			$axios.get(ACTIVITY_LIST, {params: {per_page: per_page}})
		]);

		return {
			policyList: policyData.data || [],
			noticeList: noticeData.data || [],
			projectList: projectData.data || [],
			agentList: agentData.data || [],
			optionList: optionList.data || [],
			activityList: activityList.data || []
		};
	}
};
</script>

<style lang="less" scope>
@import "~assets/css/common_avairail.less";
.home-main-container {
  width: 100%;
  min-width: 1162px;
  position: relative;
  .content-container {
    width: 1162px;
    min-width: 1162px;
    margin: auto;
      .content-row {
       height: 300px;
       margin-bottom: 20px;
        .info-col {
          height: 100%;
          .info-container {
            height: 100%;
            background: #ffffff;
            box-shadow:1px 1px 6px rgba(0,0,0,0.08);
            .info-cotent {
                padding: 0 27px 27px 27px;
              }
            .info-name {
              height: 56px;
              line-height: 56px;
              margin-bottom: 20px;
              display: flex;
              justify-content: space-between;
              // align-items: center;
              background: url('~assets//images/bg-title.jpg');
              background-size: 100% 100%;
              .info-type {
                padding-left: 27px;
                font-size: 25px;
                font-family: Microsoft YaHei;
                font-weight: bold;
                color: #ffffff;
                width: 70%;
                display: flex;
                justify-content: flex-start;
                align-items: center;
                .info-en {
                  font-size:14px;
                  font-family:Arial;
                  font-weight:400;
                  padding-left: 10px;
                  padding-top: 10px;
                }
                .icon {
                  width: 19px;
                  height: 21px;
                  margin-right: 10px;
                }
              }
              .look-more {
                font-family: Microsoft YaHei;
                font-weight: 400;
                color: @primaryColor;
                // background:rgba(235,235,235,1);
                width: 30%;
                text-align: right;
                padding-right: 10px;
                font-size:20px;
                cursor: pointer;
              }
            }
            .info-items {
              display: flex;
              justify-content: space-between;
              align-items: center;
              // height: 21px;
              padding-top: 10px;
              cursor: pointer;
              :hover {
                color: @primaryColor;
              }
              .policy-title {
                display: flex;
                justify-content: space-between;
                .red {
                  color: @tipsColor;
                }
              }
              .info-title {
                width: 350px;
                overflow: hidden;
                text-overflow:ellipsis;
                white-space: nowrap;
                font-size: 16px;
                font-family: Microsoft YaHei;
                font-weight: 400;
                .icon {
                  display: inline-block;
                  width: 8px;
                  height: 8px;
                  border: 1px solid @primaryColor;
                  margin-right: 10px;
                }
              }
              .created_at {
                font-size: 16px;
                font-family: Microsoft YaHei;
                font-weight: 400;
              }
            }
          }
        }
      }
      .building {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        .empty {
            width: 173px;
            height: 167px;
          }
          .bold {
            font-size:18px;
            font-family:Microsoft YaHei;
            font-weight:bold;
            color: #818181;
          }
          .small {
            font-size:16px;
            font-family:Microsoft YaHei;
            font-weight:400;
            color: #CBCBCB;
          }
      }

  }
  .el-popper {
    z-index: 9999 !important;
  }
}
</style>
