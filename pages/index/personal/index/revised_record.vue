<template>
	<el-col :span="19">
		<div class="content">
			<div class="top">
				<p>
					<el-divider direction="vertical"></el-divider>
					<span>申报资料订正记录</span>
				</p>
				<div class="message-type">
					<p class="tip">订正状态</p>
					<el-select
						v-model="status"
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
					<div class="search-box">
						<el-input
							placeholder="项目名称"
							v-model="keyword"
							class="input-with-select"
						>
							<el-button
								slot="append"
								icon="el-icon-search"
								@click="handleSearch"
							></el-button>
						</el-input>
					</div>
				</div>
			</div>
			<Empty
				v-if="!coreectList || !coreectList.length"
				tip="当前暂无订正记录"
			/>
			<template v-else>
				<div class="list-container">
					<el-table
						class="list-table"
						v-loading="loading"
						:data="coreectList"
						:header-cell-style="tableHeaderStyle"
						style="width: 100%"
					>
						<el-table-column
							prop="id"
							label="编号"
							align="center"
							fixed
						>
						</el-table-column>
						<el-table-column
							label="项目名称"
							align="center"
						>
							<template slot-scope="scope">
								{{scope.row.apply.project_name}}
							</template>
						</el-table-column>
						<el-table-column
							label="申报企业信息"
							width="250"
						>
							<template slot-scope="scope">
								<p>申报单位: {{scope.row.apply.enterprise_name || '--'}}</p>
								<p>申报人: {{scope.row.apply.user_name || '--'}}</p>
								<p>联系方式: {{scope.row.user.mobile || '--'}}</p>
							</template>
						</el-table-column>
						<el-table-column
							prop="apply_status"
							label="资料订正发起部门"
							align="center"
							width="200"
						>
							<template slot-scope="scope">
								{{scope.row.department.name}}
							</template>
						</el-table-column>
						<el-table-column
							label="发起时间"
							align="center"
							width="180px"
						>
							<template
								slot-scope="scope"
							>
								<p v-if="scope.row.created_at">{{scope.row.created_at*1000 | formatDate('YYYY-MM-DD HH:mm:ss')}}</p>
								<p v-else>--</p>
							</template>
						</el-table-column>
						<el-table-column
							prop="status_name"
							label="订正状态"
							align="center"
							width="120px"
						>
						</el-table-column>
						<el-table-column
							label="订正提交时间"
							align="center"
							width="180px"
						>
							<template
								slot-scope="scope"
							>
								<p v-if="scope.row.submit_time">{{scope.row.submit_time*1000 | formatDate('YYYY-MM-DD HH:mm:ss')}}</p>
								<p v-else>--</p>
							</template>
						</el-table-column>
						<el-table-column
							prop="audit_time"
							label="审核时间"
							align="center"
							width="180px"
						>
							<template
								slot-scope="scope"
							><p v-if="scope.row.audit_time">{{scope.row.audit_time*1000 | formatDate('YYYY-MM-DD HH:mm:ss')}}</p>
								<p v-else>--</p>
							</template>
						</el-table-column>
						<el-table-column
							label="操作"
							width="140"
							align="center"
							fixed="right"
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
	FETCH_COREECT_LIST
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
		}
	},
	data() {
		return {
			status: '',
			keyword: '',
			loading: false,
			coreectList: [],
			statusOptions: [
				{
					id: '',
					name: '全部'
				},
				{
					id: 3,
					name: '待订正'
				},
				{
					id: 4,
					name: '待审核'
				},
				{
					id: 5,
					name: '订正无效'
				},
				{
					id: 6,
					name: '重新订正'
				},
				{
					id: 7,
					name: '订正完成'
				},
				{
					id: 8,
					name: '订正作废'
				}
			]
		};
	},
	// 初始化项目申报列表，返回data数据
	async asyncData({$axios}) {
		let params = {
			page: 1,
			per_page: 10
		};

		let result = await $axios.get(FETCH_COREECT_LIST, {params});
		const data = result || {};

		return {
			coreectList: data.data,
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

			// 状态
			if (this.status) {
				filterParams.status = this.status;
			}
			// 关键词
			if (this.keyword) {
				filterParams.keyword = this.keyword;
			}
			return filterParams;
		},
	},
	methods: {
		// 根据过滤参数请求订正记录
		fetchCorrectList(pageId, pageSize) {
			let params = {
				...this.filterParams,
				page: pageId || this.pagination.pageId,
				per_page: pageSize || this.pagination.pageSize,
			};

			this.loading = true;
			this.$axios.get(FETCH_COREECT_LIST, {params: params})
				.then((data = {}) => {
					this.loading = false;
					this.coreectList = data.data;
					this.pagination = {
						...this.pagination,
						total: data.total,
						pageId: data.current_page,
						pageCount: data.total_page
					};
				})
				.catch(error => {
					console.log(error);
					this.loading = false;
					this.$message.error('获取数据失败，请稍后重试');
				});
		},

		// 查看
		handleLookClick(row) {
			this.$router.push({
				path: '/personal/detail',
				query: {
					id: row.id
				}
			});
		},
		// 处理塞选状态改变
		handleStatusChange() {
			this.fetchCorrectList(1);
		},
		// 触发分页
		handlePageChange(pageId) {
			this.fetchCorrectList(pageId);
		},
		// 搜索
		handleSearch() {
			this.fetchCorrectList();
		},
		// 表头样式
		tableHeaderStyle({rowIndex}) {
			if (rowIndex == 0) {
				return 'background:#F9FBFC;font-weight:bold; color: #3B3B3B';
			}
		},

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
      .search-box {
        margin-left: 20px;
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

