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
					<div>通知详情</div>
				</el-breadcrumb-item>
			</el-breadcrumb>
		</div>
		<Detail
			:detail="detail"
		/>
	</div>
</template>
<script>
import {
	NOTICE_DETAIL
} from '@/utils/urls';
import Detail from '@/components/detail';
export default {
	components: {
		Detail
	},
	async asyncData({$axios, query}) {
		let params = {
			id: query && query.id || 0
		};
		const result = await $axios.get(NOTICE_DETAIL, {params});
		const detail = result || {};

		return {
			detail
		};
	}
};
</script>
<style lang="less" scoped>

</style>

