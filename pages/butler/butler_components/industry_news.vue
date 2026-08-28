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
					<nuxt-link to="/butler">管家服务</nuxt-link>
				</el-breadcrumb-item>
				<el-breadcrumb-item>
					<div>动态信息详情</div>
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
	INFORMATION_DETAIL
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

		return Promise.all([
			$axios.get(INFORMATION_DETAIL, {params}),
		])
			.then(([detail]) => ({
				detail,
			}))
			.catch(e => {
				if (e.code == 11001) {
					return {
						detail: {}
					};
				}
			});
	}
};
</script>
<style lang="less" scoped>

</style>

