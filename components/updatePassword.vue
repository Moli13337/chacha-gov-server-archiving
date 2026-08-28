<template>
	<div class="reset-container">
		<Header/>
		<el-form
			:model="ruleForm"
			:rules="rules"
			ref="ruleForm"
			class="form-container"
		>
			<el-form-item prop="mobile">
				<el-input
					v-model.trim="ruleForm.mobile"
					placeholder="请输入手机号"
					clearable
				></el-input>
				<img
					class="icon ic_customer"
					src="~assets/images/register_icon_phone@2x.png"
				>
			</el-form-item>
			<el-form-item
				prop="pictureCaptcha"
			>
				<el-row :gutter="20">
					<el-col :span="16">
						<div>
							<el-input
								v-model="ruleForm.pictureCaptcha"
								placeholder="请输入验证码"
							></el-input>
						</div>
					</el-col>
					<el-col :span="8">
						<div class="code-image">
							<img
								class="check-code"
								:src="captcha"
								@click="fetchPictureCode"
							>
						</div>
					</el-col>
				</el-row>
			</el-form-item>
			<el-form-item prop="messageCode">
				<el-input
					v-model.trim="ruleForm.messageCode"
					placeholder="填写短信验证码"
					suffix='发送短信验证码'
					clearable
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
					src="~assets/images/register_icon_,message@2x.png"
				>
			</el-form-item>
			<el-form-item prop="passWord">
				<el-input
					v-model="ruleForm.passWord"
					placeholder="设置由数字和字母组成的6-20位登录密码"
					clearable
					show-password
				></el-input>
				<img
					class="icon ic_customer"
					src="~assets/images/register_icon_,password@2x.png"
				>
			</el-form-item>
			<el-form-item prop="repassWord">
				<el-input
					v-model.trim="ruleForm.repassWord"
					placeholder="请重新输入密码"
					clearable
					show-password
				></el-input>
				<img
					class="icon ic_customer"
					src="~assets/images/register_icon_,password@2x.png"
				>
			</el-form-item>
			<el-form-item>
				<el-button
					class="next-step"
					type="primary"
					@click="update('ruleForm')"
				>确认重置</el-button>
			</el-form-item>
		</el-form>
		<Footer/>
		<!-- 认证成功对话框 -->
		<dailog-component
			:visible="this.dialogVisible"
			title="密码重置成功"
			message="即将跳转至登录页面"
			buttonText="马上登录"
		></dailog-component>
	</div>
</template>
<script>
import Header from '@/components/user/header';
import Footer from '@/components/user/footer';
import DailogComponent from '@/components/certificationEnterprise/dailogcomponent';
import storage from '~/utils/storage';
import {
	CHECK_MS_CODE,
	CHECK_CAPTCHA_CODE,
	FETCH_CAPTCHACODE,
	SEND_MS_CODE,
	UPDATE_PASSWORD
} from '@/utils/urls.js';
export default {
	layout: 'empty',
	components: {
		Header,
		Footer,
		DailogComponent
	},
	data() {
		return {
			isCodeSending: false,
			dialogVisible: false,
			pictureCodeValid: false,
			counter: 60,
			ruleForm: {
				mobile: '',
				messageCode: '',
				passWord: '',
				repassWord: '',
				pictureCaptcha: ''
			},
			rules: {
				mobile: [
					{required: true, message: '请输入手机号', trigger: 'blur'},
					{
						min: 11,
						max: 11,
						message: '手机号码格式不正确！请输入11位正确手机号',
						trigger: 'blur'
					},
					{validator: this.checkPone, trigger: 'blur'}

				],
				messageCode: [
					{required: true, message: '请输入短信验证码', trigger: 'blur'},
					{min: 6, max: 20, message: '短信验证码格式不正确', trigger: 'change'},
					{validator: this.checkCode, trigger: 'change'},

				],
				passWord: [
					{required: true, message: '请输入密码', trigger: 'blur'},
					{validator: this.checkPass, trigger: 'blur'}],
				repassWord: [
					{required: true, message: '请再次输入密码', trigger: 'blur'},
					{validator: this.validatePass, trigger: 'blur'}],
				pictureCaptcha: [
					{required: true, message: '请输入验证码', trigger: 'blur'},
					{validator: this.checkCaptchaCode, trigger: 'change'}
				]
			}
		};
	},
	methods: {
		// 去两端空格
		trim(value) {
			return value.replace(/(^\s*)|(\s*$)/g, '');
		},
		// 手机号验证
		checkPone(rule, val, callback) {
			const value = this.trim(val);
			const reg = /^1([38][0-9]|4[579]|5[0-3,5-9]|6[6]|7[0135678]|9[89])\d{8}$/;

			if (!value) {
				return callback(new Error('请输入手机号！'));
			} else if (reg.test(value)) {
				// 后端缺认号码的唯一性
				callback();
			} else {
				return callback(new Error('手机号码格式不正确！请输入11位正确手机号'));
			}
		},
		// 在次校验密码
		validatePass(rule, value, callback) {
			if (value === '') {
				callback(new Error('请再次输入密码'));
			} else if (value !== this.ruleForm.passWord) {
				callback(new Error('两次输入密码不一致!'));
			} else {
				callback();
			}
		},
		//  验证码验证
		checkCode(rule, val, callback) {
			const value = this.trim(val);

			const reg = /^\d{6}$/;

			if (!value) {
				return callback(new Error('请输入短信验证码！'));
			} else if (value.length == 6 && reg.test(value)) {
				callback();
			} else {
				return callback(new Error('短信验证码不正确!'));
			}
		},
		// 登录密码验证
		checkPass(rule, val, callback) {
			const value = this.trim(val);

			const reg = /^[a-zA-Z0-9]{6,20}$/;

			if (!value) {
				return callback(new Error('请设置登录密码！'));
			} else if (reg.test(value)) {
				callback();
			} else {
				return callback(new Error('密码格式不正确!'));
			}
		},
		// 获取短信验证码
		handleSMSCodeSend() {
			console.log(this.pictureCodeValid);
			if (!this.ruleForm || !this.ruleForm.mobile) {
				this.$message.error('请输入正确的手机号');
				return;
			}
			if (!this.pictureCodeValid) {
				this.$message.error('请输入正确的图形验证码');
				return;
			}
			this.$axios.post(SEND_MS_CODE, {
				mobile: this.ruleForm.mobile,
				key: this.captchaKey,
				captcha: this.ruleForm.pictureCaptcha
			})
				.then(() => {
					this.$message.success('短信验证码发送成功，请留意您的手机');
					this.sendSMSCodeCountdown();
				}).catch(() => {
					this.$message.error('短信验证码发送失败');
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
		// 点击请求图片验证码
		fetchPictureCode() {
			this.$axios.get(FETCH_CAPTCHACODE)
				.then(({img, key}) => {
					this.captcha = img;
					this.captchaKey = key;
				});
		},
		// 校验图形验证码
		checkCaptchaCode(rule, val, callback) {
			this.$axios.get(CHECK_CAPTCHA_CODE, {
				captcha: val,
				key: this.captchaKey
			})
				.then(() => {
					this.pictureCodeValid = true;
					callback();
				}).catch((message) => {
					this.pictureCodeValid = false;
					callback(new Error(message || '验证码错误!'));
				});
		},
		// 密码重置
		update(formName) {
			this.$refs[formName].validate(valid => {
				if (valid) {
					this.$axios.post(UPDATE_PASSWORD, {
						mobile: this.ruleForm.mobile,
						code: this.ruleForm.messageCode,
						password: this.ruleForm.passWord,
						tag: 1
					})
						.then((data) => {
							console.log(data);
							storage.removeItem('token');
							storage.removeItem('user_info');
							storage.setItem('token', data.token);
							storage.setItem('user_info', data);
							this.$message.success('重置成功');
							this.$router.push('/');
						})
						.catch((data) => {
							console.log(data);
							this.$message.error(data.message);
						});
				} else {
					return false;
				}
			});
		}
	},
	// 初始化图片验证码，返回data数据
	async asyncData({$axios}) {
		let {key, img} = await $axios.get(FETCH_CAPTCHACODE,);

		return {
			// 图形验证码图片地址
			captcha: img,
			// 图形验证码key值
			captchaKey: key
		};
	},

};
</script>
<style lang="less" scope>
@import "~assets/css/common_avairail.less";
.reset-container {
  width: 100%;
  .form-container {
    width: 440px;
    margin: auto;
    margin-top: 60px;
    margin-bottom: 100px;
    .el-input__inner {
      height: 50px;
      border-radius: 0;
      padding-left: 50px;
    }
    .el-button--primary {
      width: 100%;
      height: 55px;
      border-radius: 0;
      background: @primaryColor;
      font-size: 24px;
      font-weight: Bold;
      font-family: Microsoft YaHei;
    }
    .el-form-item__content {
      position: relative;
      .icon {
        width: 25px;
        height: 25px;
        position: absolute;
        left: 10px;
        top: 10px;
      }
    }
  }
  .btn-container {
    .el-form-item__content {
      .el-button--default {
        width: 45%;
        height: 55px;
        border-radius: 0;
        border: 1px solid @primaryColor;
        font-size: 24px;
        color: @primaryColor;
      }
    }
  }
}
.next-step {
  width: 100%;
}
.send-code-btn {
  height: 100%;
  padding-right:10px;
}
.code-image {
  .check-code {
    width: 100%;
    height:50px;
  }
}
</style>

