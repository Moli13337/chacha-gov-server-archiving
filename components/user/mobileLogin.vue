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
				placeholder="请输入手机号"
				suffix='发送短信验证码'
				class="send-SMS-code"
			>
			</el-input>
			<img
				class="icon ic_customer"
				src="~assets/images/ic_customer@2x.png"
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
		<el-form-item
			prop="messageCode"
		>
			<el-input
				v-model="accountForm.messageCode"
				placeholder="请输入短信验证码"
			>
				<template slot="suffix">
					<el-divider direction="vertical"></el-divider>
					<el-button
						type="text"
						class="send-code-btn"
						:disabled="isCodeSending"
						@click="handleSMSCodeSend"
					>{{isCodeSending ? counter + 's 后重新发送': '发送短信验证码'}}</el-button>
				</template>
			</el-input>
			<img
				class="icon ic_customer"
				src="~assets/images/ic_password@2x.png"
			>
		</el-form-item>
		<el-button
			type="primary"
			@click="handleMobileLogin('accountForm')"
			class="login"
		>登录</el-button>
	</el-form>
</template>
<script>
import storage from '~/utils/storage';
import {
	FETCH_CAPTCHACODE,
	CHECK_CAPTCHA_CODE,
	SEND_MS_CODE,
	USER_MOBILE_LOGIN,
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
			isCodeSending: false,
			pictureCodeValid: false,
			counter: 60,
			newCaptcha: this.pictureCaptcha,
			accountForm: {
				mobile: '',
				messageCode: '',
				captcha: '',
				key: this.pictureCaptchaKey
			},
			rules: {
				mobile: [
					{required: true, message: '请输入手机号', trigger: 'blur'},
					{min: 11, max: 11, message: '手机号格式不正确！', trigger: 'blur'}
				],
				messageCode: [
					{required: true, message: '请输入短信验证码', trigger: 'blur'},
					{min: 6, max: 6, message: '短信验证码格式不正确', trigger: 'blur'}
				],
				captcha: [
					{required: true, message: '验证码不能为空', trigger: 'blur'},
					{validator: this.checkCaptchaCode, trigger: 'change'}
				]
			}
		};
	},
	methods: {
		// 校验图形验证码
		checkCaptchaCode(rule, val, callback) {
			this.$axios.get(CHECK_CAPTCHA_CODE, {
				captcha: val,
				key: this.key
			})
				.then(() => {
					this.pictureCodeValid = true;
					callback();
				}).catch((message) => {
					this.pictureCodeValid = false;
					callback(new Error(message || '验证码错误!'));
				});
		},
		// 获取短信验证码
		handleSMSCodeSend() {
			if (!this.accountForm || !this.accountForm.mobile) {
				this.$message.error('请输入正确的手机号');
				return;
			}
			if (!this.pictureCodeValid) {
				this.$message.error('请输入正确的图形验证码');
				return;
			}
			this.$axios.post(SEND_MS_CODE, {
				mobile: this.accountForm.mobile,
				key: this.accountForm.key,
				captcha: this.accountForm.captcha
			})
				.then(() => {
					this.$message.success('短信验证码发送成功，请留意您的手机');
					this.sendSMSCodeCountdown();
				}).catch(({message}) => {
					this.$message.error('短信验证码发送失败, ' + message);
				});
		},
		// 手机短信登录
		handleMobileLogin(formName) {
			this.$refs[formName].validate(valid => {
				if (valid) {
					console.log(this.accountForm.mobile, this.accountForm.messageCode, this.accountForm.captcha, this.accountForm.key);
					this.$axios.post(USER_MOBILE_LOGIN, {
						mobile: this.accountForm.mobile,
						code: this.accountForm.messageCode,
						captcha: this.accountForm.captcha,
						key: this.accountForm.key,
						tag: 1,
					})
						.then(({token}) => {
							storage.setItem('token', token);
							return this.$axios.get(FETCH_USER_INFO);
						})
						.then(userInfo => {
							storage.setItem('user_info', userInfo);
							storage.removeItem('saveData');
							this.$message.success('登录成功');
							this.$router.push({name: 'index'});
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
		},
		// 发送验证码定时器
		sendSMSCodeCountdown() {
			this.isCodeSending = true;
			this.counter = 60;
			let intervel = setInterval(() => {
				this.counter--;
				console.log(this.counter);

				if (this.counter <= 0) {
					this.isCodeSending = false;
					clearInterval(intervel);
				}
			}, 1000);
		},
		// 请求图片验证码
		fetchPictureCode() {
			console.log(22);
			this.$axios.get(FETCH_CAPTCHACODE)
				.then(({img, key}) => {
					this.newCaptcha = img;
					this.accountForm.key = key;
				});
		},
	}
};
</script>
<style lang="less" >
.form-items {
  .el-form-item__content {
    position: relative;
    .el-input__inner {
      height: 50px;
      border-radius: 0;
      padding-left: 55px;
    }
    .icon {
      position: absolute;
      left: 0;
      top: 0;
      width: 50px;
      height: 50px;
    }
  }
  .login {
    width: 100%;
  }
  .send-SMS-code {
    height: 50px;
    .send-code-btn {
      padding: 20px;
      padding-right:10px;
    }
  }
}
</style>

