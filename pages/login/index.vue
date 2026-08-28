<template>
	<div class="login-page-container">
		<Header/>
		<div class="login-content-container">
			<div class="login-container">
				<div class="bg-container">

				</div>
				<div class="form-container">
					<div class="titles">
						<span
							class="title"
							:class="{'showBorder': account}"
							@click="accountLogin('account-login')"
						>账号登录</span>
						<span
							class="title"
							:class="{'showBorder': mobile}"
							@click="mobileLogin('mobile-login')"
						>短信登录</span>
					</div>
					<div class="components-cotainer">
						<component
							:is="comName"
							ref="component"
							:pictureCaptcha="pictureCaptcha"
							:pictureCaptchaKey ="pictureCaptchaKey"
						></component>
						<div class="tips">
							<span @click="handleToRegiseter">注册新用户</span>
							<span @click="resetPassword">忘记密码？</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<Footer/>
	</div>
</template>
<script>
import Header from '@/components/user/header';
import AccountLogin from '@/components/user/accountLogin';
import MobileLogin from '@/components/user/mobileLogin';
import Footer from '@/components/user/footer';
import {
	FETCH_CAPTCHACODE
} from '@/utils/urls.js';
export default {
	layout: 'empty',
	components: {
		Header,
		'account-login': AccountLogin,
		'mobile-login': MobileLogin,
		Footer
	},
	data() {
		return {
			pictureCaptcha: '',
			pictureCaptchaKey: '',
			account: true,
			mobile: false,
			comName: 'account-login'
		};
	},
	methods: {
		accountLogin(newVal) {
			this.mobile = false;
			this.account = true;
			this.comName = newVal;
		},
		mobileLogin(newVal) {
			this.account = false;
			this.mobile = true;
			this.comName = newVal;
		},
		// 密码重置
		resetPassword() {
			this.$router.push({name: 'reset'});
		},
		// 重新注册
		handleToRegiseter() {
			this.$router.push({name: 'register'});
		}
	},
	// 图形验证码
	mounted() {
		this.$axios.get(FETCH_CAPTCHACODE).then(({key, img}) => {
			this.pictureCaptcha = img,
			this.pictureCaptchaKey = key;
		}).catch(error => {
			console.log(error);
		});
	}
};
</script>
<style lang="less" >
@import "~assets/css/common_avairail.less";
.login-page-container {
  width: 100%;
  .login-content-container {
    height: 600px;
    background: @primaryColor;
    padding-top: 52.5px;
    .login-container {
      width: 1050px;
      height: 495px;
      margin: auto;
      border-radius: 10px;
      position: relative;
      .bg-container {
        width: 600px;
        height: 495px;
        background: url("~assets/images/bg-login-new.png");
        background-size: 100% 100%;
      }
      .form-container {
        border-top-right-radius: 10px;
        border-bottom-right-radius: 10px;
        background: #ffffff;
        box-sizing: content-box;
        position: absolute;
        right: 0;
        top: 0;
        width: 450px;
        height: 495px;
        .el-button--primary {
          height: 55px;
          border-radius: 0;
          font-size:23px;
          font-weight: Bold;
          font-family: Microsoft YaHei;
          margin: 20px 0;
        }
      }
      .components-cotainer {
        padding: 0 40px;
      }
      .titles {
        width: 100%;
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
        border-bottom: 1px solid #cccccc;
        .title {
          padding: 20px;
          font-size: 23px;
          color: @textColor;
          font-weight: Bold;
          font-family: Microsoft YaHei;
          width: 50%;
          text-align: center;
          border-bottom: 2px solid #ffffff;
        }
        .showBorder {
          color: @primaryColor;
          border-bottom: 2px solid @primaryColor;
        }
      }
      .tips {
        color: @primaryColor;
        margin-top: 10px;
        width: 100%;
        display: flex;
        justify-content: space-between;
      }
    }
  }
}
</style>

