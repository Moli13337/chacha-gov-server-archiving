<template>
	<div class="industry_concer-contianer">
		<div class="explain">
			<img class="small">
			<div class="right">
				<div>
					<img
						class="icon_tips"
						src="~assets/images/butler/icon_Tips.png"
					>
				</div>
				<p>	系统会根据企业所选择的行业，进行对标行业政策申报项目的匹配与推送。</p>
			</div>
		</div>
		<div class="name-box">
			<div>
				<p class="title">我认证的企业</p>
				<p
					class="name"
					v-if="enterprise"
				>{{enterprise}}</p>
				<p
					class="name"
					v-else
				>未认证企业</p>
			</div>
			<div
				class="button"
				@click="handleIndustryEdit"
				icon="el-icon-edit"
				v-if="industryDetail.main && industryDetail.main.id"
			>修改</div>
		</div>

		<div v-if="industryDetail.main && industryDetail.main.id">
			<div class="item-box">
				<div
					class="item"
					v-if="industryDetail.main  && industryDetail.main.id"
				>
					<p class="title">企业所属主行业</p>
					<p class="tip">{{industryDetail.main | formatIndustryDesc}}</p>
				</div>
				<div
					class="item"
					v-if="industryDetail.vice  && industryDetail.vice.id"
				>
					<p class="title">企业所属副行业</p>
					<p class="tip">{{industryDetail.vice | formatIndustryDesc}}</p>
				</div>
			</div>
			<div class="tip-box">

				<div
					class="item"
				>
					<p class="title">关注的行业</p>
					<empty
						v-if="!industryDetail.follow || !industryDetail.follow.length"
						tip="暂未关注行业"
					/>
					<div
						v-else
						class="item-tip"
						v-for="(item, index) in industryDetail.follow"
						:key="index"
					>
						<p class="tip">{{item | formatIndustryDesc }}</p>
						<p
							class="cancel-button"
							@click="handleCancleConcer(item.id)"
						>取消关注</p>
					</div>
				</div>
			</div>
		</div>
		<div
			class="empty-box"
			v-else
		>
			<img
				class="empty"
				src="~assets/images/butler/empty.png"
			>
			<p class="text">您尚未关注任何行业，点击关注行业。</p>
			<el-button
				type="primary"
				@click="handleConcern"
			>去关注行业</el-button>
		</div>
		<industry-edit-dialog
			:visible.sync="industryDialogVisible"
			:industryOptions="industryOptions"
			:industryDetail="industryDetail"
			@featchIndustry="featchIndustry"
		/>
	</div>
</template>
<script>
import {
	INDUSTRY_LIST,
	FLLOW_INDUSTRY_DETAIL,
	DELETE_CONCER_INDUSTRY
} from '@/utils/urls';
import storage from '@/utils/storage';
import IndustryEditDialog from '@/components/butler/industry-edit-dialog';
import empty from '@/components/empty';

export default {
	components: {
		IndustryEditDialog,
		empty
	},
	filters: {
		formatIndustryDesc(industry) {
			let industryDesc = '';

			if (industry.first_industry_name) {
				industryDesc += industry.first_industry_name;
			}
			if (industry.second_industry_name) {
				industryDesc += '/' + industry.second_industry_name;
			}
			if (industry.third_industry_name) {
				industryDesc += '/' + industry.third_industry_name;
			}
			if (industry.fourth_industry_name) {
				industryDesc += '/' + industry.fourth_industry_name;
			}
			return industryDesc;
		}
	},
	data() {
		return {
			enterprise: '',
			industryDialogVisible: false,
			industryOptions: [],
			industryDetail: {}
		};
	},

	async asyncData({$axios}) {
		return Promise.all([
			$axios.get(INDUSTRY_LIST),
			$axios.get(FLLOW_INDUSTRY_DETAIL)
		])
			.then(([industryOptions, industryDetail]) => ({
				industryOptions: industryOptions || [],
				industryDetail: industryDetail || {},
			}))
			.catch(e => {
				console.log(e);
			});
	},
	mounted() {
		this.fetchEnterpriseInfo();
	},
	methods: {
		// 获取企业认证信息
		fetchEnterpriseInfo() {
			let userInfo = storage.getItem('user_info');

			if (userInfo.enterprise && userInfo.enterprise.length) {
				this.enterprise = userInfo.enterprise && userInfo.enterprise[0].name;
			}
		},
		handleIndustryEdit() {
			this.industryDialogVisible = true;
		},
		featchIndustry() {
			this.$axios.get(FLLOW_INDUSTRY_DETAIL).then(res => {
				this.industryDetail = res;
			}).then(error => {
				console.log(error);
			});
		},
		handleCancleConcer(id) {
			let params = {
				id
			};

			this.$axios.post(DELETE_CONCER_INDUSTRY, params).then(() => {
				this.$message.success('成功取消关注');
				this.featchIndustry();
			}).catch(error => {
				console.log(error);
			});
		},
		// 区关注
		handleConcern() {
			this.industryDialogVisible = true;
		}
	}
};
</script>
<style lang="less">
@import '~assets/css/common_avairail.less';
.industry_concer-contianer {
  padding-top: 20px;
 .explain {
      display: flex;
      flex-direction: row;
      align-items: center;
      padding: 18px 32px;
      background: @applyItemBgColor;
      border: 1px solid #bcd5e9;

      .right {
        display: flex;
        align-items: center;
        font-size:14px;
        font-family:Microsoft YaHei;
        font-weight:400;
        color:rgba(0,81,146,1);
        .icon_tips {
          width: 39px;
          height: 39px;
          margin-right: 20px;
        }
      }
    }
    .marginB {
      margin-bottom: 20px;
    }
    .name-box {
      display: flex;
      justify-content: space-between;
      padding: 20px 0;
    }
    .item-box {
      // display: flex;
      // justify-content: flex-start;
      .item {
        display: inline-block;
        margin-right: 100px;
      }
    }
    .title {
        padding: 10px 0;
      }
      .name {
        font-weight:bold;
        color:rgba(39,170,61,1);
        font-size:18px;
      }
      .button {
        width:77px;
        height:31px;
        border:1px solid rgba(0,81,146,1);
        border-radius:4px;
        font-weight:400;
        line-height:31px;
        color:rgba(0,81,146,1);
        text-align: center;
        cursor: pointer;
      }
      .cancel-button {
        width:92px;
        height:31px;
        background:rgba(255,255,255,1);
        border:1px solid rgba(203,203,203,1);
        border-radius:2px;
        text-align: center;
        line-height: 31px;
        margin-left: 20px;
        cursor: pointer;
      }
      .tip {
        // width:239px;
        height:31px;
        background:rgba(248,251,255,1);
        border:1px solid rgba(209,227,241,1);
        font-size:13px;
        border-radius:2px;
        font-weight:bold;
        line-height: 31px;
        text-align: center;
        color:rgba(0,81,146,1);
        padding: 0 10px;
      }
      .item-tip {
        display: flex;
        margin-bottom: 10px;
      }
      .editForm {

      }
    .empty-box{
      height: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 50px 0;
      .text {
          padding: 20px 0;
        }
      .empty {
        width: 119px;
        height: 119px;
        }
    }
}
</style>
