<template>
	<div class="update-phone-container">
		<Header/>
		<div class="content-container">
			<div class="steps">
				<el-steps
					:active="active"
					finish-status="success"
				>
					<el-step title="验证身份"></el-step>
					<el-step title="修改手机号码"></el-step>
					<el-step title="完成更换"></el-step>
				</el-steps>
			</div>
			<div v-if="active === 0">
				<div class="old-phone-box">
					<p class="old-phone">已绑定的手机号: {{encryptionMobile}}</p>
					<p>若该手机号已无法使用请联系工作人员</p>
				</div>
				<div class="form-container">
					<el-form
						:model="form"
						:rules="rules"
						ref="first-form"
						class="form-items"
					>
						<el-form-item
							prop="captcha"
						>
							<el-row :gutter="20">
								<el-col :span="18">
									<div>
										<el-input
											v-model="form.captcha"
											placeholder="请输入验证码"
										></el-input>
									</div>
								</el-col>
								<el-col :span="6">
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
							prop="msmCode"
						>
							<el-input
								v-model="form.msmCode"
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
						</el-form-item>
					</el-form>
					<el-button
						type="primary"
						class="next-step"
						@click="handleNext"
					>下一步
					</el-button>
				</div>
				<div class="tips">
					<div>
						<h1>温馨提醒</h1>
						<p>• 为保障您的帐号安全，变更重要信息需要身份验证</p>
						<p>• 若有疑问请联系我的管家或拨打 82668503（周一至周五 9:00-17:00）</p>
					</div>
				</div>
			</div>
			<div
				class="form-container"
				v-else-if="active === 1"
			>
				<el-form
					:model="secondForm"
					:rules="secondRules"
					ref="second-form"
					class="form-items"
				>
					<el-form-item
						prop="mobile"
					>
						<el-input
							v-model="secondForm.mobile"
							placeholder="请输入新的手机号"
						></el-input>
					</el-form-item>
					<el-form-item
						prop="captcha"
					>
						<el-row :gutter="20">
							<el-col :span="18">
								<div>
									<el-input
										v-model="secondForm.captcha"
										placeholder="请输入验证码"
									></el-input>
								</div>
							</el-col>
							<el-col :span="6">
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
						prop="msmCode"
					>
						<el-input
							v-model="secondForm.msmCode"
							placeholder="请输入短信验证码"
						>
							<template slot="suffix">
								<el-divider direction="vertical"></el-divider>
								<el-button
									type="text"
									class="send-code-btn"
									:disabled="isSecondCodeSending"
									@click="handleSecondSMSCodeSend"
								>{{isSecondCodeSending ? counter + 's 后重新发送': '发送短信验证码'}}</el-button>
							</template>
						</el-input>
					</el-form-item>
				</el-form>
				<el-button
					type="primary"
					class="next-step"
					@click="handleNext"
				>下一步
				</el-button>
			</div>
			<div
				class="form-container"
				v-else
			>
				<div class="sucess-image-box">
					<img
						src="~assets/images/ic_sucess@2x.png"
						class="sucess-image"
					>
				</div>
				<div class="old-phone-box">
					<p class="old-phone">手机号码更换成功</p>
					<p>您可以在下次使用新号码进行登录</p>
				</div>
				<el-button
					type="primary"
					class="next-step back"
					@click="handleNext"
				>返回
				</el-button>
			</div>
		</div>
		<Footer/>
	</div>
</template>
<script>
import Header from '@/components/user/header';
import Footer from '@/components/user/footer';
import {
	FETCH_CAPTCHACODE,
	FETCH_USER_INFO,
	CHECK_CAPTCHA_CODE,
	SEND_MS_CODE,
	UNBUNGDING_PHONE_FIRST,
	UNBUNGDING_PHONE_SECOND
} from '@/utils/urls.js';
export default {
	layout: 'empty',
	components: {
		Header,
		Footer,
	},
	data() {
		return {
			isCodeSending: false,
			isSecondCodeSending: false,
			pictureCodeValid: false,
			counter: 60,
			active: 0,
			accountForm: '',
			newCaptcha: '',
			key: '',
			form: {
				captcha: '',
				msmCode: ''
			},
			secondForm: {
				mobile: '',
				captcha: '',
				msmCode: ''
			},
			rules: {
				msmCode: [
					{required: true, message: '请输入短信验证码', trigger: 'blur'},
					{min: 6, max: 6, message: '短信验证码格式不正确', trigger: 'blur'}
				],
				captcha: [
					{required: true, message: '验证码不能为空', trigger: 'blur'},
					{validator: this.checkCaptchaCode, trigger: 'change'}
				]
			},
			secondRules: {
				mobile: [
					{required: true, message: '请输入新手机号', trigger: 'blur'},
					{min: 11, max: 11, message: '手机号格式不正确', trigger: 'blur'}
				],
				msmCode: [
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
	// 请求用户信息
	async asyncData({$axios}) {
		const result = await $axios.get(FETCH_USER_INFO);

		const data = result || {};

		return {
			mobile: data.mobile,
		};
	},
	computed: {
		encryptionMobile() {
			return this.mobile.replace(/(\d{3})(\d{4})(\d{4})/, '$1****$3');
		}
	},

	methods: {
		// 请求图片验证码
		fetchPictureCode() {
			this.$axios.get(FETCH_CAPTCHACODE)
				.then(({img, key}) => {
					this.newCaptcha = img;
					this.key = key;
				});
		},

		// 点击下一步
		handleNext() {
			if (this.active === 0) {
				this.$refs['first-form'].validate(valid => {
					if (valid) {
						this.$axios.post(UNBUNGDING_PHONE_FIRST, {
							code: this.form.msmCode
						})
							.then(data => {
								console.log(data);
								this.active = 1;
								sessionStorage.setItem('active', this.active);
								this.fetchPictureCode();
							}).catch(({message}) => {
								this.$message.error(message || '短信验证码错误');
							});
					} else {
						return false;
					}
				});
			} else if (this.active === 1) {
				this.$refs['second-form'].validate(valid => {
					if (valid) {
						this.$axios.post(UNBUNGDING_PHONE_SECOND, {
							mobile: this.secondForm.mobile,
							code: this.secondForm.msmCode
						})
							.then(data => {
								console.log(data);
								this.active = 2;
								sessionStorage.setItem('active', this.active);
							}).catch(({message}) => {
								this.$message.error(message || '短信验证码错误');
								this.fetchPictureCode();
							});
					} else {
						sessionStorage.removeItem('active');
						return false;
					}
				});
			} else {
				this.$router.push({name: 'index-personal-index-mine'});
			}
		},

		// 获取短信验证码
		handleSMSCodeSend() {
			console.log('handleSMSCodeSend', this.pictureCodeValid);
			if (!this.mobile) {
				this.$message.error('您还未绑定手机号');
				return;
			}
			if (!this.pictureCodeValid) {
				this.$message.error('请输入正确的图形验证码');
				return;
			}
			this.$axios.post(SEND_MS_CODE, {
				mobile: this.mobile,
				key: this.key,
				captcha: this.form.captcha,
				tag: 1
			})
				.then(() => {
					this.$message.success('短信验证码发送成功，请留意您的手机');
					this.sendSMSCodeCountdown();
				}).catch(({message}) => {
					this.$message.error('短信验证码发送失败, ' + message);
					this.fetchPictureCode();
				});
		},

		// 第二次发送短信验证码
		handleSecondSMSCodeSend() {
			if (!this.secondForm.mobile) {
				this.$message.error('您还未绑定手机号');
				return;
			}
			if (!this.pictureCodeValid) {
				console.log('handleSecondSMSCodeSend', this.pictureCodeValid);
				this.$message.error('请输入正确的图形验证码');
				return;
			}
			this.$axios.post(SEND_MS_CODE, {
				mobile: this.secondForm.mobile,
				key: this.key,
				captcha: this.secondForm.captcha,
				tag: 2
			})
				.then(() => {
					this.$message.success('短信验证码发送成功，请留意您的手机');
					this.secondSendSMSCodeCountdown();
				}).catch(({message}) => {
					this.$message.error('短信验证码发送失败, ' + message);
					this.fetchPictureCode();
				});
		},

		// 发送验证码定时器
		sendSMSCodeCountdown() {
			this.isCodeSending = true;
			this.counter = 60;
			let intervel = setInterval(() => {
				this.counter--;

				if (this.counter <= 0) {
					this.isCodeSending = false;
					clearInterval(intervel);
				}
			}, 1000);
		},

		// 第二次发送验证码定时器
		secondSendSMSCodeCountdown() {
			console.log('isSecondCodeSending');
			this.isSecondCodeSending = true;
			this.counter = 60;
			let intervel = setInterval(() => {
				this.counter--;

				if (this.counter <= 0) {
					this.isSecondCodeSending = false;
					clearInterval(intervel);
				}
			}, 1000);
		},

		// 校验图形验证码
		checkCaptchaCode(rule, val, callback) {
			this.$axios.get(CHECK_CAPTCHA_CODE, {
				captcha: val,
				key: this.key
			})
				.then(() => {
					console.log('checkCaptchaCode1', this.pictureCodeValid);
					this.pictureCodeValid = true;
					console.log('checkCaptchaCode2', this.pictureCodeValid);
					callback();
				}).catch((message) => {
					this.pictureCodeValid = false;
					callback(new Error(message || '验证码错误!'));
				});
		},
	},

	mounted() {
		this.fetchPictureCode();
		if (sessionStorage.getItem('active')) {
			let currentActive = parseInt(sessionStorage.getItem('active'));

			console.log(currentActive);
			this.active = currentActive;
		}
	},
	watch: {
		newCaptcha(newV) {
			this.newCaptcha = newV;
		},
		key(newV) {
			this.key = newV;
		},
	}

};
</script>
<style lang="less">
@import "~assets/css/common_avairail.less";
.update-phone-container {
  width: 100%;
  .steps {
    margin-bottom: 50px;
  }
  .content-container {
    width: 800px;
    margin: auto;
    min-height: 600px;
    padding: 50px 0;
  }
  .old-phone-box {
    text-align: center;
    // padding-top: 50px;
    .old-phone {
    font-size: 20px;
    font-weight: 400;
  }
  }
  .tips {
    border: 1px solid @borderLine;
    margin-top: 50px;
    padding: 20px;
    background: #f3f3f3
  }
.code-image {
  width: 100%;
  height: 49px;
  .check-code {
    width: 100%;
    height: 100%;
  }
}
  .next-step {
    width: 400px;
    border-radius: 0;
    height: 50px;
    margin: auto;
  }
  .send-SMS-code {
    height: 50px;
    .send-code-btn {
      padding-top: 30px;
      padding-right:10px;
    }
  }
  .form-container {
    width: 400px;
    padding-top: 50px;
    margin: auto;
     .el-input__inner {
      height: 50px;
      border-radius: 0;
    }
  }
  .sucess-image-box {
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
  }
  .sucess-image {
    width: 100px;
    height: 100px;
  }
  .back {
    margin-top: 20px;
  }
}
</style>

