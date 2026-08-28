<template>
	<div class="guide-container">
		<!-- 面包屑 -->
		<div class="breadcrumb-row">
			<el-divider direction="vertical"></el-divider>
			当前位置：
			<el-breadcrumb separator-class="el-icon-arrow-right">
				<el-breadcrumb-item>
					<nuxt-link to="/">首页</nuxt-link>
				</el-breadcrumb-item>
				<el-breadcrumb-item>
					<div>新手指南</div>
				</el-breadcrumb-item>
			</el-breadcrumb>
		</div>
		<guide
			:title="title"
		/>
	</div>
</template>
<script>
import guide from '~/components/guide';
export default {
	components: {
		guide
	},
	data() {
		return {
			title: '',
			content: ''
		};
	},
	methods: {
		changeGuide() {
			let type = this.$route.query.guideType;

			if (type == 1) {
				this.title = '新手指南';
				// 请求富文本
			} else if (type == 2) {
				this.title = '申报指南';
				// 请求富文本
				console.log(type);
				this.content = `<p>尊敬的用户：</p>您好！感谢您给我们提出宝贵的建议。我们会进行严格保密。您的个人信息绝不会向外公开，请根据您的实际情况或内心真实想法如实填写。`;
			}
		}
	},
	mounted() {
		this.changeGuide();
	},
	watch: {

		$route(to, from) {
			this.changeGuide();
		}
	}
};
</script>
<style lang="less" >
@import "~assets/css/common_avairail.less";
.guide-container {
  width: 100%;
  background: @backGroundColor;
  box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.05);
  padding: 15px;
  .bread-crumb {
    line-height: 18px;
    border-bottom: 1px solid @borderLine;
    padding-bottom: 15px;
    .el-divider {
      width: 5px;
      height: 18px;
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
    padding-bottom: 20px;
    min-height: 500px;
    border: 1px solid @borderLine;

    .btn-complaint {
      margin: 40px;
      width: 109px;
      height: 35px;
      line-height: 0.5px;
      border-radius: 20px;
    }
    .text-content {
      padding: 0 60px;
      color: @textColor;
    }
  }
}
</style>

