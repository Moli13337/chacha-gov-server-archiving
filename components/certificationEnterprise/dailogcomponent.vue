<template>
	<el-dialog
		:visible.sync="ownerVisible"
		:before-close="handleClose"
		class="success-dailog"
		width="300px"
		@close="$emit('update:visible', false)"
	>
		<img
			:src="iconUrl"
			class="icon"
		>
		<p class="type-tip">{{title}}</p>
		<p class="common-tip">{{message}}</p>
		<el-button
			type="primary"
			@click="handleButton"
		>{{buttonText}}</el-button>
		<p
			class="erro-tip"
			v-if="errorTip"
		>{{errorTip}}</p>
	</el-dialog>
</template>
<script>
import successIcon from '@/assets/images/ic_sucess@2x.png';
import errorIcon from '@/assets/images/ic_erro@2x.png';
import subiteIcon from '@/assets/images/icon-submit-success.jpg';

export default {
	props: {
		visible: {
			type: Boolean,
			default: false
		},
		type: {
			type: String,
			default: 'success'
		},
		title: {
			type: String,
			default: ''
		},
		message: {
			type: String,
			default: ''
		},
		errorTip: {
			type: String,
			default: ''
		},
		buttonText: {
			type: String,
			default: ''
		}
	},
	data() {
		return {
			ownerVisible: this.visible
		};
	},
	computed: {
		iconUrl() {
			switch (this.type) {
				case 'success':
					return successIcon;
				case 'error':
					return errorIcon;
				case 'submit':
					return subiteIcon;
				default:
					return successIcon;
			}
		}
	},
	methods: {
		handleClose() {
			// this.visible = false;
			this.handleButton();
		},
		handleButton() {
			if (this.buttonText == '确认') {
				this.visible = false;
			} else if (this.buttonText == '重新上传') {
				this.$router.go(-1);
			} else if (this.buttonText == '马上登录') {
				// this.$router.push({name: 'login'});
				this.tencentLogin();
			} else if (this.buttonText == '去个人中心') {
				this.$router.push({name: 'index-personal-index-mine'});
			} else {
				this.$router.push({name: 'index'});
			}
		}
	},
	watch: {
		visible(value) {
			this.ownerVisible = value;
		}
	},
	mounted() {
		console.log(this.success);
	},
};
</script>
<style lang="less" scoped>
@import '~assets/css/common_avairail.less';
.success-dailog {
  .el-dialog {
    width:428px;
    height:347px;
    background:rgba(255,255,255,1);
    border-radius:4px;
    .el-dialog__body {
      padding: 40px;
      display: flex;
      .icon {
        width:94px;
        height:98px;
        margin-left: 33%;
      }
      .type-tip {
        text-align: center;
        font-size: 24px;
        font-weight: Regular;
        color: #676767;
        margin-top: 10px;

      }
      .common-tip {
        color: #9E9E9E;
        text-align: center;
      }
      .el-button--primary {
        width: 100%;
        margin-top: 15px;
      }
      .erro-tip {
        color: @tipsColor;
         text-align: center;
         margin-top: 10px;
      }
    }


  }
}
</style>
