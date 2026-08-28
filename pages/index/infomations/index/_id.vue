<template>
	<div class="complaint-container">
		<!-- 面包屑 -->
		<bread-crumb/>
		<div class="content">
			<div class="text-content">
				<div class="title-container">
					<p class="title">我区开展应急处置“双盲”演练</p>
					<div class="tips"><p><span>发布日期：</span>2019-05-12    12：00</p><p><span>来源：</span>温江区企业服务中心</p></div>
					<el-divider></el-divider>
				</div>
				<div class="text-container">
					<section>
						<h4>
							一、欢迎使用产服通智慧企业服务平台
						</h4>
						感谢您使用我们的产品和服务(下称“服务”)。服务由上海市中小企业发展服务中心（下称“服务中心”)提供。上海市中小企业发展服务中心
						是全额拨款的公益性事业单位，是国家工业和信息化部认定的国家级中小企业公共服务示范平台。服务中心地址为中华人民共和国上海市徐汇区大木桥路108号。
						用户确认本服务协议后，本服务协议即在用户和上海市企业服务云之间产生法律效力，请用户务必在注册之前认真阅读协议内容，如有任何疑问可向平台客服咨询，无论用户事实上是否在注册之前认真阅读过协议内容，只要用户点击“确认”按钮��按照平台注册程序注册成功，其行为将被视为同意并签署本服务协议。本服务协议不涉及平台中用户之间产生的任何法律关系和法律纠纷。
					</section>
					<section>
						<h4>
							二、用户注册</h4>
						1、为了使用我们提供的服务，您需要创建一个云平台帐户。我们的账户分为三种：个人账户、企业账户、志愿专家账户。
						2、企业用户必须是具备完全民事行为能力的自然人，或者是具有合法经营资格的实体组织。无民事行为能力人、限制民事行为能力人以及无经营或特定经营资格的组织不当注册为上海市企业服务云用户或超过其民事权利或行为能力范围而从事交易的，其与上海市企业服务云之间的服务协议自动无效，一经发现，上海市企业服务云有权立即注销该用户，并追究其使用上海市企业服务云中一切“服务”的任何法律责任。
						3、企业用户注册是指用户登陆上海市企业服务云后，保证诸如电子邮件地址、联系电话、联系地址、邮政编码和所发布内容的有效性、安全性、真实性，保证上海市企业服务云及所有用户可以通过上述联系方式与其进行联系。同时，企业用户也有义务在相关资料实际变更时及时更新有关注册资料并保证不以他人或其他组织的资料在上海市企业服务云进行注册或认证。

					</section>

				</div>

			</div>
		</div>
	</div>
</template>
<script>

import BreadCrumb from '~/components/breadcrumb';
export default {
	components: {
		BreadCrumb
	},
	data() {
		return {
			ruleForm: {
				title: '',
				content: '',
				key: '',
				captcha: ''
			},
		};
	},
	methods: {
		onSubmit() {
			console.log(this.ruleForm);
		}
	},
	// 独立设置head信息
	head() {
		return {
			title: this.title,
			meta: [
				{hid: 'home', breadcrumbName: '我要投诉', path: '/'}
			]
		};
	},
	mounted() {
		console.log(this.$route.matched);
	},
};
</script>
<style lang="less" >
@import '~assets/css/common_avairail.less';
.complaint-container {
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
    // height: 500px;
    border: 1px solid @borderLine;
    margin-top: 20px;
    .title-container {
      .title {
        font-size:31px;
        font-family:Microsoft YaHei;
        font-weight:400;
        text-align: center;
        color: #3B3B3B;
        padding: 20px 0;
      }
      .tips {
        font-size:14px;
        font-family:Microsoft YaHei;
        font-weight:400;
        width: 400px;
        margin: auto;
        display: flex;
        justify-content: space-between;
      }
    }

    .text-content {
      padding: 0 60px;
      color: @textColor;
      section {
        margin-bottom: 20px;
      }
    }

  }
}
</style>

