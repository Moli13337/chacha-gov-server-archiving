<template>
	<div class="breadcrumb-row">
		<el-divider direction="vertical"></el-divider>
		当前位置：
		<el-breadcrumb separator-class="el-icon-arrow-right">
			<el-breadcrumb-item
				v-for="(item, index) in breadcrumbList"
				:key="index"
				@click.native="handleClick(item.path)"
			>
				{{item.breadcrumbName}}
			</el-breadcrumb-item>
		</el-breadcrumb>
	</div>
</template>
<script>
import routes from '../config/routes';
export default {
	computed: {
		breadcrumbList() {
			return this.$route.matched.map(item => {
				const theItem = routes.find(route => route.path === item.path) || {};

				return {
					path: item.path,
					breadcrumbName: theItem.breadcrumbName || ''
				};
			});
		}
	},
	methods: {
		handleClick(path) {
			this.$router.push(path || '/');
		}
	},
	mounted() {
		console.log(this.$route.matched);
	}
};

</script>
<style lang="less" scoped>
@import '../assets/css/common_avairail';
.breadcrumb-row {
	display: flex;
	align-items: center;
	color: @textColor;
	font-size: 14px;
  font-family:Microsoft YaHei;
  cursor: pointer;
  padding-bottom: 15px;
  .el-divider--vertical {
    width: 5px;
    background: @primaryColor;
  }
	.breadcrumb-item {
		&:after {
			content: '/';
			margin-left: 5px;
			margin-right: 5px;
    }
	}
	.breadcrumb-item:nth-last-of-type(1) {
		font-weight: bold;
		&:after {
			display: none;
		}
  }
}
</style>

