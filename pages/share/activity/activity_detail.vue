<template>
	<div class="activity-detail-container">
		<!-- 面包屑 -->
		<div class="breadcrumb-row">
			<el-divider direction="vertical"></el-divider>
			当前位置：
			<el-breadcrumb separator-class="el-icon-arrow-right">
				<el-breadcrumb-item>
					<nuxt-link to="/">首页</nuxt-link>
				</el-breadcrumb-item>
				<el-breadcrumb-item>
					<nuxt-link to="/share">活动学习</nuxt-link>
				</el-breadcrumb-item>
				<el-breadcrumb-item>
					<div>活动详情</div>
				</el-breadcrumb-item>
			</el-breadcrumb>
		</div>
		<div class="detail-contianer">
			<div>
				<img
					class="detail-image"
					:src="activityDetail.file_url"
				>
			</div>
			<div class="detail-text">
				<div class="organ-name-box"><p class="organ-name">{{activityDetail.title}}</p>
				</div>
				<div class="tag-box">
					<el-tag type="success">{{activityDetail.status_name}}</el-tag>
					<el-tag color="#ffffff">{{activityDetail.type_name}}</el-tag>
					<el-tag color="#ffffff">{{activityDetail.number}}人</el-tag>
				</div>
				<div class="tip-box"><span class="title">活动时间：</span>{{activityDetail.validity_sdate |formatDate('YYYY年MM月DD日 HH:mm')}} ~ {{activityDetail.validity_edate | formatDate('YYYY年MM月DD日 HH:mm')}}</div>
				<div class="tip-box"><span class="title">活动地点：</span><span>{{activityDetail.province_name == activityDetail.city_name ? '' : activityDetail.province_name}}{{activityDetail.city_name}}{{activityDetail.district_name}}{{activityDetail.address}}</span></div>
				<div class="tip-box"><span class="title">活动主办方：</span><span>{{activityDetail.sponsor}}
				</span></div>
				<div class="tip-box"><span class="title">联系电话：</span><span>{{activityDetail.mobile}}
				</span></div>
				<div class="tip-box"><el-button
					type="primary"
					@click="handleRgist"
					v-if="activityDetail.status == 1"
				>我要报名</el-button></div>
			</div>
		</div>
		<div class="detail-contianer-tab">
			<div class="tab-content">
				<p class="item-title">活动内容详情</p>
				<div
					class="tab-text"
					v-html="activityDetail.content"
				>
					<p v-if="!activityDetail.content">无</p>
				</div>
			</div>
		</div>
		<activityDialog
			:activityRgistVisible="activityRgistVisible"
			@changeRegistVisible="changeRegistVisible"
		/>
	</div>
</template>
<script>
import {
	ACTIVITY_DETAIL,
	ACTIVITY_SUBMIT
} from '@/utils/urls.js';

import activityDialog from '@/components/share/activity_regist';
export default {
	components: {
		activityDialog
	},
	data() {
		return {
			activityRgistVisible: false,
			activityDetail: {},
			registId: 0
		};
	},
	methods: {

		// 表格样式
		headerStyle({row, rowIndex}) {
			if (rowIndex == 0) {
				return 'headerStyle';
			}
		},

		// 获取机构详情
		fetchActivityDetail(id, keyword) {
			let params = {};

			if (id) {
				params.id = id;
			}
			if (keyword) {
				params.keyword = keyword;
			}

			this.$axios(ACTIVITY_DETAIL, {params: params}).then(res => {
				this.activityDetail = res || [];
			}).catch(error => {
				console.log(error);
			});
		},

		changeRegistVisible(val) {
			this.activityRgistVisible = val;
		},

		// 立即报名
		handleRgist() {
			this.registId = this.$route.query.id;
			this.handleSubmiteRegist();
		},
		// 立即报名
		handleSubmiteRegist() {
			let params = {
				id: this.registId,
			};

			this.$axios.post(ACTIVITY_SUBMIT, params).then(() => {
				this.activityRgistVisible = true;
			}).catch(error => {
				this.$message.error(error.message);
			});
			this.$emit('changeRegistVisible', false);
		},

	},

	mounted() {
		let id = this.$route.query.id;

		this.fetchActivityDetail(id);
	},
};
</script>
<style lang="less">
@import "~assets/css/common_avairail";
  .activity-detail-container {
    .detail-contianer {
      border:1px solid #DCDFE6;
      background: #ffffff;
      padding: 20px;
      display: flex;
      justify-content: space-between;
      .detail-image {
        width: 391px;
        height: 280px;
      }
      .detail-text {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding-left: 20px;
        .organ-name-box {
          display: flex;
          justify-content: space-between;
          .organ-name {
             font-size:31px;
          font-weight:bold;
          color:rgba(59,59,59,1);
          }
          .complaint-tip-box {
            display: flex;
            align-items: center;
          }
          .icon_complaint {
            width: 14px;
            height: 14px;
            margin-right: 10px;
          }
        }
        .title {
          color:rgba(59,59,59,1);
          font-weight:bold;
        }
        .tag-box {
           padding: 10px 0;
        .el-tag {
          border-radius: 0;
        }
        }
        .el-button {
          border-radius: 0;
        }

      }
    }

    .detail-contianer-tab {
      background: #ffffff;
      margin-top: 20px;
    .el-tabs--border-card {
      box-shadow: none;
    }
    .el-divider--vertical {
      width: 6px;
      height: 27px;
      background: @primaryColor;
    }
    .tab-text {
      color: #818181;
      padding: 20px;
      table {
        border: 1px solid @borderLine;
        border-collapse:collapse;
        margin-bottom: 20px;

        td {
           border: 1px solid @borderLine;
        }
      }
    }
    .item-title {
      font-size: 18px;
      color: #3B3B3B;
      width: 100%;
      height:48px;
      line-height: 48px;
      background:rgba(249,251,252,1);
      border:1px solid rgba(220,223,230,1);
      padding-left: 20px;
    }
  }
  }

</style>
