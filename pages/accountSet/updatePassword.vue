<template>
	<div class="update-password-container">
		<Header/>
		<el-form
			:model="ruleForm"
			:rules="rules"
			ref="ruleForm"
			class="form-container"
		>
			<div class="title">修改密码</div>
			<p class="tip">原密码</p>
			<el-form-item prop="oldPassword">
				<el-input
					v-model.trim="ruleForm.oldPassword"
					:type="type"
					@focus="type = 'password'"
					placeholder="请输入原密码"
					clearable
					show-password
					autocomplete="off"
				></el-input>
				<img
					class="icon ic_customer"
					src="~assets/images/register_icon_,password@2x.png"
				>
			</el-form-item>
			<p class="tip">新密码</p>
			<el-form-item
				ref="password-item"
				prop="passWord"
			>
				<el-input
					v-model.trim="ruleForm.passWord"
					placeholder="设置由数字或字母组成的6-20位登录密码"
					clearable
					show-password
					autocomplete="off"
					@focus="type = 'password'"
					:type="type"
				></el-input>
				<img
					class="icon ic_customer"
					src="~assets/images/register_icon_,password@2x.png"
				>
			</el-form-item>
			<input
				type="text"
				name="txtPassword"
				style="display: none"
				value="18200490947"
			>
			<el-form-item
				ref="repassword-item"
				prop="repassWord"
			>
				<el-input
					v-model.trim="ruleForm.repassWord"
					placeholder="请重新输入密码"
					:type="type"
					@focus="type = 'password'"
					autocomplete="off"
					clearable
					show-password
				></el-input>
				<img
					class="icon ic_customer"
					src="~assets/images/register_icon_,password@2x.png"
				>
			</el-form-item>
			<el-form-item>
				<div class="btn-box">
					<div  class="next-step">
						<el-button
							@click="handleCancelClick"
						>取消</el-button>
					</div>
					<div  class="next-step">
						<el-button
							type="primary"
							@click="handleConfirmClick"
						>确认</el-button>
					</div>
				</div>
			</el-form-item>
		</el-form>
		<Footer/>
		<!-- 提示对话框 -->
		<common-dialog
			:visible.sync="dialog.visible"
			:type="dialog.type"
			:title="dialog.title"
			:message="dialog.message"
			:errorTip="dialog.errorTip"
			:buttonText="dialog.buttonText"
			:onButtonClick="dialog.onButtonClick"
		/>
	</div>
</template>
<script>
import Header from '@/components/user/header';
import Footer from '@/components/user/footer';
import CommonDialog from '@/components/common-dialog';
import storage from '@/utils/storage.js';
import {
	CHANGE_PASSWORD
} from '@/utils/urls.js';
export default {
	layout: 'empty',
	components: {
		Header,
		Footer,
		CommonDialog
	},
	data() {
		return {
			type: 'text',
			isCodeSending: false,
			pictureCodeValid: false,
			counter: 60,
			ruleForm: {
				oldPassword: '',
				passWord: '',
				repassWord: '',
			},
			rules: {
				oldPassword: [
					{required: true, message: '请输入原密码', trigger: 'blur'},
					{validator: this.checkOldPassword, trigger: ['blur', 'change']}],
				passWord: [
					{required: true, message: '请输入新密码', trigger: 'blur'},
					{validator: this.checkPassword, trigger: ['blur', 'change']},
					{validator: this.checkPasswordSame, trigger: ['blur', 'change']}],
				repassWord: [
					{required: true, message: '请再次输入新密码', trigger: 'blur'},
					{validator: this.checkRepeatPassword, trigger: ['blur', 'change']},
					{validator: this.checkPasswordSame, trigger: ['blur', 'change']}],
			},
			dialog: {
				visible: false,
			},
		};
	},
	methods: {
		// 原密码输入验证
		checkOldPassword(rule, value, callback) {
			if (!value) {
				callback(new Error('请填写原密码！'));
			} else if ((/^[a-zA-Z0-9]{6,20}$/).test(value)) {
				callback();
			} else {
				callback(new Error('原密码是由数字或字母组成的6-20位登录密码!'));
			}
		},
		// 新密码输入验证
		checkPassword(rule, value, callback) {
			if (!value) {
				callback(new Error('请填写新密码！'));
			} else if ((/^[a-zA-Z0-9]{6,20}$/).test(value)) {
				callback();
			} else {
				callback(new Error('新密码是由数字或字母组成的6-20位登录密码!'));
			}
		},
		// 确认密码输入校验
		checkRepeatPassword(rule, value, callback) {
			if (value === '') {
				callback(new Error('请再次输入密码'));
			} else if (value !== this.ruleForm.passWord) {
				callback(new Error('两次输入密码不一致!'));
			} else {
				callback();
			}
		},
		// 检查两次密码输入是否一致
		checkPasswordSame(rule, value, callback) {
			this.$refs['password-item'].clearValidate();
			this.$refs['repassword-item'].clearValidate();

			if (!this.ruleForm.passWord || !this.ruleForm.repassWord) {
				callback();
			} else if (this.ruleForm.passWord !== this.ruleForm.repassWord) {
				callback(new Error('两次密码输入不一致！'));
			} else {
				callback();
			}
		},
		// 取消更换密码
		handleCancelClick() {
			this.$router.push({name: 'index-personal-index-mine'});
		},
		// 密码重置
		handleConfirmClick() {
			this.$refs['ruleForm'].validate(valid => {
				if (!valid) {
					return false;
				}

				this.$axios.post(CHANGE_PASSWORD, {
					old_password: this.ruleForm.oldPassword,
					password: this.ruleForm.passWord,
					password_confirmation: this.ruleForm.repassWord,
				})
					.then((data) => {
						storage.setItem('token', data.token);
						this.showDialog({
							title: '密码重置成功',
							message: '',
							buttonText: '去个人中心',
							onButtonClick: () => {
								this.$router.push('/personal/mine');
							}
						});
					})
					.catch((data) => {
						console.log(data);
						this.$message.error(data.message);
					});
			});
		},
		// 显示提示弹窗
		showDialog(dialog) {
			this.dialog = {
				...dialog,
				visible: true
			};
		},
	},
};
</script>
<style lang="less" scope>
@import "~assets/css/common_avairail.less";
.update-password-container {
  width: 100%;
  .title {
    text-align: center;
    margin-bottom: 20px;
    font-size: 30px;
    font-weight: 500;
  }
  .tip {
    margin-bottom: 10px;
  }
  .form-container {
    width: 440px;
    min-height: 500px;
    margin: auto;
    margin-top: 60px;
    margin-bottom: 100px;
    .el-input__inner {
      height: 50px;
      border-radius: 0;
      padding-left: 50px;
    }
    .el-button--default {
      height: 55px;
      border-radius: 0;
      font-size: 24px;
      font-weight: Bold;
      font-family: Microsoft YaHei;
    }
    .el-button--primary{
      // width: 100%;
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
}
.btn-box {
  display: flex;
  justify-content: space-between;
}
.next-step {
  width: 40%;
  .el-button {
    width: 100%;
  }
}
</style>
