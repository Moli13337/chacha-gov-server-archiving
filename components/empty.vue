<template>
	<div class="empty-container">
		<img
			class="empty-background"
			src="~assets/images/personal_background_photo.png"
		>
		<p class="empty-tip">{{tip}}</p>
		<el-button
			v-if="hasBtn"
			class="certification-btn"
			type="primary"
			@click="handleCertificationClick"
		>
			去认证企业
		</el-button>
	</div>
</template>
<script>
export default {
	props: {
		tip: {
			type: String,
			default: '暂无数据'
		},
		hasBtn: {
			type: Boolean,
			default: false
		}
	},
	methods: {
		handleCertificationClick() {
			this.$router.push({name: 'certification'});
		},
	}
};
</script>
<style lang="less">
@import '~assets/css/common_avairail.less';
  .empty-container {
      min-height: 584px;
      padding-top: 100px;
      display: flex;
      justify-content: flex-start;
      align-items: center;
      flex-direction: column;
      background: #ffffff;
      margin-top: 30px;
      .empty-background {
        width: 193px;
        height: 193px;
      }
      .empty-tip {
        font-size:21px;
        font-family:Microsoft YaHei;
        font-weight:400;
        color: @textColor;
        margin-top: 20px;
      }
  }
</style>

