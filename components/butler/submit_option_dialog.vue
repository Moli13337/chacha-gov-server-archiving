<template>
	<div class="submit-option-dialog ">
		<el-dialog
			:visible.sync="innerSubmitVisible"
			width="45%"
			center
			class="optionForm"
			@close="handleCloseDialog"
		>
			<template slot="title">
				<p
					class="title"
					style="background='red'"
				>企业意见征集</p>
			</template>
			<el-form
				:model="opinionForm"
				:rules="rules"
				ref="optionForm"
			>
				<el-form-item
					label="用户意见"
					label-width="120px"
					prop="content"
				>
					<el-input
						type="textarea"
						:rows="5"
						v-model="opinionForm.content"
						show-word-limit
						autocomplete="off"
						maxlength="500"
					></el-input>
				</el-form-item>
				<el-form-item
					label="反馈文件上传"
					label-width="120px"
				>
					<diy-upload
						v-model="file"
						multiple
						:tip="'支持上传DOC，WPS，DOCX，PDF，XLS，XLSX，JPG, JPEG, PNG，BMP, PPT，TXT格式的文件'"
						:limitSizePerFile="100"
						:accept="'.jpg,.JPG,.JPEG,.jpeg,.PNG,.png,.BMP,.bmp,.DOC,.doc,.DOCX,.docx,.WPS,.wps,.PDF,.pdf,.XLS,.xls,.XLSX,.xlsx'"
					/>
				</el-form-item>
				<el-form-item
					label-width="120px"
					prop="captcha"
				>
					<div class="qr-code-box">
						<div class="code-input">
							<el-input
								v-model="opinionForm.captcha"
								placeholder="请输入验证码"
							></el-input>
						</div>
						<div
							class="code-image"
							@click="handleChangeImage"
						>
							<img :src="pictureCode">
						</div>
					</div>
				</el-form-item>
			</el-form>
			<div
				slot="footer"
				class="dialog-footer"
			>
				<el-button @click="handleCancle">取 消</el-button>
				<el-button
					type="primary"
					@click="handleOptionSubmit"
				>确 定</el-button>
			</div>
		</el-dialog>
	</div>

</template>
<script>
import {
	OPTION_SUBMIT,
	FETCH_CAPTCHACODE
} from '@/utils/urls.js';
import DiyUpload from '@/components/butler/diy_upload.vue';
export default {
	components: {
		DiyUpload
	},
	props: {
		submitVisible: Boolean,
		captcha: {
			type: Object,
			default: function () {
				return {};
			}
		}
	},
	data() {
		return {
			innerSubmitVisible: this.submitVisible,
			file: [],
			opinionForm: {
				content: '',
				file: [],
				captcha: ''
			},
			pictureCode: this.captcha.pictureCode,
			captchaKey: this.captcha.captchaKey,
			rules: {
				content: [
					{required: true, message: '请输入用户意见', trigger: 'blur'}
				],
				captcha: [
					{required: true, message: '请输入验证码', trigger: 'blur'}
				]
			}
		};
	},
	computed: {
		params() {
			let params = {};

			params.content = this.opinionForm.content;
			params.captcha = this.opinionForm.captcha;
			params.key = this.captchaKey;
			params.id = this.$route.query.id;
			if (this.file && this.file.length > 0) {
				params.file = this.file.map(item => ({
					name: item.name,
					save_url: item.url
				}));
			}

			return params;
		}
	},
	watch: {
		submitVisible(val) {
			this.innerSubmitVisible = val;
		},
		captcha: {
			handler(val) {
				this.pictureCode = val.pictureCode;
				this.captchaKey = val.captchaKey;
			},
			deep: true
		}
	},
	methods: {
		handleCloseDialog() {
			this.innerSubmitVisible = false;
			this.$emit('changeSubmitVisible', false);
			this.$refs['optionForm'].resetFields();
			this.file = [];
		},
		// 提交表单
		handleOptionSubmit() {
			this.$refs['optionForm'].validate((valid) => {
				if (valid) {
					this.$axios.post(OPTION_SUBMIT, this.params).then(() => {
						this.$message.success('提交成功');
						this.handleCloseDialog();
					}).catch(error => {
						this.$message.error(error.message || '提交失败');
					});
				} else {
					console.log('error submit!!');
					return false;
				}
			});
		},
		handleCancle() {
			this.handleCloseDialog();
		},
		// 点击请求图片验证码
		fetchPictureCode() {
			this.$axios.get(FETCH_CAPTCHACODE)
				.then(({img, key}) => {
					this.pictureCode = img;
					this.captchaKey = key;
				}).catch(error => {
					console.log(error.message);
				});
		},
		handleChangeImage() {
			this.fetchPictureCode();
		},
	}
};
</script>
<style lang="less" scoped>
.submit-option-dialog {
    .qr-code-box {
      display: flex;
      justify-content: space-between;
    }
    .code-input {
      flex: 1;
    }
    .el-input__inner {
      border-radius: 0;
    }
    .code-image {
      width: 150px;
      border: 1px solid #DCDFE6;
      height: 40px;
      margin-left: 30px;
      img {
        width: 100%;
        height: 100%;
      }
    }
}
</style>
