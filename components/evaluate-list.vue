<template>
	<div class="agentList-Contanier">
		<div
			class="evaluation-empty-box"
			v-if="agentList && agentList.length == 0"
		>
			<img src="~assets/images/personal_background_photo.png">
			<p>暂无数据</p>
		</div>
		<div
			v-for="(item, index) in agentList"
			:key="index"
		>
			<div
				class="evaluate-list-container"
				:class="{'evaluate-list-container-list': isShowShadow}"
			>
				<div class="image-box">
					<img
						class="image"
						:src="item.file_url"
					>
				</div>
				<div class="text-box">
					<div class="name ">
						<p><span
							@click="handleToDetail(item.enc_id)"
							class="organ-name"
						>{{item.agent_name}}</span> <span class="type">{{item.agent_type_name}}</span></p>
						<p
							@click="handleEvaluta(item.id)"
							style="color: #036DB4; font-size:16px; display: flex; align-items: center;"
						>
							<img
								class="icon-edit"
								src="~assets/images/agent-icon-edit.png"
							>
							评价
						</p>
					</div>
					<p class="content line"><span class="content-title">服务事项：</span>{{item.service_item}}</p>
					<p class="content line"><span class="content-title">机构地址：</span>{{item.province_name == item.city_name ? '' : item.province_name}}{{item.city_name}}{{item.district_name}}{{item.address}}</p>
					<div class="bottom-tip line">
						<p>
							<span class="tip-title">联系人：</span>
							<span>{{item.contact_name}}</span>
						</p>
						<p>
							<span class="tip-title">联系电话：</span>
							<span>{{item.contact_phone}}</span>
						</p>
						<p>
							<span>综合评价：</span>
							<el-rate
								style="display: inline-block"
								v-model="item.composite_stars"
								disabled
								text-color="#ff9900"
								score-template="{value}"
							>
							</el-rate>
						</p>
						<p>
							<span>部门评价：</span>
							<el-rate
								style="display: inline-block"
								v-model="item.department_stars"
								disabled
								text-color="#ff9900"
								score-template="{value}"
							>
							</el-rate>
						</p>
					</div>
				</div>
			</div>
			<el-divider v-if="isShowDivider"></el-divider>
		</div>

		<el-dialog
			title="信息提示"
			:visible.sync="uncertifiedVisible"
			width="30%"
			:before-close="handleClose"
		>
			<div class="content-box">
				<div>
					<img
						class="icon_message_tips"
						src="~assets/images/icon_message_tips.png"
					>
				</div>
				<div>
					<p class="black">进行了企业认证的用户才能进行评价</p>
					<p>如果需要进行评价，请先进行企业认证</p>
				</div>
			</div>
			<span
				slot="footer"
				class="dialog-footer"
			>
				<nuxt-link to="/certification">
					<el-button
						type="primary"
					>去认证
					</el-button>
				</nuxt-link>
			</span>
		</el-dialog>
		<el-dialog
			title="用户评价"
			:visible.sync="evaluateFormVisible"
			class="evaluateForm"
		>
			<el-form
				:model="evaluateForm"
				ref="evaluateForm"
				:rules="rules"
			>
				<el-form-item
					label="综合评价"
					prop="stars"
					:label-width="formLabelWidth"
				>
					<el-rate v-model="evaluateForm.stars"></el-rate>
				</el-form-item>
				<el-form-item
					label="评价内容"
					prop="content"
					:label-width="formLabelWidth"
				>
					<el-input
						v-model="evaluateForm.content"
						type="textarea"
						autocomplete="off"
						show-word-limit
						maxlength="300"
						rows="8"
						placeholder="请输入评价内容"
					></el-input>
				</el-form-item>
				<el-form-item
					prop="captcha"
					:label-width="formLabelWidth"
				>
					<el-row :gutter="50">
						<el-col :span="16">
							<el-input
								v-model="evaluateForm.captcha"
								placeholder="请输入验证码"
							></el-input>
						</el-col>
						<el-col :span="4">
							<img
								class="capthaPicture"
								:src="capthaPicture.img"
								@click="handleChangeImg"
							>
						</el-col>
					</el-row>
				</el-form-item>
			</el-form>
			<div
				slot="footer"
				class="dialog-footer"
			>
				<el-button @click="handleCancleClick('evaluateForm')">取 消</el-button>
				<el-button
					type="primary"
					@click="submiteEvaluta('evaluateForm')"
				>提 交</el-button>
			</div>
		</el-dialog>

	</div>
</template>
<script>
import storage from '@/utils/storage';
import {
	AGENT_CMMENT,
	CHECK_CAPTCHA_CODE,
	FETCH_CAPTCHACODE
} from '@/utils/urls.js';
export default {
	props: {
		isShowShadow: {
			type: Boolean,
			default: false,
		},
		isShowDivider: {
			type: Boolean,
			default: true,
		},
		agentList: {
			type: Array,
			default: function () {
				return [];
			}
		},
		capthaPicture: {
			type: Object,
			default: function () {
				return {};
			}
		}
	},
	data() {
		return {
			value: 3.7,
			uncertifiedVisible: false,
			evaluateFormVisible: false,
			formLabelWidth: '150px',
			evaluateForm: {
				agent_id: 0,
				stars: 0,
				content: '',
				key: '',
				captcha: ''
			},
			complaintForm: {},
			rules: {
				stars: [
					{required: true, message: '请评价星级', trigger: 'blur'}
				],
				content: [
					{required: true, message: '请输入评价内容', trigger: 'blur'}
				],
				captcha: [
					{required: true, message: '请输入图形验证码', trigger: 'blur'},
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
				key: this.evaluateForm.key
			})
				.then(() => {
					callback();
				}).catch((message) => {
					callback(new Error(message || '验证码错误!'));
				});
		},

		// 请求图片验证码
		fetchPictureCode() {
			this.$axios.get(FETCH_CAPTCHACODE)
				.then(({img, key}) => {
					this.capthaPicture.img = img;
					this.capthaPicture.key = key;
				});
		},

		handleChangeImg() {
			this.fetchPictureCode();
		},
		// 评价登录
		handleEvaluta(id) {
			// this.$message.error('请先进行企业认证'); this.$router.push({name: 'certification'});
			let user_info = storage.getItem('user_info');
			let _token = storage.getItem('token');

			if (!_token) {
				this.tencentLogin();
			} else {
				if (user_info && user_info.enterprise && user_info.enterprise.length > 0) {
					this.evaluateFormVisible = true;
					this.evaluateForm.agent_id = id;
				} else if (user_info && !user_info.enterprise.length) {
					this.uncertifiedVisible = true;
				} else if (!user_info) {
					storage.setItem('agentRoute', this.$route.path);
					// this.$router.push('/login');
					this.$message.error('请先进行企业认证');
					this.$router.push({name: 'certification'});
				}
			}
		},

		// 提交评价表单
		submiteEvaluta(formName) {
			this.$refs[formName].validate(valid => {
				if (valid) {
					this.evaluateForm.key = this.capthaPicture.key;
					this.$axios.post(AGENT_CMMENT, this.evaluateForm)
						.then(() => {
							this.$message.success('评论成功！');
							this.handleCancleClick(formName);
							this.fetchPictureCode();
						})
						.catch((data) => {
							this.$message.error(data.message);
						});
				} else {
					console.log('error submit!!');
					return false;
				}
			});
		},
		// 点击取消操作
		handleCancleClick(formName) {
			this.evaluateFormVisible = false;
			this.$refs[formName].resetFields();
		},
		handleClose() {
			this.uncertifiedVisible = false;
		},
		handleToDetail(id) {
			const {href} = this.$router.resolve({
				path: '/agent/organ_detail',
				query: {
					id: id,
				}
			});

			window.open(href, '_blank');
		},
	}
};
</script>
<style lang="less">
@import "~assets/css/common_avairail.less";
.agentList-Contanier {
  margin-top: 20px;
}
.evaluation-empty-box {
  background: #ffffff;
  min-height: 584px;
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
  margin-top: 20px;
  p {
    padding: 20px;
  }
}
.evaluate-list-container {
  background: #ffffff;
  display: flex;
  justify-content: flex-start;
}
.evaluate-list-container-list{
	padding: 20px;
	box-shadow:0px 0px 5px rgba(0,0,0,0.05);
}
.image-box {
  width: 141px;
  height: 100%;
  margin-right: 10px;
}
.text-box {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}
.image {
width: 141px;
height: 141px;
}
.name {
  font-weight:bold;
  margin-right: 10px;
  display: flex;
  justify-content: space-between;
}
.type {
  display: inline-block;
  width:63px;
  height:23px;
  font-size:12px;
  background:rgba(3,109,180,1);
  color: #ffffff;
  text-align: center;
  line-height: 23px;
  border-radius:4px;
}
.content {
  font-size:14px;
  color:rgba(129,129,129,1);
  .content-title {
    color: #3B3B3B;
    font-weight: Bold;
  }
}
.bottom-tip {
  display: flex;
  justify-content: space-between;
  color:rgba(129,129,129,1);
  font-size:14px;
}
.tip-title {
 color: #3B3B3B;
}
.evaluate-list-container {
  margin-top: 20px;
}
.icon-edit {
  width: 15px;
  height: 15px;
  margin-right: 5px;
}
.content-box {
  display: flex;
  justify-content: flex-start;
}
.icon_message_tips {
  width: 40px;
  height: 40px;
  margin: 0 20px;
}
.black {
  color: #3B3B3B;
  font-size: 16px;
}
.evaluateForm {
  .el-form-item__content {
    padding-top: 10px;
  }
  .el-input__inner {
    border-radius: 0;
  }
  .el-dialog__body {
    padding: 0 20px;
  }
}
.organ-name:hover {
  color: @primaryColor;
}
.capthaPicture {
  width: 100%;
  height: 40px;
}
</style>
