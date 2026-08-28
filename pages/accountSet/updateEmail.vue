<template>
	<div class="update-password-container">
		<Header/>
		<el-form
			:model="ruleForm"
			:rules="rules"
			ref="ruleForm"
			class="form-container"
		>
			<div class="title">绑定邮箱</div>
			<p class="tip">绑定邮箱可以用于登录帐号</p>
			<el-form-item prop="email">
				<el-input
					v-model.trim="ruleForm.email"
					placeholder="请输入有效邮箱"
					clearable
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
							@click="handlecancel('ruleForm')"
						>取消</el-button>
					</div>
					<div  class="next-step">
						<el-button
							type="primary"
							@click="update('ruleForm')"
						>确认</el-button>
					</div>
				</div>
			</el-form-item>
		</el-form>
		<Footer/>
		<!-- 认证成功对话框 -->
		<dailog-component
			:visible="this.dialogVisible"
			title="邮箱绑定成功"
			message="请前往当前邮箱激活,以便后续使用"
			buttonText="去个人中心"
		></dailog-component>
	</div>
</template>
<script>
import Header from '@/components/user/header';
import Footer from '@/components/user/footer';
import DailogComponent from '@/components/certificationEnterprise/dailogcomponent';
import {
	CHANGE_EMAIL
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
				email: '',
			},
			rules: {
				email: [
					{required: true, message: '请输入有效邮箱', trigger: 'blur'},
					{validator: this.checkEmail, trigger: 'blur'}],
			}
		};
	},
	methods: {
		// 去两端空格
		trim(value) {
			return value.replace(/(^\s*)|(\s*$)/g, '');
		},

		//  原密码验证
		checkEmail(rule, val, callback) {
			const value = this.trim(val);

			const reg = /^[A-Za-z0-9\u4e00-\u9fa5]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/;

			if (!value) {
				return callback(new Error('请填写有效邮箱地址！'));
			} else if (reg.test(value)) {
				callback();
			} else {
				return callback(new Error('邮箱地址格式不正确!'));
			}
		},
		handlecancel() {
			this.$router.push({name: 'index-personal-index-mine'});
		},
		// 密码重置
		update(formName) {
			this.$refs[formName].validate(valid => {
				if (valid) {
					this.$axios.post(CHANGE_EMAIL, {
						email: this.ruleForm.email
					})
						.then(() => {
							this.dialogVisible = true;
							this.$message.success('绑定成功');
							// this.$router.push('/');
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
    margin-top: 100px;
    // margin-bottom: 100px;
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
