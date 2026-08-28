<template>
	<div class="person-container">
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
					<div>一般警示名单</div>
				</el-breadcrumb-item>
			</el-breadcrumb>
		</div>
		<CommonList
			title="一般警示名单"
			:list="creditList"
			:isShowShelf="true"
			to="agent-organ_detail"
			type="credit"
		/>
		<Pagination
			:pagination="pagination"
			@onPageChange="handlePageChange"
		/>
	</div>
</template>
<script>
import {
	CREDIT_LIST
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
		};
	},
	async asyncData({$axios}) {
		let list = await $axios.get(CREDIT_LIST, {params: {credit_type: 1}});

		return {
			creditList: list.data || [],
			pagination: {
				total: list.total,
				pageCount: list.total_page,
				pageSize: 10
			}
		};
	},
};
</script>
<style lang="less" scoped>

</style>
