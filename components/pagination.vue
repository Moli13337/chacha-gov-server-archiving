<template>
	<div>
		<div
			class="pagination-container"
			v-if="pagination.total > pagination.pageSize"
		>
			<el-pagination
				background
				prev-text='上一页'
				next-text='下一页'
				layout="prev, pager, next"
				:total="pagination.total"
				:current-page.sync="pagination.pageCount"
				:page-count="pagination.totalpage"
				:page-size="pagination.pageSize"
				@current-change="handleCurrentChange"
			/>
		</div>
	</div>
</template>
<script>
export default {
	props: {
		pagination: {
			type: Object,
			default() {
				return {};
			}
		}
	},
	methods: {
		handleCurrentChange(pageId) {
			console.log('pageId', pageId);
			this.$emit('onPageChange', pageId);
		}
	},
};
</script>
<style lang="less">
@import "~assets/css/common_avairail.less";
 .pagination-container {
    margin: 40px 0;
    display: flex;
    justify-content: center;
    .number {
      width: 42px;
      height: 41px;
      line-height: 41px;
      background: #ffffff;
    }
    .el-icon-more {
      background: none;
      height: 41px;
      line-height: 41px;
    }
    .btn-prev, .btn-next {
      height: 41px;
      width: 92px;
      font-size: 20px;
      background: #ffffff;
    }
  }
</style>
