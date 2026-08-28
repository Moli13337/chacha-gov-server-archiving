<template>
	<div class="activityList-Contanier">
		<empty v-if="!innerActivitytList || !innerActivitytList.length"/>
		<div
			v-else
			v-for="(item, index) in innerActivitytList"
			:key="index"
		>
			<div
				class="evaluate-list-container"
				:class="{'evaluate-list-container-list': isShowShadow}"
			>
				<div class="activity-image-box">
					<img
						v-if="item.file_url"
						class="image"
						:src="item.file_url"
					>
					<img
						v-else
						class="image"
						src="~assets/images/share/default_activity.png"
					>
				</div>
				<div class="text-box">
					<div class="name ">
						<p><span
							@click="handleDetailClick(item.enc_id)"
							class="organ-name"
						>活动名称：{{item.title}}</span></p>
					</div>
					<div class="tag-box">
						<el-tag type="success">{{item.status_name}}</el-tag>
						<el-tag color="#ffffff">{{item.type_name || '无活动类别'}}</el-tag>
						<el-tag color="#ffffff">{{item.number}}人</el-tag>
					</div>
					<p class="content line"><span class="content-title">活动时间：</span>{{item.validity_sdate | formatDate('YYYY年MM月DD日 HH:mm')}} ~ {{item.validity_edate | formatDate('YYYY年MM月DD日 HH:mm')}}</p>
					<p class="content line"><span class="content-title">活动地点：</span>{{item.province_name == item.city_name ? '' : item.province_name}}{{item.city_name}}{{item.district_name}}{{item.address}}</p>
					<div class="last-line content line">
						<p><span class="content-title">活动主办方：</span>{{item.sponsor}}</p>
					</div>
					<div class="content line btn">
						<div v-if="item.status != 3 && item.apply_count > 0"> <el-button
							type="primary"
						>已报名</el-button></div>
						<div
							v-else-if="item.status != 3 && item.apply_count == 0"
						> <el-button
							type="primary"
							@click="handleRegistClick(item.id)"
						>立即报名</el-button>
						</div>
						<el-tag
							v-if="item.status == 3"
							class="end-tag"
							type="info"
							color="#3B3B3B"
						>活动已结束</el-tag>
					</div>
				</div>
			</div>
			<el-divider v-if="isShowDivider"></el-divider>
		</div>
		<regist-dailog
			:visible.sync="registPromptVisible"
		/>
	</div>
</template>
<script>
import storage from '@/utils/storage';
import RegistDailog from '@/components/share/activity_regist';
import Empty from '@/components/empty';
import {
	ACTIVITY_SUBMIT
} from '@/utils/urls.js';
export default {
	components: {
		RegistDailog,
		Empty
	},
	props: {
		isShowShadow: {
			type: Boolean,
			default: false,
		},
		isShowDivider: {
			type: Boolean,
			default: true,
		},
		activityList: {
			type: Array,
			default: function () {
				return [];
			}
		},
	},
	data() {
		return {
			registPromptVisible: false,
			uncertifiedVisible: false,
			evaluateFormVisible: false,
			formLabelWidth: '150px',
			registId: 0,
			innerActivitytList: this.activityList,
			isRegist: '立即报名'
		};
	},
	watch: {
		activityList(val) {
			this.innerActivitytList = val;
		}
	},
	methods: {
		// 评价登录
		handleEvaluta(id) {
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
		// 处理详情点击
		handleDetailClick(id) {
			const {href} = this.$router.resolve({
				path: '/share/activity/activity_detail',
				query: {
					id: id,
				}
			});

			window.open(href, '_blank');
		},
		// 处理报名点击
		handleRegistClick(id) {
			this.$axios.post(ACTIVITY_SUBMIT, {id})
				.then(() => {
					this.showRegistPromptDialog();
					this.notifyRegistChange(id);
				}).catch(error => {
					this.$message.error(error.message);
				});
			// this.$emit('changeRegistVisible', false);
		},
		// 显示报名提示弹窗
		showRegistPromptDialog() {
			this.registPromptVisible = true;
		},
		// 通知报名变更
		notifyRegistChange(id) {
			this.$emit('onRegistChange', id);
		}
	}
};
</script>
<style lang="less">
@import "~assets/css/common_avairail.less";
.activityList-Contanier {
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
.activity-image-box {
  width: 266px;
  height: 100%;
  margin-right: 10px;
  position: relative;
  .image {
    width: 266px;
    height: 210px;
  }
  .tip {
    position: absolute;
    top: 0px;
    left: 0px;
    width: 25px;
    background: rgba(3,109,180,1);
    color: #ffffff;
    text-align: center;
    padding-bottom: 10px;
  }
}
.text-box {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  .name {
  font-weight:bold;
  display: flex;
  align-items: center;
  font-size:18px;
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
  }
  .tag-box {
    padding: 10px 0;
    .el-tag {
      border-radius: 0;
    }
  }
}
.image {
  width: 100%;
  height: 141px;
}

.content {
  padding: 5px 0;
  .end-tag {
    color: #ffffff;
  }

}

.content {
  font-size:16px;
  color:rgba(129,129,129,1);
  .el-button {
    border-radius: 0;
  }

  .content-title {
    color: #3B3B3B;
    font-weight: Bold;
    font-size: 16px;
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
.last-line {
  display: flex;
  justify-content: space-between;
}
</style>
