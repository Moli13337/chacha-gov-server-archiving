<template>
	<div>
		<el-dialog
			title="信息提示"
			width="30%"
			:visible.sync="innerVisible"
			:before-close="handleClose"
		>
			<div>
				<p>您已经成功报名本次活动</p>
				<p>感谢您的参与，请如期参与活动。</p>
			</div>
			<span
				slot="footer"
				class="dialog-footer"
			>
				<el-button
					type="primary"
					@click="handleSubmiteRegist"
				>确 定</el-button>
			</span>
		</el-dialog>
	</div>
</template>
<script>
import {

} from '@/utils/urls.js';
export default {
	props: {
		visible: {
			type: Boolean,
			default: false
		},
	},
	data() {
		return {
			innerVisible: this.visible,
		};
	},
	methods: {
		handleClose() {
			this.innerVisible = false;
			this.$emit('update:visible', false);
			// this.$router.push(this.$route.path);
			// this.$emit('changeRegistVisible', false);
			// this.$emit('updateActivityList');
		},
		handleSubmiteRegist() {
			this.handleClose();
		},
		handleCancle() {
			this.handleClose();
		}
	},
	watch: {
		visible(val) {
			this.innerVisible = val;
		}
	}
};
</script>
<style lang="less" scoped>

</style>
