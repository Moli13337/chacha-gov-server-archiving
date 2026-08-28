<template>
	<div class="guid-content">
		<el-button
			type="primary"
			class="btn-complaint"
		>新手指南</el-button>
		<div class="text-content">
			<div
				v-html="content"
				v-if="content"
			></div>
			<div v-else>
				<div class="title-box">
					<p>产业扶持政策项目</p>
					<p>申报操作流程</p>
				</div>
				<div class="item">
					<p class="subtitle">第一步：打开浏览器（建议使用谷歌浏览器），登录“产服通智慧企业服务平台”网站，网址：<a
						class="link"
						href="http://service.wenjiang.gov.cn"
					>http://service.wenjiang.gov.cn</a></p>
					<img src="~assets/images/guide/first.png">
				</div>
				<div class="item">
					<p class="subtitle">第二步：用户点击首页右上角“注册”，填写个人真实姓名、手机号、验证码、设置密码进行个人账号注册。</p>
					<img src="../assets/images/guide/second.png">
					<p class="subtitle">已注册账号用户，直接点击“登录”，输入账号、密码、验证码进行登录。</p>
					<img src="../assets/images/guide/third.png">
				</div>
				<div class="item">
					<p class="subtitle">第三步：注册用户自动进入“企业认证”页面，通过填写企业名称、企业社会信用代码、法定代表人姓名、上传加盖公章的营业执照复印件进行企业认证。</p>
					<img src="../assets/images/guide/fourth.png">
					<p class="subtitle">已注册未及时进行企业认证的用户，可在“个人中心”-“我的主页”栏目中，点击“去认证企业”并上传加盖公章的营业执照复印件进行认证。
					</p>
					<img src="../assets/images/guide/five.png">
				</div>
				<div class="item">
					<p class="subtitle">第四步：通过企业认证后，用户点击首页菜单栏的“政策申报”，浏览、搜索并查看当前全部可在线申报的区级产业扶持政策项目。</p>
					<img src="../assets/images/guide/six.png">
				</div>
				<div class="item">
					<p class="subtitle">第五步：用户点击项目名称，可查看到项目申报详情，如政策依据、支持对象、支持标准、申报条件、申报材料、政策咨询等。</p>
					<img src="../assets/images/guide/seven.png">
				</div>
				<div class="item">
					<p class="subtitle">第六步：如符合申报要求需申报区级产业扶持项目，用户可点击项目左下角的“在线申报”。</p>
					<img src="../assets/images/guide/eight.png">
					<p class="subtitle">在“政策申报-申报详情”页面的用户，可点击右上角“在线申报”进行项目申报。</p>
					<img src="../assets/images/guide/nine.png">
				</div>
				<div class="item">
					<p class="subtitle">第七步：企业在线填写完整《温江区产业扶持专项资金申请表》相关信息并按要求上传合法合规附件材料，确认无误后点击提交完成申报。</p>
					<img src="../assets/images/guide/ten.png">
					<img src="../assets/images/guide/eleven.png">
				</div>
				<div class="item">
					<p class="subtitle">第八步：企业可在“个人中心-我的申报”中，查看已经提交的在线申报记录和审核详情。
					</p>
					<img src="../assets/images/guide/twelve.png">
				</div>
			</div>
		</div>
	</div>
</template>
<script>
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
	}
};
</script>

<style lang="less">
@import "~assets/css/common_avairail.less";
  .guid-content {
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
    .title-box {
      font-size: 25px;
      font-weight: 500;
      text-align: center;
      padding-bottom: 20px;
    }
    .link {
      color: @primaryColor;
      cursor: pointer;
    }
    .item {
      margin-bottom: 30px;
    }
    .subtitle {
      padding: 10px 0;
      font-weight: 500;
      font-size: 18px;
    }
  }

</style>

