<template>
	<div class="evaluateDialog">
		<el-dialog
			title="信息提示"
			:visible.sync="uncertifiedVisible"
			width="30%"
			@closed="handleClose"
		>
			<div class="content-box">
				<div>
					<img
						class="icon_message_tips"
						src="~assets/images/icon_message_tips.png"
					/>
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
					<el-button type="primary">去认证</el-button>
				</nuxt-link>
			</span>
		</el-dialog>
		<el-dialog
			title="用户评价"
			:visible.sync="currentevaluateVisible"
			class="evaluateForm"
			@closed="handleEvaluateFormClose"
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
								@click="handleCapthaPicture"
							/>
						</el-col>
					</el-row>
				</el-form-item>
			</el-form>
			<div
				slot="footer"
				class="dialog-footer"
			>
				<el-button @click="handleCancleClick">取 消</el-button>
				<el-button
					type="primary"
					@click="submiteEvaluta('evaluateForm')"
				>提 交</el-button>
			</div>
		</el-dialog>
	</div>
</template>
<script>
import {
	AGENT_CMMENT
} from '@/utils/urls.js';

export default {
	props: {
		uncertifiedVisible: {
			type: Boolean,
			default: false
		},
		evaluateFormVisible: {
			type: Boolean,
			default: false
		},
		agent_id: {
			type: [String, Number],
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
			currentevaluateVisible: this.evaluateFormVisible,
			evaluateForm: {
				agent_id: this.agent_id,
				stars: 0,
				content: '',
				key: '',
				captcha: ''
			},
			formLabelWidth: '150px',
			rules: {
				stars: [{required: true, message: '请评价星级', trigger: 'blur'}],
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
		// 监听表单关闭
		handleClose() {
			this.uncertifiedVisible = false;
		},
		handleEvaluateFormClose() {
			this.handleCancleClick();
		},
		// 点击取消操作
		handleCancleClick() {
			this.currentevaluateVisible = false;
			this.$emit('changeEvaluateFormVisible', false);
			this.$refs['evaluateForm'].resetFields();
		},
		handleCapthaPicture() {
			this.$emit('changeCapthaPicture');
		},
		// 提交评价表单
		submiteEvaluta(formName) {
			this.$refs[formName].validate(valid => {
				if (valid) {
					this.evaluateForm.key = this.capthaPicture.key;
					this.$axios
						.post(AGENT_CMMENT, this.evaluateForm)
						.then(() => {
							this.$message.success('评论成功！');
							this.currentevaluateVisible = false;
							this.$emit('changeEvaluateFormVisible', false);
							this.$emit('changeCapthaPicture');
							this.$emit('updateCommentList');
							this.$refs[formName].resetFields();
						})
						.catch(data => {
							console.log(data);
							this.$message.error(data.message);
						});
				} else {
					console.log('error submit!!');
					return false;
				}
			});
		}
	},
	watch: {
		agent_id(val) {
			this.evaluateForm.agent_id = val;
		},
		evaluateFormVisible(val) {
			this.currentevaluateVisible = val;
		}
	}
};
</script>
<style lang="less">
.evaluateDialog {
  .el-col {
  padding: 0 !important;
  margin-left: 25px;
}
}
</style>
