<template>
	<div class="person-container">
		<!-- 面包屑 -->
		<div class="breadcrumb-row">
			<el-divider direction="vertical"></el-divider>
			当前位置：
			<el-breadcrumb separator-class="el-icon-arrow-right">
				<el-breadcrumb-item>
					<nuxt-link to="/butler">管家服务</nuxt-link>
				</el-breadcrumb-item>
				<el-breadcrumb-item>
					<nuxt-link to="/butler/enterprise_collect"> 企业意见征集</nuxt-link>
				</el-breadcrumb-item>
				<el-breadcrumb-item>
					<div>意见征集详情</div>
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
	OPTION_DETAIL
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
		const result = await $axios.get(OPTION_DETAIL, {params});
		const detail = result || {};

		return {
			detail
		};
	}
};
</script>
<style lang="less" scoped>

</style>

