<template>
	<div id="dialog-container">
		<el-dialog
			:title="name"
			:visible.sync="visible"
			@close="handleClose"
		>
			<div
				class="tip"
				v-if="name !== '我要咨询'"
			>
				您好！感谢您给我们提出宝贵的建议。我们会进行严格保密。您的个人信息绝不会向外公开，请根据您的实际情况或内心真实想法如实填写。
			</div>
			<el-form
				:model="form"
				:rules="rules"
				ref="form"
			>
				<el-form-item
					:label="title"
					label-width="120px"
					prop="title"
					v-if="tipType != 3"
				>
					<el-input
						type="text"
						placeholder="请输入标题"
						v-model="form.title"
					>
					</el-input>
				</el-form-item>
				<el-form-item
					:label="contentTitle"
					label-width="120px"
					prop="content"
				>
					<el-input
						type="textarea"
						placeholder="请输入内容"
						v-model="form.content"
						maxlength="300"
						show-word-limit
					>
					</el-input>
				</el-form-item>
				<el-form-item
					label-width="120px"
					prop="pictureCode"
				>
					<el-row>
						<el-col :span="15">
							<el-input
								v-model="form.pictureCode"
								placeholder="输入验证码"
							>
							</el-input>
						</el-col>
						<el-col :span="8">
							<div
								class="code-image-box"
								@click="fetchPictureCode"
							>
								<img
									:src="img"
									class="code-image"
								>
							</div>
						</el-col>
					</el-row>
				</el-form-item>
			</el-form>
			<div
				slot="footer"
				class="dialog-footer"
			>
				<el-button @click="handleClose">取 消</el-button>
				<el-button
					type="primary"
					@click="submite('form')"
				>提交</el-button>
			</div>
		</el-dialog>
	</div>
</template>
<script>
import {
	USER_FEED_BACK,
	FETCH_CAPTCHACODE,
	CHECK_CAPTCHA_CODE
} from '@/utils/urls';
export default {
	props: {
		visible: {
			type: Boolean,
			default: false
		},
		tipType: {
			type: Number,
			default: 3
		},
		title: {
			type: String,
			default: ''
		},
		contentTitle: {
	    type: String,
			default: ''
		},
		name: {
			type: String,
			default: ''
		},
		picturCode: {
			type: String,
			default: ''
		},
		captchaKey: {
			type: String,
			default: ''
		}
	},
	data() {
		return {
			img: '',
			key: '',
			form: {
				title: '',
				content: '',
				pictureCode: ''
			},
			rules: {
				title: [
					{required: true, message: '标题不能为空', trigger: 'blur'},
				],
				content: [
					{required: true, message: '内容不能为空', trigger: 'blur'},
				],
				pictureCode: [
					{required: true, message: '验证码不能为空', trigger: 'blur'},
					{validator: this.checkPictureCode, trigger: 'blur'}
				]
			},
		};
	},
	methods: {
		// 提交反馈
		submite(formName) {
			this.$refs[formName].validate((valid) => {
				if (valid) {
					let params = {
						captcha: this.form.pictureCode,
						key: this.key,
						title: this.form.title || '我要咨询',
						type: this.tipType,
						content: this.form.content
					};

					this.$axios.post(USER_FEED_BACK, params).then(() => {
						this.$message.success('感谢您的反馈，我们会尽快处理');
						this.fetchPictureCode();
						this.$emit('changeVisible', false);
						this.form.title = '';
						this.form.content = '';
						this.form.pictureCode = '';
						this.$refs['form'].resetFields();
					}).catch(({message}) => {
						this.$message.error(message);
					});
				} else {
					this.$message.error('提交失败,请检查信息是否填写完整');
					return false;
				}
			});
		},
		// 关闭弹框
		handleClose() {
			this.$emit('changeVisible', false);
			this.form.title = '';
			this.form.content = '';
			this.form.pictureCode = '';
			this.$refs['form'].resetFields();
		},
		// 图形验证码校验
		checkPictureCode(rule, value, callback) {
			let params = {
				captcha: value,
				key: this.key
			};

			this.$axios.get(CHECK_CAPTCHA_CODE, params)
				.then(res => {
					console.log(res);
					callback();
				})
				.catch(() => callback(new Error('验证码输入不正确')));
		},
		// 点击刷新图形验证码
		fetchPictureCode() {
			this.$axios.get(FETCH_CAPTCHACODE)
				.then(({img, key}) => {
					this.img = img;
					this.key = key;
				});
		},
	},

	// 监听图形验证码图片地址和key值并赋值显示
	watch: {
		picturCode(img) {
			this.img = img;
		},
		captchaKey(key) {
			this.key = key;
		}
	}
};
</script>

<style lang="less" >
@import '~assets/css/common_avairail.less';
#dialog-container {
  .el-dialog {
    width: 40%;
  }
  .el-dialog__body {
    padding-right: 50px;
  }
  .el-input__inner, .el-textarea__inner {
    border-radius: 0;
  }
  .el-textarea__inner  {
    height: 100px;
  }
  .dialog-footer {
    text-align: left;
    margin-left: 120px;
  }
  .code-image-box{
     width: 100%;
     height: 100%;
  }
  .code-image {
  width: 100%;
  height: 100%;
  height: 40px;
  border: 1px solid @borderLine;
  margin-left: 10px;
}
.tip {
  padding: 0px 0px 20px 50px;
}
}

</style>

