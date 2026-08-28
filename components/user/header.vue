<template>
	<div class="header-container">
		<div class="container">
			<div class="logo-container">
				<div 	class="logo">
					产服通智慧企业服务平台
				</div>
				<div
					class="logo-text"
					v-if="this.$route.path=='/register'"
				>
					绑定手机号
				</div>
				<div
					class="logo-text"
					v-if="this.$route.path=='/register/agreement'"
				>
					用户服务协议
				</div>
				<div
					class="logo-text"
					v-if="this.$route.path=='/login'"
				>
					登录
				</div>
				<div
					class="logo-text"
					v-if="this.$route.path=='/certificationEnterprise'"
				>
					企业认证
				</div>
				<div
					class="logo-text"
					v-if="this.$route.path=='/reset'"
				>
					密码重置
				</div>
			</div>
			<div
				class="tip-cotainer"
				v-if="this.$route.path!='/register/agreement'"
				@click="handelBackHome"
			>
				<img
					src="~assets/images/ic_backhome@2x.png"
					class="ic-backhome"
				>
				<span
					class="text"
				>进入首页</span>
			</div>
		</div>
	</div>
</template>
<script>
export default {
	methods: {
		handelBackHome() {
			this.$router.push({name: 'index'});
		},
	},
	mounted() {
		console.log(this.$route.path);
	}
};
</script>
<style lang="less" scope>
@import '~assets/css/common_avairail.less';
.header-container {
  width: 100%;
  background: @backGroundColor;
  height: 100px;
  // margin-bottom: 60px;
  .container {
    width: 1024px;
    height: 100%;
    margin: auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
    .logo-container {
      display: flex;
      height: 100%;
      justify-content: space-between;
      align-items: center;
      .logo {
        height: 76px;
        font-size: 30px;
        line-height: 76px;
        font-weight: 500;
        color: @primaryColor;
      }
      .logo-text {
        // height: 49px;
        border-left: 1px solid #cccccc;
        margin-left: 26px;
        padding: 0 26px;
        font-size: 23px;
        color: @textColor;
        font-weight: Regular;
      }
    }
    .tip-cotainer {
      height: 100%;
      display: flex;
      align-items: center;
    }
    .tip-cotainer {
      display: flex;
      justify-content: space-between;
      align-items: center;
    .ic-backhome {
        width:25px;
        height:22px;
        margin-right: 6px;
    }
    }
    .text {
      color:@primaryColor;
      font-size: 23px;
    }

  }
}
</style>

