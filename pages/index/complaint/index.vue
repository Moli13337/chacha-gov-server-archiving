<template>
	<div class="complaint-container">
		<!-- 面包屑 -->
		<bread-crumb/>
		<div class="content">
			<!-- <el-button
				type="primary"
				class="btn-complaint"
			>我要投诉</el-button> -->
			<div class="text-content">
				<!-- <div>
					<p>尊敬的用户：</p>您好！感谢您给我们提出宝贵的建议。我们会进行严格保密。您的个人信息绝不会向外公开，请根据您的实际情况或内心真实想法如实填写。
				</div> -->
				<!-- <el-form
					:model="ruleForm"
					ref="ruleForm"
					label-width="120px"
					class="form-content"
					:rules="rules"
				>
					<el-form-item
						label="投诉标题:"
						prop="title"
					>
						<el-input v-model="ruleForm.title"></el-input>
					</el-form-item>
					<el-form-item
						label="投诉内容:"
						prop="content"
					>
						<el-input
							type="textarea"
							maxlength="500"
							show-word-limit
							v-model="ruleForm.content"
						></el-input>
					</el-form-item>
					<el-form-item
						label="请输入验证码:"
						prop="captcha"
						id="password-row"
					>
						<el-row>
							<el-col :span="10">
								<div class="pictur-code">
									<el-input v-model="ruleForm.captcha"></el-input>
								</div>
							</el-col>
							<el-col :span="6">
								<div class="code-image">
									<img
										:src="picturCode"
										@click="fetchPictureCode"
									/>
								</div>
							</el-col>
						</el-row>
					</el-form-item>
					<el-form-item label>
						<el-button
							type="primary"
							class="submit form-btn"
							@click="onSubmit('ruleForm')"
						>提交</el-button>
						<el-button
							class="clear form-btn"
							@click="handleBack"
						>返回</el-button>
					</el-form-item>
				</el-form> -->
				<div>
					<img src="../../../assets/images/home-build.png">
					<p class="tip">本功能正在建设中</p>
				</div>
			</div>
		</div>
	</div>
</template>
<script>
import BreadCrumb from '~/components/breadcrumb';
import {
	USER_FEED_BACK,
	FETCH_CAPTCHACODE,
	CHECK_CAPTCHA_CODE
} from '@/utils/urls.js';
export default {
	components: {
		BreadCrumb
	},
	data() {
		return {
			ruleForm: {
				title: '',
				content: '',
				captcha: ''
			},
			rules: {
				title: [
					{required: true, message: '请填写您要投诉的标题!', trigger: 'blur'},
					{max: 20, message: '标题字符长度不超过20个字符!', trigger: 'blur'}
				],
				content: [
					{required: true, message: '请填写您要投诉的内容!', trigger: 'blur'}
				],
				captcha: [
					{required: true, message: '验证码不能为空!', trigger: 'blur'},
					{validator: this.checkCaptchaCode, trigger: 'change'}
				]
			}
		};
	},
	methods: {
		onSubmit(formName) {
			this.$refs[formName].validate(valid => {
				if (valid) {
					let params = {
						type: 2,
						key: this.captchaKey,
						...this.ruleForm
					};

					this.$axios
						.post(USER_FEED_BACK, params)
						.then(() => {
							this.$message.success('您的建议已成功提交，感谢您宝贵的建议');
							this.$router.go(-1);
						})
						.catch(({message}) => {
							this.$message.error(message);
						});
				} else {
					this.$message.error('信息填写不完整，请检查填写内容');
				}
			});
		},
		// 点击请求图片验证码
		fetchPictureCode() {
			this.$axios.get(FETCH_CAPTCHACODE).then(({img, key}) => {
				this.picturCode = img;
				this.captchaKey = key;
			});
		},
		// 校验图形验证码
		checkCaptchaCode(rule, val, callback) {
			this.$axios
				.get(CHECK_CAPTCHA_CODE, {
					captcha: val,
					key: this.captchaKey
				})
				.then((res) => {
					console.log(res);
					callback();
				})
				.catch(() => {
					callback(new Error('验证码错误!'));
				});
		},
		handleBack() {
			this.$router.go(-1);
		}
	},
	mounted() {
		console.log(this.$route.matched);
	},

	// 初始化图片验证码，返回data数据
	async asyncData({$axios}) {
		let {key, img} = await $axios.get(FETCH_CAPTCHACODE);

		return {
			// 图形验证码图片地址
			picturCode: img,
			// 图形验证码key值
			captchaKey: key
		};
	}
};
</script>
<style lang="less" >
@import "~assets/css/common_avairail.less";
.complaint-container {
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
      height: 400px;
      color: @textColor;
      display: flex;
      justify-content: center;
      .tip {
        text-align: center;
        padding-top: 10px;
      }
    }
    // .form-content {
    //   width: 660px;
    //   margin: auto;
    //   padding: 60px 0 80px 0;
    //   .el-input__inner {
    //     border-radius: 0;
    //   }
    //   .el-textarea__inner {
    //     border-radius: 0;
    //     height: 117px;
    //   }
    //   .pictur-code {
    //     margin-right: 10px;
    //   }
    //   .form-btn {
    //     width: 148px;
    //     height: 39px;
    //     font-size: 16px;
    //     font-family: Microsoft YaHei;
    //     font-weight: 400;
    //     border-radius: 0;
    //   }
    // }
  }
}
</style>

