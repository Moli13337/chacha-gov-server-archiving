<template>
	<div class="container">
		<Header/>
		<el-form
			:model="ruleForm"
			:rules="rules"
			ref="certification-form"
			label-width="140px"
			class="form-container"
		>
			<!-- 企业名称 -->
			<el-form-item
				label="企业名称:"
				prop="name"
			>
				<el-input v-model="ruleForm.name"></el-input>
			</el-form-item>
			<!--  企业社会信用代码-->
			<el-form-item
				label="企业社会信用代码:"
				prop="creditCode"
			>
				<el-input v-model="ruleForm.creditCode"></el-input>
			</el-form-item>
			<!-- 法定代表人 -->
			<el-form-item
				label="法定代表人:"
				prop="representative"
			>
				<el-input v-model="ruleForm.representative"></el-input>
			</el-form-item>
			<!-- 上传营业执照 -->
			<el-form-item
				label="上传营业执照:"
				prop="businessUrl"
			>
				<el-upload
					class="image-upload"
					action=""
					list-type="picture-card"
					:limit="1"
					:data="{ business_id: businessId }"
					:http-request="customUpload"
					:on-preview="handleUploadPreview"
					:on-remove="handleUploadRemove"
					:on-success="handleUploadSuccess"
					:on-error="handleUploadError"
					:beforeUpload="beforeAvatarUpload"
					:on-progress="handleUploadProgress"
				>
					<i class="el-icon-plus"/>
				</el-upload>
				<el-dialog :visible.sync="previewVisible">
					<img
						width="100%"
						:src="dialogImageUrl"
					>
				</el-dialog>
				<div class="license-tip">
					*请上传企业的营业执照图片,
					并加盖公章(原图大小上限100MB，上传文件只能是.jpg,.jpeg,.png,.JPG,.JPEG,.PNG格式）。
					<p>	*加盖公章不要盖住统一社会信用代码和公司名称，否则容易导致认证失败</p>
				</div>
			</el-form-item>
			<el-form-item class="btn-container">
				<el-button
					type="primary"
					@click="handleSubmitClick"
					:loading="submiting"
				>立即认证</el-button>
				<el-button @click="toHome">跳过</el-button>
			</el-form-item>
		</el-form>
		<Footer/>
		<!-- 认证成功对话框 -->
		<common-dialog
			:visible="dialog.visible"
			:type="dialog.type"
			:title="dialog.title"
			:message="dialog.message"
			:errorTip="dialog.errorTip"
			:buttonText="dialog.buttonText"
			:onButtonClick="dialog.onButtonClick"
			v-on:update:visible="dismissDialog"
		/>
	</div>
</template>
<script>
import Header from '~/components/user/header';
import Footer from '~/components/user/footer';
import CommonDialog from '~/components/common-dialog';
import {
	CERTIFICATION,
	UPLOAD_FILE,
	FETCH_USER_INFO
} from '~/utils/urls.js';
import storage from '~/utils/storage.js';
export default {
	layout: 'empty',
	components: {
		Header,
		Footer,
		CommonDialog
	},
	data() {
		return {
			loading: true,
			dialogImageUrl: '',
			previewVisible: false,
			dialog: {
				visible: false,
				type: '',
				title: '',
				message: '',
				buttonText: '',
				errorTip: '',
				onButtonClick: null
			},
			submiting: false,
			ruleForm: {
				name: '',
				creditCode: '',
				representative: '',
				businessUrl: ''
			},
			rules: {
				name: [
					{required: true, message: '请填写企业名称', trigger: 'blur'},
					{max: 50, message: '最大长度为50个字符', trigger: 'blur'}
				],
				creditCode: [
					{required: true, message: '请填写企业社会信用代码', trigger: 'blur'},
					{max: 20, message: '最大长度为20个字符', trigger: 'blur'}
				],
				representative: [
					{required: true, message: '请填写企业法定代表人', trigger: 'blur'},
					{max: 50, message: '最大长度为50个字符', trigger: 'blur'}
				],
				businessUrl: [
					{required: true, message: '请上传营业执照'}
				]
			}
		};
	},
	computed: {
		businessId: function () {
			let date = new Date().getTime();

			return `wenjiang-${date}-${date.toString().substr(-5)}`;
		}
	},
	methods: {
		// 上传进度条
		handleUploadProgress(event, file) {
			console.log('event', 111);
			console.log('file', file);
			// 进度减一，为了避免上传进度为100%卡住的问题
		},
		// 自定义上传请求
		customUpload({file, data, filename, onProgress}) {
			let params = new FormData();

			params.append(filename, file);
			if (data) {
				for (let key in data) {
					params.append(key, data[key]);
				}
			}
			return this.$axios.post(UPLOAD_FILE, params, {
				onUploadProgress: ({lengthComputable, loaded, total}) => {
					if (lengthComputable) {
						let percent = Math.floor(loaded / total * 100);

						percent = percent > 1 ? percent - 1 : percent;
						onProgress({percent});
					}
				},
				headers: {'Content-Type': 'multipart/form-data'}
			});
		},
		beforeAvatarUpload(file) {
			var msg = file.name.substring(file.name.lastIndexOf('.') + 1);
			let msgArr = ['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG'];
			const isLt2M = file.size / 1024 / 1024 < 100;

			if (msgArr.indexOf(msg) < 0) {
				this.$message({
					message: '上传文件只能是.jpg,.jpeg,.png,.JPG,.JPEG,.PNG格式!',
					type: 'warning'
				});
				return false;
			}
			if (!isLt2M) {
				this.$message({
					message: '上传文件大小不能超过 100MB!',
					type: 'warning'
				});
				return false;
			}
		},
		// 上传图片
		handleUploadSuccess(response) {
			this.ruleForm.businessUrl = response.url;
		},
		handleUploadError({message}) {
			this.$message.error(message || '营业执照上传失败');
		},
		handleUploadPreview(file) {
			this.dialogImageUrl = file.url;
			this.previewVisible = true;
		},
		handleUploadRemove() {
			this.ruleForm.businessUrl = '';
		},
		handleCertificationSuccess(data) {
			storage.setItem('user_info', data);

			this.showDialog({
				type: 'success',
				title: '认证成功',
				message: '您提交的注册信息已经通过审核',
				buttonText: '确认',
				onButtonClick: () => {
					this.$router.push({name: 'index'});
				}
			});
		},
		handleCertificationError(code, message) {
			if (code == 11000) {
				this.showDialog({
					type: 'error',
					title: '认证失败',
					message: '信息填写不完整',
					buttonText: '确认',
					errorTip: '如有无法解决的问题，请联系管理员进行处理,联系电话为:400-900-9088'
				});
			} else if (code == 30001) {
				this.showDialog({
					type: 'error',
					title: '认证失败',
					message: '图片过大',
					buttonText: '确认'
				});
			} else if (code == 200) {
				this.showDialog({
					type: 'error',
					title: '认证失败',
					message: '该企业已经被其它企业认证',
					buttonText: '返回首页',
					errorTip: '如有无法解决的问题，请联系管理员进行处理,联系电话为:400-900-9088',
				});
			} else {
				this.showDialog({
					type: 'error',
					title: '认证失败',
					message: message || '未知错误',
					buttonText: '确认',
					errorTip: '如有无法解决的问题，请联系管理员进行处理,联系电话为:400-900-9088',
				});
			}
		},
		handleSubmitClick() {
			this.$refs['certification-form'].validate(valid => {
				if (valid) {
					// 验证通过发送到后端比对信息,成功则认证成功，对比失败则重新认证
					this.submiting = true;
					this.$axios.post(CERTIFICATION, {
						name: this.ruleForm.name,
						unified_credit_code: this.ruleForm.creditCode,
						legal_represent: this.ruleForm.representative,
						business_license_url: this.ruleForm.businessUrl
					})
						.then(() => this.$axios.get(FETCH_USER_INFO))
						.then((data = {}) => {
							this.submiting = false;
							this.handleCertificationSuccess(data);
						})
						.catch(({code, message}) => {
							this.submiting = false;
							this.handleCertificationError(code, message);
						});
				}
			});
		},
		showDialog(dialog) {
			this.dialog = {
				...dialog,
				visible: true
			};
		},
		dismissDialog() {
			this.dialog.visible = false;
		},
		toHome() {
			this.$router.push({name: 'index'});
		}
	}
};
</script>
<style lang="less" scope>
@import "../../assets/css/common_avairail.less";
.container {
  width: 100%;
  .form-container {
    width: 580px;
    margin: auto;
    margin-top: 60px;
    margin-bottom: 40px;
    .el-input__inner {
      height: 49px;
      border-radius: 0;
    }
  }
  .image-uploader {
    .el-upload--picture-card {
      width: 80px;
      height: 80px;
      border: 1px dashed #d9d9d9;
      border-radius: 6px;
      cursor: pointer;
      position: relative;
      overflow: hidden;
    }
    .el-upload:hover {
      border-color: @primaryColor;
    }
  }
  .btn-container {
    .el-form-item__content {
      .el-button--default {
        width: 45%;
        height: 55px;
        border-radius: 0;
        border: 1px solid @primaryColor;
        font-size: 23px;
        color:@primaryColor;
      }
      .el-button--primary {
        width: 45%;
        height: 55px;
        border-radius: 0;
        background: @primaryColor;
        font-size: 23px;
        font-weight: Bold;
        font-family: Microsoft YaHei;
      }
    }
  }
  .license-tip {
    line-height: 25px;
  }
}
</style>

