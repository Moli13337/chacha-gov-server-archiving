<template>
	<el-form
		:model="accountForm"
		:rules="rules"
		ref="accountForm"
		class="form-items"
	>
		<el-form-item
			prop="mobile"
		>
			<el-input
				v-model="accountForm.mobile"
				placeholder="请输入手机号/邮箱"
			></el-input>
			<img
				class="icon ic_customer"
				src="~assets/images/ic_customer@2x.png"
			>
		</el-form-item>
		<el-form-item
			prop="password"
		>
			<el-input
				v-model="accountForm.password"
				placeholder="请输入登录密码"
				show-password
			>
			</el-input>
			<img
				class="icon ic_customer"
				src="~assets/images/ic_password@2x.png"
			>
		</el-form-item>
		<el-form-item
			prop="captcha"
		>
			<el-row :gutter="20">
				<el-col :span="16">
					<div>
						<el-input
							v-model="accountForm.captcha"
							placeholder="请输入验证码"
						></el-input>
					</div>
				</el-col>
				<el-col :span="8">
					<div class="code-image">
						<img
							class="check-code"
							:src="newCaptcha"
							@click="fetchPictureCode"
						>
					</div>
				</el-col>
			</el-row>
		</el-form-item>
		<el-button
			type="primary"
			@click="handleAccountLogin('accountForm')"
			class="login"
		>登录</el-button>
	</el-form>
</template>
<script>
import storage from '~/utils/storage';
import {
	FETCH_CAPTCHACODE,
	USER_ACCOUNT_LOGIN,
	FETCH_USER_INFO
} from '@/utils/urls';
export default {
	props: {
		pictureCaptcha: {
			type: String,
			default: ''
		},
		pictureCaptchaKey: {
			type: String,
			default: ''
		}
	},
	data() {
		return {
			newCaptcha: this.pictureCaptcha,
			accountForm: {
				// 账户
				mobile: '',
				// 登录密码
				password: '',
				// 图片验证码
				captcha: '',
				key: this.pictureCaptchaKey
			},
			rules: {
				mobile: [
					{required: true, message: '请输入账户名称', trigger: 'blur'}
				],
				password: [
					{required: true, message: '请输入登录密码', trigger: 'blur'},
					{min: 6, max: 20, message: '登录密码格式不正确', trigger: 'blur'}
				],
				captcha: [
					{required: true, message: '请输入验证码', trigger: 'blur'},
					{max: 6, message: '验证码格式不正确', trigger: 'blur'}
				]
			}
		};
	},
	watch: {
		pictureCaptcha(newV) {
			this.newCaptcha = newV;
		},
		pictureCaptchaKey(newV) {
			this.accountForm.key = newV;
		}
	},
	methods: {
		// 请求图片验证码
		fetchPictureCode() {
			this.$axios.get(FETCH_CAPTCHACODE)
				.then(({img, key}) => {
					this.newCaptcha = img;
					this.accountForm.key = key;
				});
		},
		// 账户登录
		handleAccountLogin(formName) {
			this.$refs[formName].validate(valid => {
				if (valid) {
					this.$axios.post(USER_ACCOUNT_LOGIN, {
						account: this.accountForm.mobile,
						password: this.accountForm.password,
						captcha: this.accountForm.captcha,
						key: this.accountForm.key
					})
						.then(({token}) => {
							storage.setItem('token', token);
							return this.$axios.get(FETCH_USER_INFO);
						})
						.then(userInfo => {
							storage.setItem('user_info', userInfo);
							storage.removeItem('saveData');
							this.$message.success('登录成功');
							if (storage.getItem('agentRoute') && storage.getItem('agentRoute') == '/agent') {
								this.$router.push({name: 'agent'});
								storage.removeItem('agentRoute');
							} else {
								this.$router.push({name: 'index'});
							}
						}).catch(({message}) => {
							storage.removeItem('token');
							storage.removeItem('touser_info');
							this.$message.error(message || '登录失败');
							this.fetchPictureCode();
						});
				} else {
					return false;
				}
			});
		}
	}
};
</script>
<style lang="less" >
.form-items {
  .el-form-item__content {
    position: relative;
    .el-input__inner {
      height: 49px;
      border-radius: 0;
      padding-left: 55px;
    }
    .icon {
      position: absolute;
      left: 0;
      top: 0;
      width: 49px;
      height: 49px;
    }
  }
  .login {
  width: 100%;
}
}
.code-image {
  width: 100%;
  height: 49px;
  .check-code {
    width: 100%;
    height: 100%;
  }
}

</style>

