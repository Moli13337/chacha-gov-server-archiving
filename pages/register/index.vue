<template>
	<div class="container">
		<Header/>
		<div class="form-container">
			<el-form
				:model="ruleForm"
				:rules="rules"
				ref="register-form"
				class="register-form"
			>
				<!-- 用户名 -->
				<el-form-item
					prop="name"
					class="register-input"
				>
					<el-input
						type="text"
						v-model="ruleForm.name"
						placeholder="填写真实姓名"
						class="userinfo-input"
						clearable
						autocomplete="off"
					></el-input>
					<img
						src="~assets/images/register_icon_,customer@2x.png"
						class="icon icon-person"
					>
				</el-form-item>
				<!-- 手机号 -->
				<el-form-item
					prop="mobile"
					class="register-input"
				>
					<el-input
						type="Number"
						v-model="ruleForm.mobile"
						placeholder="输入手机号码"
						class="userinfo-input"
						clearable
					></el-input>
					<img
						src="~assets/images/register_icon_phone@2x.png"
						class="icon icon-phone"
					>
				</el-form-item>
				<!-- 图形验证码 -->
				<el-form-item
					prop="pictureCode"
					class="register-input"
				>
					<el-row :gutter="20">
						<el-col :span="16">
							<el-input
								type="number"
								v-model="ruleForm.pictureCode"
								placeholder="请输入验证码"
								class="userinfo-input"
								clearable
								@change="pictureCodeValid = true"
							></el-input>
						</el-col>
						<el-col :span="8">
							<img
								class="checkCode-img"
								:src="captcha"
								@click="fetchPictureCode"
							>
						</el-col>
					</el-row>
				</el-form-item>
				<!-- 短信验证码 -->
				<el-form-item
					prop="code"
					class="register-input"
				>
					<el-input
						type="Number"
						v-model="ruleForm.code"
						placeholder="填写短信验证码"
						class="userinfo-input"
						suffix='发送短信验证码'
						@change="smsCodeValid = false"
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
						src="../../assets/images/register_icon_,message@2x.png"
						class="icon icon-message"
					>
				</el-form-item>
				<!-- 密码 -->
				<!-- <el-form-item
					prop="password"
					class="register-input"
				>
					<el-input
						type="password"
						v-model="ruleForm.password"
						placeholder="设置由数字和字母组成的6-20位登录密码"
						class="userinfo-input"
						:show-password="true"
						clearable
						autocomplete="off"
					></el-input>
					<img
						src="../../assets/images/register_icon_,password@2x.png"
						class="icon icon-password"
					>
				</el-form-item> -->
				<p class="errorTip">{{errorTip}}</p>
				<el-form-item prop="checked">
					<el-checkbox
						v-model="ruleForm.checked"
					>
						<span
							class="user-tip-gray"
						>已阅读并同意
						</span>
						<span
							class="user-tip"
							@click="loockAgreement"
						>《用户服务协议》
						</span>
					</el-checkbox>
				</el-form-item>
				<el-form-item>
					<el-button @click="handleSubmitClick">立即绑定</el-button>
				</el-form-item>
				<!-- <div
					class="backToLogin"
					@click="handelToLogin"
				>返回登录</div> -->
			</el-form>
		</div>
		<Footer/>
	</div>
</template>
<script>
import Header from '../../components/user/header';
import Footer from '../../components/user/footer';
import storage from '~/utils/storage';
import {
	CHECK_MS_CODE,
	CHECK_CAPTCHA_CODE,
	FETCH_CAPTCHACODE,
	USER_REGISTER,
	SEND_MS_CODE,
	FETCH_USER_INFO
} from '@/utils/urls.js';

export default {
	layout: 'empty',
	components: {
		Header,
		Footer
	},
	data() {
		return {
			errorTip: '',
			isCodeSending: false,
			pictureCodeValid: false,
			smsCodeValid: false,
			counter: 60,
			ruleForm: {
				// 用户名
				name: '',
				// 手机号
				mobile: '',
				// 短信验证码
				code: '',
				// 密码
				// password: '',
				// 验证码
				pictureCode: '',
				// 用户协议
				checked: true,
			},
			rules: {
				name: [
					{validator: this.checkName, trigger: 'blur'},
					{max: 10, message: '最大长度为10个字符', trigger: 'blur'},
				],
				mobile: [
					{validator: this.checkPone, trigger: 'blur'},
					{min: 11, max: 11, message: '手机号码格式不正确！请输入11位正确手机号', trigger: 'blur'},
				],
				code: [
					{required: true, message: '验证码不能为空!', trigger: 'blur'},
					{min: 6, max: 20, message: '短信验证码格式不正确', trigger: 'blur'},
					{validator: this.checkSMSCode, trigger: 'blur'},
				],
				// password: [{validator: this.checkPass, trigger: 'blur'}],
				pictureCode: [
					{required: true, message: '验证码不能为空!', trigger: 'blur'},
					{validator: this.checkCaptchaCode, trigger: 'blur'}
				],
				checked: [
					{validator: this.checkAgreement, trigger: 'change'}
				],
			}
		};
	},
	// 初始化图片验证码，返回data数据
	async asyncData({$axios}) {
		let {key, img} = await $axios.get(FETCH_CAPTCHACODE);

		return {
			// 图形验证码图片地址
			captcha: img,
			// 图形验证码key值
			captchaKey: key
		};
	},
	methods: {
		handelToLogin() {
			// this.$router.push({name: 'login'});
			this.tencentLogin();
		},
		showErrorTip(message = '') {
			this.errorTip = message;
		},
		clearErrorTip() {
			this.errorTip = '';
		},
		// 去两端空格
		trim(value) {
			return value.replace(/(^\s*)|(\s*$)/g, '');
		},
		// 协议认证
		checkAgreement(rule, val, callback) {
			if (!val) {
				return callback(new Error('请选择用户协议！'));
			} else {
				callback();
			}
		},
		// 姓名验证
		checkName(rule, val, callback) {
			const value = this.trim(val);

			if (!value) {
				return callback(new Error('请输入真实用户名称！'));
			} else {
				const reg = /^[\u4e00-\u9fa5_a-zA-Z]+$/;

				if (reg.test(value)) {
					callback();
				} else {
					return callback(new Error('姓名格式不正确！请输入中文或字母'));
				}
			}
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
		// 校验短信验证码
		checkSMSCode(rule, val, callback) {
			if (!this.ruleForm || !this.ruleForm.mobile || this.smsCodeValid) {
				callback();
				return;
			}
			if ((/^\d{6}$/).test(val)) {
				this.$axios.post(CHECK_MS_CODE, {
					code: val,
					mobile: this.ruleForm.mobile
				})
					.then(() => {
						this.smsCodeValid = true;
						callback();
					}).catch(({message}) => {
						this.smsCodeValid = false;
						callback(new Error(message || '短信验证码错误!'));
					});
			} else {
				return callback(new Error('短信验证码不正确!'));
			}
		},
		// 校验图形验证码
		checkCaptchaCode(rule, val, callback) {
			if (this.pictureCodeValid) {
				callback();
				return;
			}
			this.$axios.get(CHECK_CAPTCHA_CODE, {
				params: {
					captcha: val,
					key: this.captchaKey
				}
			})
				.then(() => {
					this.pictureCodeValid = true;
					callback();
				}).catch((message) => {
					this.pictureCodeValid = false;
					callback(new Error(message || '验证码错误!'));
				});
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
		// 提交绑定表单
		handleSubmitClick() {
			this.$refs['register-form'].validate((valid) => {
				if (valid) {
					this.$axios.post(USER_REGISTER, {
						name: this.ruleForm.name,
						mobile: this.ruleForm.mobile,
						// password: this.ruleForm.password,
						code: this.ruleForm.code,
						tag: 2,
						uid: this.$cookies.get('uin')
					})
						.then(({token}) => {
							storage.setItem('token', token);
							return this.$axios.get(FETCH_USER_INFO);
						})
						.then(userInfo => {
							storage.setItem('user_info', userInfo);
							// 跳转到上一个页面
							this.$router.go(-1);
							this.$message.success('绑定成功');
							this.$router.push({name: 'certification'});
						})
						.catch(({message}) => {
							this.$message.error(message || '绑定失败，请重试');
						});
				}
			});
		},
		toCertification() {
			// this.$router.push({name: 'login'});
			this.tencentLogin();
		},
		// 点击请求图片验证码
		fetchPictureCode() {
			this.$axios.get(FETCH_CAPTCHACODE)
				.then(({img, key}) => {
					this.captcha = img;
					this.captchaKey = key;
				});
		},
		// 进入协议页面
		loockAgreement() {
			let routeData = this.$router.resolve({name: 'register-agreement'});

			window.open(routeData.href, '_blank');
		},
		// 发送验证码计时器
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
		// 获取短信验证码
		handleSMSCodeSend() {
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
				captcha: this.ruleForm.pictureCode,
				tag: 2
			})
				.then(() => {
					this.$message.success('短信验证码发送成功，请留意您的手机');
					this.sendSMSCodeCountdown();
				}).catch(({message}) => {
					this.$message.error(message || '发送验证码失败，请重试');
				});
		}
	}
};
</script>
<style lang="less" >
@import '~assets/css/common_avairail.less';
.container {
  width: 100%;
  .form-container {
    width: 440px;
    margin: auto;
    margin-top: 60px;
    margin-bottom: 40px;
    .register-form {
      .register-input{
        position: relative;
        .userinfo-input .el-input__inner {
          height: 49px;
          border-radius: 0;
        }
        .icon {
          width:24px;
          height:24px;
          position: absolute;
          top: 12px;
          left: 12px;
        }
        .el-input__inner {
          padding-left: 50px;
        }
        .checkCode-img {
          width: 100%;
          height: 49px;
        }
      }
      .el-button--default {
        width: 100%;
        border-radius: 0;
        height: 49px;
        background: @primaryColor;
        color: #ffffff;
      }
      .user-tip-gray {
        color: @textColor;
      }
      .user-tip,.backToLogin {
        color: @primaryColor;
      }
    }
    .errorTip {
      color: #CC0000;
      font-size: 14px;
    }
    .send-code-btn {
      height: 26px;
      padding: 0 20px 0 10px;
      margin-top: 12px;
    }
  }
}
</style>

