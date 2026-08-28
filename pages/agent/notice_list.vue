<template>
	<div class="notice-container">
		<!-- 面包屑 -->
		<div class="breadcrumb-row">
			<el-divider direction="vertical"></el-divider>
			当前位置：
			<el-breadcrumb separator-class="el-icon-arrow-right">
				<el-breadcrumb-item>
					<nuxt-link to="/">首页</nuxt-link>
				</el-breadcrumb-item>
				<el-breadcrumb-item>
					<nuxt-link to="/agent">中介服务</nuxt-link>
				</el-breadcrumb-item>
				<el-breadcrumb-item>
					<div>通知列表</div>
				</el-breadcrumb-item>
			</el-breadcrumb>
		</div>
		<CommonList
			:isShowNew="true"
			:list="noticeList"
			title="通知列表"
			to="agent-detail"
		/>
		<Pagination
			:pagination="pagination"
			@onPageChange="handlePageChange"
		/>
	</div>
</template>
<script>
import {
	AGENTNOTIFY_LIST,
} from '@/utils/urls.js';
import CommonList from '@/components/common-list';
import Pagination from '@/components/pagination';
export default {
	components: {
		CommonList,
		Pagination
	},
	data() {
		return {
			params: {
				per_page: 10,
				page: 1
			}
		};
	},
	// 初始化通知列表数据
	async asyncData({$axios}) {
		let requestParams = {
			per_page: 10,
			page: 1
		};
		let result = await $axios.get(AGENTNOTIFY_LIST, {params: requestParams});

		return {
			noticeList: result.data || [],
			pagination: {
				total: result.total,
				pageCount: result.current_page,
				pageSize: result.per_page_num
			}
		};
	},
	methods: {
		handlePageChange(pageId) {
			let params = {
				per_page: 10,
				page: pageId
			};

			this.$axios.get(AGENTNOTIFY_LIST, {params: params}).then(res => {
				this.noticeList = res.data;
			}).catch(error => {
				this.$message.error(error.message);
			});
		}
	}

};
</script>
<style lang="less" scoped>

</style>
