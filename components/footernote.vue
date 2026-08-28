<template>
	<div class="footnote-container">
		<!-- 面包屑 -->
		<bread-crumb/>
		<div class="content">
			<div class="text-content">
				<div class="title-container">
					<el-divider></el-divider>
					<p class="title">{{title}}</p>
					<el-divider></el-divider>
				</div>
				<div
					class="text-container"
					v-html="content"
				>
				</div>
			</div>
		</div>
	</div>
</template>
<script>

import BreadCrumb from '~/components/breadcrumb';
export default {
	props: {
		title: {
			type: String,
			default: ''
		},
		content: {
			type: String,
			default: ''
		}
	},
	components: {
		BreadCrumb
	},
};
</script>
<style lang="less" >
@import '~assets/css/common_avairail.less';
@import '~assets/css/common.less';
.footnote-container {
  width: 100%;
  background: @backGroundColor;
  box-shadow:0px 0px 5px rgba(0,0,0,0.05);
  padding: 15px;
  .bread-crumb {
    line-height: 18px;
    border-bottom: 1px solid @borderLine;
    padding-bottom: 15px;
    .el-divider {
      width:5px;
      height:18px;
      background-color: @primaryColor;
    }
    .bread-crumb-tip {
      display: inline-block;
      height: 18px;
      line-height: 18px;
    }
  }
  .content {
    width: 100%;
    // border: 1px solid @borderLine;
    margin-top: 20px;
  }
  .title-container{
    .title {
    text-align: center;
    }
  }
  .text-container {
    margin: 20px 50px;
    border: 1px solid @borderLine;
    padding: 20px;
  }
}
</style>

