<template>
	<div class="butler-massage-list">
		<div class="breadcrumb-row">
			<el-divider direction="vertical"></el-divider>
			当前位置：
			<el-breadcrumb separator-class="el-icon-arrow-right">
				<el-breadcrumb-item>
					<nuxt-link to="/">首页</nuxt-link>
				</el-breadcrumb-item>
				<el-breadcrumb-item>
					<nuxt-link to="/butler">管家服务</nuxt-link>
				</el-breadcrumb-item>
				<el-breadcrumb-item>
					<div>消息通知</div>
				</el-breadcrumb-item>
			</el-breadcrumb>
		</div>
		<div class="personal-content">
			<div class="top">
				<p><el-divider direction="vertical"></el-divider><span>消息记录</span></p>
				<div class="message-type">
					<el-input
						:placeholder="placeholder"
						v-model="keyword"
						class="input-with-select"
					>
						<el-button
							slot="append"
							icon="el-icon-search"
							@click="handelSearchMessage"
						></el-button>
					</el-input>
				</div>
			</div>
			<!-- 消息为空 -->
			<Empty v-if="!trumpetList || !trumpetList.length"/>
			<div
				class="list-container"
				v-else
			>
				<ul v-loading="loading">
					<li
						class="list-item"
						v-for="(item, index) in trumpetList"
						:key="index"
					>
						<el-row>
							<el-col
								:span="24"
								class="col-left"
							>
								<div class="title">
									<div class="message">
										<p 	@click="handleTodeclareDetail(item.id)">{{item.obj_type_name}} <span class="red">new</span></p>
									</div>
									<p class="time">{{item.created_at | formatDate('YYYY-MM-DD HH:mm:ss')}}</p>
								</div>
								<el-divider></el-divider>
								<p class="describe">{{item.content}}</p>
							</el-col>
						</el-row>
					</li>
				</ul>
			</div>
			<pagination
				:pagination="pagination"
				@onPageChange="onPageChange"
			/>
		</div>
	</div>
</template>
<script>
import {
	PROJECT_PUSH_TRUMPET
} from '@/utils/urls.js';
import Empty from '@/components/empty';
import pagination from '@/components/pagination.vue';

export default {
	components: {
		Empty,
		pagination
	},
	data() {
		return {
			loading: false,
			keyword: '',
			messageList: [],
			trumpetList: []
		};
	},

	// 渲染页面前获取数据
	async asyncData({$axios, query}) {
		let params = {
			page: 1,
			per_page: 10,
		};
		let placeholder = '';

		if (query.mode && query.mode == 'project_recommend') {
			params.obj_type = 8;
			placeholder = '请输入项目名称';
		} else if (query.mode && query.mode == 'industry_news') {
			params.obj_type = 14;
			placeholder = '请输入行业动态名称';
		} else if (query.mode && query.mode == 'meeting_notice') {
			params.obj_type = 15;
			placeholder = '请输入会议通知名称';
		}

		return Promise.all([
			$axios.get(PROJECT_PUSH_TRUMPET, {params})
		])
			.then(([result]) => {
				const data = result || {};

				return {
					trumpetList: data.data,
					placeholder: placeholder,
					pagination: {
						total: data.total,
						pageCount: data.current_page,
						pageSize: 10,
						totalpage: data.total_page
					},
				};
			})
			.catch(e => {
				console.log(e);
			});
	},
	methods: {

		// 获取项目推荐小喇叭
		featchIndustryTrumpetList(pageCount, pageSize) {
			let params = {
				page: pageCount || this.pagination.pageCount,
				per_page: pageSize || this.pagination.pageSize,
			};

			if (this.keyword) {
				params.keyword = this.keyword;
			}

			if (this.$route.query.mode && this.$route.query.mode == 'project_recommend') {
				params.obj_type = 8;
			} else if (this.$route.query.mode && this.$route.query.mode == 'industry_news') {
				params.obj_type = 14;
			} else if (this.$route.query.mode && this.$route.query.mode == 'meeting_notice') {
				params.obj_type = 15;
			}
			this.$axios.get(PROJECT_PUSH_TRUMPET, {params: params}).then(res => {
				this.trumpetList = res.data || [];
				this.pagination.total = res.total;
				this.pagination.pageCount = res.current_page;
				this.pagination.totalPage = res.total_page;
			}).catch(error => {
				console.log(error.message);
			});
		},

		// 搜索参数
		handelSearchMessage() {
			this.featchIndustryTrumpetList();
		},

		// 跳转详情参数
		handleTodeclareDetail(id) {
			let mode = this.$route.query.mode;

			switch (mode) {
				case 'project_recommend':
					this.$router.push({
						path: '/declaration/detail',
						query: {
							id
						}
					});
					break;
				case 'industry_news':
					this.$router.push({
						path: '/butler/butler_components/industry_news',
						query: {
							id,
							type: 'information'
						}
					});
					break;
				case 'meeting_notice':
					this.$router.push({
						path: '/butler/butler_components/industry_news',
						query: {
							id,
							type: 'information'
						}
					});
					break;
				default:
					break;
			}
		},

		// 页码变化触发获取搜索
		onPageChange(pageCount) {
			this.featchIndustryTrumpetList(pageCount);
		}
	},
};
</script>

<style lang="less">
@import '~assets/css/common_avairail.less';
.butler-massage-list{
  .personal-content {
    background: #ffffff;
    .el-divider--vertical {
      width: 11px;
      height: 34px;
      background: @primaryColor;
    }
    .top {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 0;
      border-bottom: 1px solid @defaultBorderColor;
      .message-type {
        margin-right: 30px;
        display: flex;
        justify-content: space-around;
        align-items: center;
        .tip {
          padding: 0 10px;
          font-size:16px;
          font-family:Microsoft YaHei;
          font-weight:400;
          color: @borderLine;
        }
      }
    }
    .list-container {
      padding: 20px 30px 30px 30px;
      .list-item {
        width: 100%;
        border: 1px solid  rgba(235,235,235,1);
        border-radius:5px;
        margin-bottom: 10px;
      }
      .col-left {
        padding: 10px 20px;
        border-right: 1px solid rgba(235,235,235,1);
        .message {
          font-size:16px;
          font-family:Microsoft YaHei;
          font-weight:bold;
          .red {
            color: #FF4646;
          }
        }
        .title {
          display: flex;
          justify-content: space-between;
          .time {
            font-size:14px;
            font-family:Microsoft YaHei;
            font-weight:400;
            color: @borderLine;
          }
        }
        .el-divider--horizontal {
          margin: 5px 0;
        }
        .describe {
          font-size:14px;
          font-family:Microsoft YaHei;
          font-weight:400;
          color: @textColor;
          overflow:hidden;
          text-overflow:ellipsis;
          display:-webkit-box;
          /* autoprefixer: off */
          -webkit-box-orient:vertical;
          /* autoprefixer: on */
          -webkit-line-clamp:2;
        }
      }
      .el-row {
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
      }
       .col-right {
        text-align: center;

       }
    }
    .dialog{
    .el-dialog__header {
       background: @primaryColor;
       color: #ffffff;
       font-size: 18px;
       text-align: left;
       font-weight: bold;
    }
    .el-icon-close {
       color: #ffffff;
        font-size: 20px;
    }
    .tip {
      color:@defaultTextColor;
      font-weight: bold;
      padding: 20px 20px 0 20px;
    }
    }
    .dialog-footer {
      text-align: left;
      padding: 0px 20px 0 20px;
    .content {
      border: 1px solid @defaultBorderColor;
      padding: 10px;
      margin-top: 10px;
    }
    }

  }
}

</style>

