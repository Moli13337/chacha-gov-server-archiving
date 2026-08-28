<template>
	<el-col :span="19">
		<div class="content">
			<div class="top">
				<p>
					<el-divider direction="vertical"></el-divider>
					<span>申报记录</span>
				</p>
				<div class="message-type">
					<p class="tip">申报状态</p>
					<el-select
						v-model="recordStatus"
						placeholder="全部"
						@change="handleStatusChange"
					>
						<el-option
							:label="item.name"
							:value="item.id"
							v-for="(item, index) in statusOptions"
							:key="index"
						></el-option>
					</el-select>
				</div>
			</div>
			<Empty
				v-if="!haveEnterprise"
				tip="企业未认证，点击按钮立即认证"
				:hasBtn="true"
			/>
			<Empty
				v-else-if="!recordList || !recordList.length"
				tip="当前暂无申报记录"
			/>
			<template v-else>
				<div class="list-container">
					<el-table
						class="list-table"
						v-loading="loading"
						:data="recordList"
						:header-cell-style="tableHeaderStyle"
						style="width: 100%"
					>
						<el-table-column
							prop="policy_name"
							label="政策类型"
							align="center"
						></el-table-column>
						<el-table-column
							prop="project_name"
							label="支持项目"
							align="center"
						></el-table-column>
						<el-table-column
							prop="created_at"
							label="提交时间"
							align="center"
						>
							<template slot-scope="scope"><span v-if="scope.row.apply_status == 1">--</span><span v-else>{{scope.row.created_at*1000 | formatDate}}</span></template>
						</el-table-column>
						<el-table-column
							prop="apply_status"
							label="审核状态"
							align="center"
						>
							<template slot-scope="scope">{{scope.row.apply_status | formatDeclara}}</template>
						</el-table-column>
						<el-table-column
							prop="audit_time"
							label="审核时间"
							align="center"
							width="120px"
						>
							<template
								slot-scope="scope"
							><span v-if="scope.row.apply_status == 1">--</span><span v-else>{{scope.row.audit_time*1000 | formatDate}}</span></template>
						</el-table-column>
						<el-table-column
							label="操作"
							width="140"
							align="center"
						>
							<template
								class="btn-box"
								slot-scope="scope"
							>
								<el-button
									type="text"
									@click="handleLookClick(scope.row)"
								>
									查看
								</el-button>
								<el-button
									type="text"
									@click="handleRemoveClick(scope.row.id)"
									v-if="scope.row.apply_status == 1"
								>
									删除
								</el-button>
								<el-button
									type="text"
									@click="handleEditClick(scope.row)"
									v-if="scope.row.apply_status == 1 || scope.row.apply_status == 4"
								>
									修改
								</el-button>
								<el-button
									type="text"
									v-if="scope.row.has_material == true"
									@click="handleMaterialClick(scope.row)"
								>
									补充资料
								</el-button>
								<el-button
									type="text"
									v-if="scope.row.has_correct == true"
									@click="handleCorrectClick(scope.row)"
								>
									订正资料
								</el-button>
								<el-button
									type="text"
									v-if="scope.row.able_revocation == 1"
									@click="handleApplyRevoction(scope.row)"
								>
									撤销申报
								</el-button>
							</template>
						</el-table-column>
					</el-table>

					<div class="pagination">
						<el-pagination
							background
							prev-text="上一页"
							next-text="下一页"
							layout="prev, pager, next"
							:total="pagination.total"
							:page-count="pagination.pageCount"
							:page-size="pagination.pageSize"
							@current-change="handlePageChange"
						/>
					</div>
				</div>
			</template>
		</div>
	</el-col>
</template>
<script>
import {
	APPLY_LIST,
	DELETE_DRAFT,
	CANCEL_APPLY_REVOCATION
} from '@/utils/urls.js';
import storage from '@/utils/storage';
import Empty from '@/components/empty';
export default {
	components: {
		Empty
	},
	props: {
		value: {
			type: Number,
			default: 0
		}
	},
	data() {
		return {
			recordStatus: '',
			loading: false,
			recordList: [],
			pagination: {
				total: 0,
				pageId: 1,
				pageCount: 0,
				pageSize: 10
			},
			statusOptions: [
				{
					id: 0,
					name: '全部'
				},
				{
					id: 1,
					name: '草稿'
				},
				{
					id: 2,
					name: '待系统预处理'
				},
				{
					id: 3,
					name: '待受理'
				},
				{
					id: 4,
					name: '不受理'
				},
				{
					id: 5,
					name: '待主审部门审核'
				},
				{
					id: 6,
					name: '线下会审中'
				},
				{
					id: 7,
					name: '待指挥部审核'
				},
				{
					id: 8,
					name: '待拨款'
				},
				{
					id: 9,
					name: '已拨款'
				},
				{
					id: 10,
					name: '主审部门不通过'
				},
				{
					id: 11,
					name: '线下会审不通过'
				},
				{
					id: 12,
					name: '指挥部不通过'
				}
			]
		};
	},
	// 初始化项目申报列表，返回data数据
	async asyncData({$axios}) {
		let userInfo = storage.getItem('user_info');

		// 没有企业信息不应该发起网络请求
		if (!userInfo || !userInfo.enterprise || !userInfo.enterprise.length) {
			return {};
		}

		let params = {
			page: 1,
			per_page: 10
		};

		let result = await $axios.get(APPLY_LIST, {params});
		const data = result || {};

		return {
			recordList: data.data,
			pagination: {
				total: data.total,
				pageId: 1,
				pageCount: data.total_page,
				pageSize: 10
			},
		};
	},
	computed: {
		// 后端需要的过滤参数
		filterParams() {
			let filterParams = {};

			// 适用地区
			if (this.recordStatus) {
				filterParams.apply_status = this.recordStatus;
			}
			return filterParams;
		},
		// 是否有绑定企业
		haveEnterprise() {
			let userInfo = storage.getItem('user_info');

			return userInfo && userInfo.enterprise && userInfo.enterprise.length;
		}
	},
	methods: {
		// 根据过滤参数请求政策列表
		fetchRecordList(pageId, pageSize) {
			// 没有企业信息不应该发起网络请求
			if (!this.haveEnterprise) {
				return;
			}

			let params = {
				...this.filterParams,
				page: pageId || this.pagination.pageId,
				per_page: pageSize || this.pagination.pageSize,
			};

			this.loading = true;
			this.$axios.get(APPLY_LIST, {params: params})
				.then((data = {}) => {
					if (data.code === 13008) {
						this.$router.push('/personal/mine');
					} else {
						this.loading = false;
						this.recordList = data.data;
						this.pagination = {
							...this.pagination,
							total: data.total,
							pageId: data.current_page,
							pageCount: data.total_page
						};
					}
				})
				.catch(error => {
					console.log(error);
					this.loading = false;
					this.$message.error('获取数据失败，请稍后重试');
				});
		},
		handleEditClick(row) {
			if (row.apply_status == 4 && row.children_id !== 0) {
				this.$message.error('该项目已有新纪录，请前去项目申报最新记录修改！');
			} else {
				let routeData = this.$router.resolve({
					name: 'declaration-online-mode',
					params: {
						mode: 'edit'
					},
					query: {
						id: row.id,
						has_approval: 1,
						apply_status: row.apply_status
					}
				});

				window.open(routeData.href, '_blank');
			}
		},
		// 删除
		handleRemoveClick(id) {
			console.log(id);

			this.$confirm('此操作将删除该草稿, 是否继续?', '提示', {
				confirmButtonText: '确定',
				cancelButtonText: '取消',
				type: 'warning'
			}).then(() => {
				this.recordList.forEach(item => {
					if (item.id == id) {
						let index = this.recordList.indexOf(item);

						this.$axios.post(DELETE_DRAFT, {id: id}).then(res => {
							console.log(res);
							this.recordList.splice(index, 1);
						})
							.catch(error => {
            	console.log(error);
							});
					}
				});
				this.$message({
					type: 'success',
					message: '删除成功!'
				});
			}).catch(() => {
				this.$message({
					type: 'info',
					message: '已取消删除'
				});
			});
		},
		// 查看
		handleLookClick(row) {
			console.log(row);
			if (row.apply_status >= 4) {
				let routeData = this.$router.resolve({
					name: 'declaration-online-mode',
					params: {
						mode: 'look',
					},
					query: {
						id: row.id,
						need_approval: 2,
					}
				});

				window.open(routeData.href, '_blank');
			} else {
				let routeData = this.$router.resolve({
					name: 'declaration-online-mode',
					params: {
						mode: 'look',
					},
					query: {
						id: row.id,
						need_approval: 1,
					}
				});

				window.open(routeData.href, '_blank');
			}
		},
		// 补充材料
		handleMaterialClick(row) {
			let routeData = this.$router.resolve({
				name: 'declaration-online-mode',
				params: {
					mode: 'material'
				},
				query: {
					id: row.id
				}
			});

			window.open(routeData.href, '_blank');
		},

		// 订正资料
		handleCorrectClick(row) {
			let routeData = this.$router.resolve({
				name: 'declaration-online-mode',
				params: {
					mode: 'correct'
				},
				query: {
					id: row.id
				}
			});

			window.open(routeData.href, '_blank');
		},

		// 撤销申报
		handleApplyRevoction(row) {
			let params = {
				id: row.id
			};

			this.$axios.post(CANCEL_APPLY_REVOCATION, params).then(() => {
				this.fetchRecordList(1);
				this.$message.success('撤销申报成功');
			}).catch(error => {
				console.log(error);
			});
		},
		handleCertificationClick() {
			this.$router.push({name: 'certification'});
		},
		// 处理塞选状态改变
		handleStatusChange() {
			this.fetchRecordList(1);
		},
		// 触发分页
		handlePageChange(pageId) {
			this.fetchRecordList(pageId);
		},
		tableHeaderStyle({rowIndex}) {
			if (rowIndex == 0) {
				return 'background:#F9FBFC;font-weight:bold; color: #3B3B3B';
			}
		}
	}
};
</script>

<style lang="less">
@import "~assets/css/common_avairail.less";
.content {
  border: 1px solid rgba(235, 235, 235, 1);
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
        font-size: 16px;
        font-family: Microsoft YaHei;
        font-weight: 400;
        color: @borderLine;
      }
    }
  }
  .certification-btn {
    margin-top: 32px;
  }
  .list-container {
    padding: 20px 30px 30px 30px;
    .btn-box {
      display: flex;
      .el-button--text {
        span {
          text-decoration: underline;
        }
      }
    }

    .dialog {
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
        color: @defaultTextColor;
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

