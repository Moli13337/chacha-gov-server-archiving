<template>
	<div class="problem_feedback-content">
		<div class="top">
			<p><el-divider direction="vertical"></el-divider><span>企业问题反馈与处理</span></p>
			<div class="message-type">
				<p class="tip">反馈类型</p>
				<el-select
					v-model="filter.type"
					placeholder="请输入关键词"
					@change="handleFilterChange"
				>
					<el-option
						:label="item.name"
						:value="item.id"
						v-for="(item, index) in feedbackTypeOptions"
						:key="index"
					>
					</el-option>
				</el-select>
				<p class="tip">反馈状态</p>
				<el-select
					v-model="filter.status"
					placeholder="请输入关键词"
					@change="handleFilterChange"
				>
					<el-option
						:label="item.name"
						:value="item.id"
						v-for="(item, index) in feedbackStatusOptions"
						:key="index"
					>
					</el-option>
				</el-select>
			</div>
		</div>
		<!-- 消息为空 -->
		<Empty v-if="!feedbackList || !feedbackList.length"/>
		<div
			class="list-container"
			v-else
		>
			<ul v-loading="loading">
				<li
					class="list-item"
					v-for="(item, index) in feedbackList"
					:key="index"
				>
					<div class="title">
						<div class="title-box">
							<p class="item-title">{{item.title}}</p>
							<div class="btn-box">
								<el-tag>{{item.type == 1 ? '建议' : item.type == 2 ? '投诉' : '咨询' }}</el-tag>
								<el-tag
									type="success"
									v-if="item.status == 2"
								>待处理</el-tag>
								<el-tag
									type="warning"
									v-else-if="item.status == 1"
								>已处理</el-tag>
							</div>
						</div>
						<p class="time">{{item.created_at | formatDate('YYYY-MM-DD HH:mm:ss')}}</p>
					</div>
					<p class="describe">{{item.content}}</p>
					<div
						class="bubble"
						v-if="item.status == 1"
					>
						<p class="item-title">处理结果:</p>
						<p class="describe">{{item.reply.content || '无'}}</p>
					</div>
				</li>
			</ul>
			<div class="pagination">
				<pagination
					:pagination="pagination"
					@onPageChange="onPageChange"
				/>
			</div>
		</div>
	</div>
</template>
<script>
import {
	FEEDBACK_LIST,
} from '@/utils/urls.js';
import Empty from '@/components/empty';
import pagination from '@/components/pagination.vue';
export default {
	components: {
		Empty,
		pagination
	},
	props: {
		value: {
			type: Number,
			default: 0
		},
	},
	data() {
		return {
			opinionForm: {},
			loading: false,
			messageList: [],
			filter: {
				type: '',
				status: '',
			},
			selectedMessageType: '',
			feedbackTypeOptions: [
				{
					id: '',
					name: '全部'
				},
				{
					id: 1,
					name: '建议'
				},
				{
					id: 2,
					name: '投诉'
				},
				{
					id: 3,
					name: '咨询'
				},
			],
			feedbackStatusOptions: [
				{
					id: '',
					name: '全部'
				},
				{
					id: 1,
					name: '已处理'
				},
				{
					id: 2,
					name: '待处理'
				},
			],
		};
	},

	async asyncData({$axios}) {
		let params = {
			page: 1,
			per_page: 10
		};

		return Promise.all([
			$axios.get(FEEDBACK_LIST, {params}),
		])
			.then(([result]) => {
				const data = result || {};

				return {
					feedbackList: data.data,
					pagination: {
						total: data.total,
						pageCount: data.current_page,
						totalPage: data.total_page,
						pageSize: data.per_page_num
					},
				};
			})
			.catch(e => {
				console.log(e);
			});
	},
	methods: {
		// 获取消息列表数据
		fetchFeedbackList(pageCount, pageSize) {
			let params = {
				page: pageCount || this.pagination.pageCount,
				per_page: pageSize || this.pagination.pageSize,
				...this.filter
			};


			this.loading = true;
			this.$axios.get(FEEDBACK_LIST, {params: params})
				.then((data = {}) => {
					this.loading = false;
					this.feedbackList = data.data;
					this.pagination = {
						...this.pagination,
						total: data.total,
						pageCount: data.current_page,
						totalPage: data.total_page,
					};
				}).catch((error) => {
					this.loading = false;
					console.log(error.message);
				});
		},

		// 过滤条件改变
		handleFilterChange() {
			this.fetchFeedbackList(1);
		},

		// 页码改变
		onPageChange(pageCount) {
			this.fetchFeedbackList(pageCount);
		}
	}
};
</script>

<style lang="less">
@import '~assets/css/common_avairail.less';
  .problem_feedback-content {
    background: #ffffff;
    .el-divider--vertical {
      width: 6px;
      height: 27px;
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
        padding: 20px;
      }

        .title-box {
          font-size:16px;
          font-weight:bold;
          display: flex;
          align-items: center;
          .item-title {
            padding: 10px 10px 10px 0;
          }
          .button {
            height:26px;
            border:1px solid rgba(3,109,180,1);
            padding: 10px;
          }
        }
        .title {
          display: flex;
          justify-content: space-between;
          align-items: center;
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
        }
      .bubble {
        min-height: 100px;
        background: url('~assets/images/butler/bg_result.png');
        background-size: 100% 100%;
        margin-top: 10px;
        padding: 20px;
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
   .tip-box {
      font-size:14px;
      display: flex;
      justify-content: flex-start;
      padding-top: 10px;
      .tip {
        color: #CBCBCB;
        margin-right: 50px;
      }
      .tip-content {
        color: #818181;
      }
    }
    .pagination {
    text-align: center;
    padding: 20px 0;
    .number {
      width: 40px;
      height: 40px;
      line-height: 40px;
    }
    .btn-prev, .btn-next {
      height: 40px;
      border:1px solid rgba(235,235,235,1);
      background: none;
      padding: 0 10px;
    }
    .more {
      height: 40px;
      line-height: 40px;
    }
  }
  .optionForm {
    .upload-tip {
      color: #818181;
    }
    .qr-code-box {
      display: flex;
      justify-content: space-between;
    }
    .code-input {
      flex: 1;
    }
    .el-input__inner {
      border-radius: 0;
    }
    .code-image {
      width: 150px;
      border: 1px solid #DCDFE6;
      height: 40px;
      margin-left: 30px;
    }
    .dialog-footer {
      text-align: center;
    }
  }
  }
</style>

