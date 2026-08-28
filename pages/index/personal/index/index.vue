<template>
	<el-col :span="19">
		<div class="personal-content">
			<div class="top">
				<p><el-divider direction="vertical"></el-divider><span>系统消息</span></p>
				<div class="message-type">
					<p class="tip">消息类型</p>
					<el-select
						v-model="selectedMessageType"
						placeholder="请选择消息类型"
						@change="handleFilterChange"
					>
						<el-option
							:label="item.name"
							:value="item.id"
							v-for="(item, index) in messageTypeOptions"
							:key="index"
						>
						</el-option>
					</el-select>
				</div>
			</div>
			<!-- 消息为空 -->
			<Empty v-if="!messageList || !messageList.length"/>
			<div
				class="list-container"
				v-else
			>
				<ul v-loading="loading">
					<li
						class="list-item"
						v-for="(item, index) in messageList"
						:key="index"
					>
						<el-row>
							<el-col
								:span="19"
								class="col-left"
							>
								<div class="title">
									<div class="message">
										<el-badge
											is-dot
											v-if="item.is_read == 1"
										>
											{{item.title}}
										</el-badge>
										<p v-if="item.is_read == 2">{{item.title}}</p>
									</div>
									<p class="time">{{item.created_at | formatDate('YYYY-MM-DD HH:mm:ss')}}</p>
								</div>
								<el-divider></el-divider>
								<p class="describe">{{item.content}}</p>
							</el-col>
							<el-col
								:span="5"
								class="col-right"
							>
								<div>
									<el-button
										type="primary"
										@click="handleDetailClick(item)"
									>
										查看详情
									</el-button>
								</div>
							</el-col>
						</el-row>
					</li>
				</ul>
				<div class="pagination">
					<el-pagination
						background
						prev-text='上一页'
						next-text='下一页'
						layout="prev, pager, next"
						:total="pagination.total"
						:page-count="pagination.pageCount"
						:page-size="pagination.pageSize"
						@current-change="handlePageChange"
					/>
				</div>
			</div>
			<el-dialog
				:visible.sync="detailDialogVisible"
				width="45%"
				center
				class="dialog"
			>
				<template slot="title">
					<p
						class="title"
						style="background='red'"
					>消息通知详情</p>
				</template>
				<template>
				</template>
				<div
					class="dialog-footer"
				>
					<p>具体内容:</p>
					<div class="content">
						{{currMessage && currMessage.content}}
					</div>
				</div>
			</el-dialog>
		</div>
	</el-col>
</template>
<script>
import {
	FETCH_MESSAGE_DETAIL,
	FETCH_MESSAGE_LIST,
	FETCH_UNREAD_MESSAGE_COUNT,
	APPLY_CONFIG
} from '@/utils/urls.js';
import Empty from '@/components/empty';

export default {
	components: {
		Empty
	},
	props: {
		value: {
			type: Number,
			default: 0
		},
	},
	data() {
		return {
			loading: false,
			messageList: [],
			pagination: {
				total: 0,
				pageId: 1,
				pageCount: 0,
				pageSize: 10
			},
			detailDialogVisible: false,
			currMessage: null,
			selectedMessageType: '',
		};
	},

	async asyncData({$axios}) {
		let params = {
			page: 1,
			per_page: 10
		};

		return Promise.all([
			$axios.get(APPLY_CONFIG),
			$axios.get(FETCH_MESSAGE_LIST, {params}),
		])
			.then(([config, result]) => {
				const data = result || {};

				let messageTypeOptions = config.user_message_source || [];

				// 添加全部选项
				messageTypeOptions.unshift({
					id: '',
					name: '全部'
				});

				return {
					messageList: data.data,
					pagination: {
						total: data.total,
						pageId: 1,
						pageCount: data.total_page,
						pageSize: 10
					},
					messageTypeOptions
				};
			})
			.catch(e => {
				console.log(e);
			});
	},
	methods: {
		// 获取消息列表数据
		fetchMessageList(pageId, pageSize) {
			let params = {
				page: pageId || this.pagination.pageId,
				per_page: pageSize || this.pagination.pageSize,
			};

			// 系统通知为0, this.selectedMessageType为false
			if (this.selectedMessageType || this.selectedMessageType === 0) {
				params.source_type_id = this.selectedMessageType;
			}

			this.loading = true;
			this.$axios.get(FETCH_MESSAGE_LIST, {params: params})
				.then((data = {}) => {
					this.loading = false;
					this.messageList = data.data;
					this.pagination = {
						...this.pagination,
						total: data.total,
						pageId: data.current_page,
						pageCount: data.total_page
					};
				}).catch(() => {
					this.loading = false;
					this.$message.error('获取消息数据失败，请重试');
				});
		},
		// 获取消息详情
		fetchDetail(id) {
			this.$axios.get(FETCH_MESSAGE_DETAIL, {params: {id}})
				.then(message => {
					this.fetacUnreadMumber();
					this.showDetailDialog(message);
				}).catch(() => {
					this.$message.error('获取消息内容失败，请重试');
				});
		},
		// 更新未读消息数
		fetacUnreadMumber() {
			this.$axios.get(FETCH_UNREAD_MESSAGE_COUNT)
				.then(unreadCount => {
					this.$bus.emit('onUnreadCountChange', unreadCount);
				})
				.catch(error => {
					console.log(error);
				});
		},
		// 处理消息内容点击
		handleDetailClick(item) {
			this.fetchDetail(item.id);
			item.is_read = 2;
		},
		// 页码改变
		handlePageChange(pageId) {
			this.fetchMessageList(pageId);
		},
		// 过滤条件改变
		handleFilterChange() {
			this.fetchMessageList(1);
		},
		// 显示消息详情
		showDetailDialog(message) {
			this.detailDialogVisible = true;
			this.currMessage = message;
		},
		// 关闭弹窗
		dismissDetailDialog() {
			this.detailDialogVisible = false;
			this.currMessage = null;
		}
	}
};
</script>

<style lang="less">
@import '~assets/css/common_avairail.less';
  .personal-content {
    background: #ffffff;
    border:1px solid rgba(235,235,235,1);
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
</style>

